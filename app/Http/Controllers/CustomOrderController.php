<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomOrderController extends Controller
{
    /**
     * Display Custom Orders.
     */
    public function customOrders(): View
    {
        $dbOrders = \App\Models\CustomOrder::orderBy('delivery_date', 'asc')->get();
        $orders = $dbOrders->map(function ($order) {
            return [
                'real_id' => $order->id,
                'id' => $order->order_number,
                'customer' => $order->customer_name,
                'details' => $order->details,
                'delivery_date' => $order->delivery_date->format('Y-m-d'),
                'price' => (float) $order->total_price,
                'advance' => (float) $order->advance_payment,
                'status' => $order->status,
            ];
        })->toArray();

        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('dashboard.custom-orders.index', compact('orders', 'customers'));
    }

    /**
     * Store a new Custom Order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'details' => 'required|string',
            'delivery_date' => 'required|date',
            'total_price' => 'required|numeric|min:0',
            'advance_payment' => 'required|numeric|min:0',
        ]);

        if (!empty($validated['customer_id']) && empty($validated['customer_name'])) {
            $customer = \App\Models\Customer::find($validated['customer_id']);
            $validated['customer_name'] = $customer->name;
        }

        \App\Models\CustomOrder::create([
            'order_number' => 'CO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'customer_id' => $validated['customer_id'] ?? null,
            'customer_name' => $validated['customer_name'],
            'details' => $validated['details'],
            'delivery_date' => $validated['delivery_date'],
            'total_price' => $validated['total_price'],
            'advance_payment' => $validated['advance_payment'],
            'status' => 'Pending',
        ]);

        return redirect()->route('dashboard.custom-orders')->with('success', 'Custom order created successfully.');
    }

    /**
     * Show Custom Order details.
     */
    public function show(\App\Models\CustomOrder $order): View
    {
        return view('dashboard.custom-orders.show', compact('order'));
    }

    /**
     * Print Custom Order Slip.
     */
    public function print(\App\Models\CustomOrder $order): View
    {
        return view('dashboard.custom-orders.print', compact('order'));
    }

    /**
     * Cancel Custom Order.
     */
    public function cancel(\App\Models\CustomOrder $order)
    {
        if ($order->status === 'Confirmed' || $order->status === 'In Progress') {
            // Depending on business logic, maybe you can't cancel or maybe you can.
            // Let's allow it for now.
        }

        $order->update(['status' => 'Cancelled']);

        return redirect()->route('dashboard.custom-orders')->with('success', 'Custom order cancelled.');
    }

    /**
     * Update Custom Order Status.
     */
    public function updateStatus(Request $request, \App\Models\CustomOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Confirmed,In Progress,Completed,Cancelled'
        ]);

        if ($validated['status'] === 'Completed' && $order->total_price > $order->advance_payment) {
            return redirect()->back()->withErrors(['status' => 'Cannot mark order as Completed until the full payment is collected.']);
        }

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    /**
     * Add payment to Custom Order.
     */
    public function addPayment(Request $request, \App\Models\CustomOrder $order)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01'
        ]);

        $newAdvance = $order->advance_payment + $validated['payment_amount'];
        if ($newAdvance > $order->total_price) {
            return redirect()->back()->withErrors(['payment_amount' => 'Total payment cannot exceed the total cost of the order.']);
        }

        $order->update(['advance_payment' => $newAdvance]);

        return redirect()->back()->with('success', 'Payment added successfully.');
    }
}
