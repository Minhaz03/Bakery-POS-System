<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderIngredient;
use App\Models\ProductionBatch;
use App\Models\Recipe;
use App\Models\StockLedger;

class ProductionController extends Controller
{
    /**
     * Display Production Orders (mapped to legacy $batches array for view compatibility).
     */
    public function production(): View
    {
        $dbOrders = ProductionOrder::with('recipe')->orderBy('created_at', 'desc')->get();

        $batches = $dbOrders->map(function ($order) {
            return [
                'real_id' => $order->id,
                'id'      => $order->reference_no,
                'recipe'  => $order->recipe ? $order->recipe->name : 'Unknown Recipe',
                'qty'     => (float) $order->planned_quantity,
                'status'  => ucwords(str_replace('-', ' ', $order->status)), // planned -> Planned, in-progress -> In Progress
                'date'    => $order->planned_date ? $order->planned_date->format('Y-m-d') : '—',
            ];
        })->toArray();

        $recipes = Recipe::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.production.index', compact('batches', 'recipes'));
    }

    /**
     * Show Production Order details.
     */
    public function show(ProductionOrder $order): View
    {
        $order->load(['recipe.ingredients.product', 'ingredients.ingredient', 'batches', 'creator']);
        return view('dashboard.production.show', compact('order'));
    }

    /**
     * Store a new Production Order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'qty'       => 'required|numeric|min:0.01',
            'scheduled_at' => 'required|date',
        ]);

        $recipe = Recipe::with('ingredients.product')->findOrFail($validated['recipe_id']);

        DB::transaction(function () use ($validated, $recipe) {
            $order = ProductionOrder::create([
                'reference_no'     => 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)),
                'recipe_id'        => $validated['recipe_id'],
                'planned_quantity' => $validated['qty'],
                'actual_quantity'  => 0,
                'planned_date'     => date('Y-m-d', strtotime($validated['scheduled_at'])),
                'status'           => 'planned',
                'produced_by'      => auth()->id(),
            ]);

            // Auto-populate ingredients from recipe
            $multiplier = $validated['qty'] / ($recipe->yield_qty ?: 1);
            foreach ($recipe->ingredients as $ingredient) {
                ProductionOrderIngredient::create([
                    'production_order_id' => $order->id,
                    'ingredient_id'       => $ingredient->product_id,
                    'required_qty'        => $ingredient->net_quantity * $multiplier,
                    'consumed_qty'        => 0,
                    'waste_qty'           => 0,
                ]);
            }
        });

        return redirect()->route('dashboard.production')->with('success', 'Production order created successfully.');
    }

    /**
     * Start a production order (change to in-progress).
     */
    public function start(ProductionOrder $order)
    {
        if ($order->status !== 'planned') {
            return redirect()->back()->with('error', 'Only planned orders can be started.');
        }

        $order->update(['status' => 'in-progress']);

        return redirect()->route('dashboard.production')->with('success', 'Production order started.');
    }

    /**
     * Complete a Production Order.
     */
    public function complete(ProductionOrder $order, Request $request)
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Cannot complete this order.');
        }

        $validated = $request->validate([
            'manufacturing_date' => 'nullable|date',
            'expiry_date'        => 'nullable|date',
            'wastage_qty'        => 'nullable|numeric|min:0',
            'wastage_notes'      => 'nullable|string',
        ]);

        $recipe = $order->recipe()->with('ingredients.product', 'product')->first();

        if (!$recipe) {
            return redirect()->back()->with('error', 'Associated recipe not found.');
        }

        DB::transaction(function () use ($order, $recipe, $validated) {
            $wasteQty   = $validated['wastage_qty'] ?? 0;
            $actualQty  = $order->planned_quantity - $wasteQty;
            $multiplier = $order->planned_quantity / ($recipe->yield_qty ?: 1);

            // Deduct raw ingredients from stock
            foreach ($recipe->ingredients as $recipeIngredient) {
                if ($recipeIngredient->product) {
                    $deductQty = $recipeIngredient->net_quantity * $multiplier;
                    $recipeIngredient->product->decrement('stock_qty', $deductQty);

                    StockLedger::create([
                        'product_id' => $recipeIngredient->product_id,
                        'type'       => 'Production Usage (-)',
                        'quantity'   => -$deductQty,
                        'user_id'    => auth()->id(),
                        'notes'      => "Used in order: {$order->reference_no}" . ($validated['wastage_notes'] ?? ''),
                    ]);
                }
            }

            // Update ingredient consumed quantities
            foreach ($order->ingredients as $ingredient) {
                $ingredient->update([
                    'consumed_qty' => $ingredient->required_qty,
                    'waste_qty'    => $wasteQty > 0
                        ? round($ingredient->required_qty * ($wasteQty / $order->planned_quantity), 3)
                        : 0,
                ]);
            }

            // Calculate cost
            $totalCost   = $order->ingredients->sum(fn($i) => $i->consumed_qty * ($i->ingredient->cost_price ?? 0));
            $costPerUnit = $actualQty > 0 ? round($totalCost / $actualQty, 4) : 0;

            // Add finished product to stock
            if ($recipe->product && $actualQty > 0) {
                $recipe->product->increment('stock_qty', $actualQty);

                StockLedger::create([
                    'product_id' => $recipe->product_id,
                    'type'       => 'Production (+)',
                    'quantity'   => $actualQty,
                    'user_id'    => auth()->id(),
                    'notes'      => "Produced from order: {$order->reference_no}" . ($wasteQty > 0 ? " (Waste: $wasteQty)" : ''),
                ]);
            }

            // Create a production batch record for this run
            ProductionBatch::create([
                'production_order_id' => $order->id,
                'batch_number'        => 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                'produced_qty'        => $actualQty,
                'manufacturing_date'  => $validated['manufacturing_date'] ?? today(),
                'expiry_date'         => $validated['expiry_date'] ?? null,
            ]);

            $order->update([
                'status'          => 'completed',
                'actual_quantity' => $actualQty,
                'produced_at'     => now(),
                'cost_per_unit'   => $costPerUnit,
                'total_cost'      => $totalCost,
            ]);
        });

        return redirect()->route('dashboard.production')->with('success', 'Production order completed and stock updated.');
    }

    /**
     * Cancel a Production Order.
     */
    public function cancel(ProductionOrder $order)
    {
        if ($order->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel a completed order.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('dashboard.production')->with('success', 'Production order cancelled.');
    }
}
