<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sale::with(['customer', 'creator'])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', fn ($sq) => $sq->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->latest('sale_date')->paginate(15);

        return view('dashboard.sales.index', compact('sales'));
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'creator', 'items.product.unit']);
        return view('dashboard.sales.show', compact('sale'));
    }

    public function print(Sale $sale): View
    {
        $sale->load(['customer', 'creator', 'items.product.unit']);
        return view('dashboard.sales.invoice', compact('sale'));
    }

    public function edit(Sale $sale): View
    {
        if ($sale->status !== 'completed') {
            return redirect()->route('dashboard.sales.show', $sale)
                ->with('error', 'Only completed sales can be edited.');
        }

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $sale->load('items.product');

        return view('dashboard.sales.edit', compact('sale', 'customers'));
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        if ($sale->status !== 'completed') {
            return redirect()->route('dashboard.sales.show', $sale)
                ->with('error', 'Only completed sales can be edited.');
        }

        $validated = $request->validate([
            'customer_id'     => 'nullable|exists:customers,id',
            'payment_method'  => 'required|in:cash,card,mobile_pay,credit',
            'discount_amount' => 'required|numeric|min:0',
            'note'            => 'nullable|string|max:1000',
        ]);

        // Recalculate grand total when discount changes
        $subtotal   = $sale->subtotal;
        $tax        = $sale->tax_amount;
        $discount   = $validated['discount_amount'];
        $grandTotal = $subtotal - $discount + $tax;

        $sale->update([
            'customer_id'     => $validated['customer_id'],
            'payment_method'  => $validated['payment_method'],
            'discount_amount' => $discount,
            'grand_total'     => $grandTotal > 0 ? $grandTotal : 0,
            'note'            => $validated['note'],
        ]);

        return redirect()->route('dashboard.sales.show', $sale)
            ->with('success', 'Sale invoice updated successfully.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        // Soft-delete only — stock reversal would require separate refund flow
        $sale->delete();

        return redirect()->route('dashboard.sales')
            ->with('success', 'Sale invoice ' . $sale->invoice_no . ' has been voided and removed.');
    }

    public function collectPayment(Request $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validate([
            'amount_collected' => 'required|numeric|min:0.01',
        ]);

        $collected = (float) $validated['amount_collected'];
        $remaining = $sale->grand_total - $sale->amount_tendered;

        if ($collected > $remaining) {
            return redirect()->back()->with('error', 'Collected amount cannot exceed the remaining due amount of ৳' . number_format($remaining, 2));
        }

        $newAmountTendered = $sale->amount_tendered + $collected;
        $status = 'completed';
        if ($newAmountTendered < $sale->grand_total) {
            $status = 'partial';
        }

        $sale->update([
            'amount_tendered' => $newAmountTendered,
            'status' => $status,
        ]);

        return redirect()->route('dashboard.sales.show', $sale)
            ->with('success', 'Payment of ৳' . number_format($collected, 2) . ' collected successfully.');
    }
}
