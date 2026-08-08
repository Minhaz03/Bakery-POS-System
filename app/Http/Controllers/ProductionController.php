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
                'qty'                 => $actualQty,
                'scheduled_at'        => now(),
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

    public function edit(ProductionOrder $order)
    {
        if ($order->status === 'completed') {
            return redirect()->route('dashboard.production')->with('error', 'Cannot edit a completed order. If you need to make changes, delete it and create a new one.');
        }
        $recipes = \App\Models\Recipe::with('product')->where('is_active', true)->get();
        return view('dashboard.production.edit', compact('order', 'recipes'));
    }

    public function update(Request $request, ProductionOrder $order)
    {
        if ($order->status === 'completed') {
            return redirect()->route('dashboard.production')->with('error', 'Cannot edit a completed order.');
        }

        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'qty'       => 'required|numeric|min:0.01',
            'scheduled_at' => 'required|date',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated, $order) {
            $order->update([
                'recipe_id' => $validated['recipe_id'],
                'planned_quantity' => $validated['qty'],
                'planned_date' => $validated['scheduled_at'],
            ]);

            // Re-sync ingredients based on new qty
            $recipe = \App\Models\Recipe::with('ingredients')->find($validated['recipe_id']);
            $order->ingredients()->delete();
            foreach ($recipe->ingredients as $recipeIngredient) {
                \App\Models\ProductionOrderIngredient::create([
                    'tenant_id' => auth()->user()->tenant_id ?? 1,
                    'production_order_id' => $order->id,
                    'ingredient_id' => $recipeIngredient->product_id,
                    'required_qty' => ($recipeIngredient->quantity / $recipe->yield_qty) * $validated['qty'],
                ]);
            }
        });

        return redirect()->route('dashboard.production')->with('success', 'Production order updated successfully.');
    }

    public function destroy(ProductionOrder $order)
    {
        if ($order->status === 'completed') {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                // 1. Revert Output Product Stock
                if ($order->recipe && $order->recipe->product && $order->actual_quantity > 0) {
                    $order->recipe->product->decrement('stock_qty', $order->actual_quantity);
                    \App\Models\StockLedger::create([
                        'product_id' => $order->recipe->product_id,
                        'type'       => 'Production Reversal (-)',
                        'quantity'   => -$order->actual_quantity,
                        'user_id'    => auth()->id(),
                        'notes'      => "Reversed production order: {$order->reference_no}",
                    ]);
                }

                // 2. Revert Consumed Ingredients Stock
                foreach ($order->ingredients as $ingredient) {
                    $consumed = $ingredient->consumed_qty ?? $ingredient->required_qty;
                    $waste = $ingredient->waste_qty ?? 0;
                    $totalDeducted = $consumed + $waste;
                    if ($totalDeducted > 0 && $ingredient->ingredient) {
                        $ingredient->ingredient->increment('stock_qty', $totalDeducted);
                        \App\Models\StockLedger::create([
                            'product_id' => $ingredient->ingredient_id,
                            'type'       => 'Production Reversal (+)',
                            'quantity'   => $totalDeducted,
                            'user_id'    => auth()->id(),
                            'notes'      => "Reversed consumption for order: {$order->reference_no}",
                        ]);
                    }
                }
                
                // Delete batches
                $order->batches()->delete();
                $order->ingredients()->delete();
                $order->delete();
            });
            return redirect()->route('dashboard.production')->with('success', 'Production order deleted and inventory reversed successfully.');
        } else {
            $order->ingredients()->delete();
            $order->delete();
            return redirect()->route('dashboard.production')->with('success', 'Production order deleted successfully.');
        }
    }

    public function cancel(ProductionOrder $order)
    {
        if ($order->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel a completed order.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('dashboard.production')->with('success', 'Production order cancelled.');
    }
}
