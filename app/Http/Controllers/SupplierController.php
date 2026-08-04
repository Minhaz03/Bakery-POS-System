<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%');
            });
        }

        $suppliers = $query->orderBy('name')->paginate(15);
        return view('dashboard.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('dashboard.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['current_balance'] = $validated['opening_balance'];

        Supplier::create($validated);

        return redirect()->route('dashboard.suppliers')->with('success', 'Supplier added successfully!');
    }

    public function edit(Supplier $supplier): View
    {
        return view('dashboard.suppliers.edit', compact('supplier'));
    }

    public function show(Supplier $supplier): View
    {
        // Eager load purchases and payments, ordered by latest date
        $supplier->load(['purchases' => function ($query) {
            $query->orderBy('purchase_date', 'desc');
        }, 'payments' => function ($query) {
            $query->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc');
        }]);

        return view('dashboard.suppliers.show', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'opening_balance' => 'required|numeric',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        // Adjust current balance based on difference in opening balance
        $balanceDiff = $validated['opening_balance'] - $supplier->opening_balance;
        $validated['current_balance'] = $supplier->current_balance + $balanceDiff;

        $supplier->update($validated);

        return redirect()->route('dashboard.suppliers')->with('success', 'Supplier updated successfully!');
    }

    public function payBalance(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($supplier, $amount, $validated) {
            // Create payment record
            $supplier->payments()->create([
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'description' => $validated['description'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $supplier->decrement('current_balance', $amount);

            // Auto-apply payment to oldest unpaid purchases
            $unpaidPurchases = $supplier->purchases()
                ->where('amount_due', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->get();

            $remainingAmount = $amount;

            foreach ($unpaidPurchases as $purchase) {
                if ($remainingAmount <= 0) break;

                $due = $purchase->amount_due;
                $pay = min($due, $remainingAmount);

                $newPaid = $purchase->amount_paid + $pay;
                $newDue  = $purchase->grand_total - $newPaid;

                $purchase->update([
                    'amount_paid' => $newPaid,
                    'amount_due'  => $newDue,
                    'payment_status' => $newDue <= 0 ? 'paid' : 'partial',
                ]);

                $remainingAmount -= $pay;
            }
        });

        return redirect()->route('dashboard.suppliers.show', $supplier)
            ->with('success', 'Payment of ৳' . number_format($amount, 2) . ' recorded successfully.');
    }

    public function updatePayment(Request $request, SupplierPayment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $newAmount = (float) $validated['amount'];
        $oldAmount = $payment->amount;
        $diff = $newAmount - $oldAmount;
        $supplier = $payment->supplier;

        DB::transaction(function () use ($payment, $supplier, $diff, $validated) {
            $supplier->decrement('current_balance', $diff);

            if ($diff > 0) {
                // Applied more money: apply to oldest unpaid purchases
                $unpaidPurchases = $supplier->purchases()
                    ->where('amount_due', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->get();
                $remainingAmount = $diff;
                foreach ($unpaidPurchases as $purchase) {
                    if ($remainingAmount <= 0) break;
                    $due = $purchase->amount_due;
                    $pay = min($due, $remainingAmount);
                    $newPaid = $purchase->amount_paid + $pay;
                    $newDue  = $purchase->grand_total - $newPaid;
                    $purchase->update([
                        'amount_paid' => $newPaid,
                        'amount_due'  => $newDue,
                        'payment_status' => $newDue <= 0 ? 'paid' : 'partial',
                    ]);
                    $remainingAmount -= $pay;
                }
            } elseif ($diff < 0) {
                // Applied less money: un-apply from newest paid purchases
                $paidPurchases = $supplier->purchases()
                    ->where('amount_paid', '>', 0)
                    ->orderBy('purchase_date', 'desc')
                    ->get();
                $unapplyAmount = abs($diff);
                foreach ($paidPurchases as $purchase) {
                    if ($unapplyAmount <= 0) break;
                    $paid = $purchase->amount_paid;
                    $unpay = min($paid, $unapplyAmount);
                    $newPaid = $purchase->amount_paid - $unpay;
                    $newDue  = $purchase->grand_total - $newPaid;
                    $purchase->update([
                        'amount_paid' => $newPaid,
                        'amount_due'  => $newDue,
                        'payment_status' => $newPaid <= 0 ? 'unpaid' : 'partial',
                    ]);
                    $unapplyAmount -= $unpay;
                }
            }

            $payment->update([
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'description' => $validated['description'] ?? null,
            ]);
        });

        return redirect()->route('dashboard.suppliers.show', $supplier)
            ->with('success', 'Payment updated successfully.');
    }

    public function destroyPayment(SupplierPayment $payment): RedirectResponse
    {
        $supplier = $payment->supplier;
        $amount = $payment->amount;

        DB::transaction(function () use ($payment, $supplier, $amount) {
            $supplier->increment('current_balance', $amount);

            // Un-apply from newest paid purchases
            $paidPurchases = $supplier->purchases()
                ->where('amount_paid', '>', 0)
                ->orderBy('purchase_date', 'desc')
                ->get();
            $unapplyAmount = $amount;
            foreach ($paidPurchases as $purchase) {
                if ($unapplyAmount <= 0) break;
                $paid = $purchase->amount_paid;
                $unpay = min($paid, $unapplyAmount);
                $newPaid = $purchase->amount_paid - $unpay;
                $newDue  = $purchase->grand_total - $newPaid;
                $purchase->update([
                    'amount_paid' => $newPaid,
                    'amount_due'  => $newDue,
                    'payment_status' => $newPaid <= 0 ? 'unpaid' : 'partial',
                ]);
                $unapplyAmount -= $unpay;
            }

            $payment->delete();
        });

        return redirect()->route('dashboard.suppliers.show', $supplier)
            ->with('success', 'Payment deleted successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        return redirect()->route('dashboard.suppliers')->with('success', 'Supplier deleted successfully!');
    }
}
