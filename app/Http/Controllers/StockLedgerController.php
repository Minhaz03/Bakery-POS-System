<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StockLedgerController extends Controller
{
    /**
     * Display Stock Ledger.
     */
    public function stockLedger(Request $request): View
    {
        $query = StockLedger::with(['product', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $ledger = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('stockLedger.stock-ledger', compact('ledger', 'products'));
    }

    /**
     * Store a new stock adjustment.
     */
    public function adjustStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|string|in:Adjustment (+),Adjustment (-),Wastage (-),Production (+),Stock Audit (Adj)',
            'qty' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = $validated['qty'];
        $type = $validated['type'];

        // Determine signed quantity based on type and direction
        if ($type === 'Adjustment (+)' || $type === 'Production (+)') {
            $signedQty = $qty;
        } elseif ($type === 'Adjustment (-)' || $type === 'Wastage (-)') {
            $signedQty = -$qty;
        } else { // 'Stock Audit (Adj)'
            $direction = $request->input('direction', 'add');
            $signedQty = ($direction === 'add') ? $qty : -$qty;
        }

        // Update product stock_qty
        $newStock = $product->stock_qty + $signedQty;
        $product->update(['stock_qty' => $newStock]);

        // Create StockAdjustment
        $adjustment = \App\Models\StockAdjustment::create([
            'product_id' => $product->id,
            'quantity' => abs($signedQty),
            'type' => $signedQty >= 0 ? 'addition' : 'deduction',
            'reason' => $validated['notes'],
            'user_id' => auth()->id(),
        ]);

        // Log to stock ledger
        StockLedger::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $signedQty,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'],
            'reference_id' => $adjustment->id,
            'reference_type' => \App\Models\StockAdjustment::class,
        ]);

        return redirect()->back()->with('success', 'Stock adjusted successfully! Product: ' . $product->name . ', New Stock: ' . number_format($newStock, 2));
    }

    /**
     * Export Stock Ledger to Excel (CSV).
     */
    public function exportExcel()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock_ledger_' . date('Y-m-d_H-i-s') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($file, ['Ref ID', 'Product SKU', 'Product Name', 'Movement Type', 'Quantity', 'Updated By', 'Date & Time', 'Notes']);

            // Fetch ledger entries
            $entries = StockLedger::with(['product', 'user'])->orderBy('id', 'desc')->get();

            foreach ($entries as $row) {
                fputcsv($file, [
                    'TXN-' . $row->id,
                    $row->product?->sku ?? 'N/A',
                    $row->product?->name ?? 'N/A',
                    $row->type,
                    ($row->quantity > 0 ? '+' : '') . number_format($row->quantity, 2),
                    $row->user?->name ?? 'System/Unknown',
                    $row->created_at->format('Y-m-d h:i A'),
                    $row->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
