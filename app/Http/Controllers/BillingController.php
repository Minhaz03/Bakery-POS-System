<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\User;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function index(): View
    {
        $tenant = auth()->user()->tenant;
        $activeSubscription = $tenant->activeSubscription();
        $plans = Plan::orderBy('price')->get();

        // Calculate limits usage
        $productsCount = Product::count();
        $usersCount = User::where('tenant_id', $tenant->id)->count();

        // Fetch subscription history
        $history = Subscription::where('tenant_id', $tenant->id)->with('plan')->latest()->get();

        return view('dashboard.billing.index', compact('tenant', 'activeSubscription', 'plans', 'productsCount', 'usersCount', 'history'));
    }

    public function subscribe(Request $request, SslCommerzService $sslCommerz): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $tenant = auth()->user()->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        $transactionId = uniqid('txn_');

        // Create new pending subscription
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'status' => 'pending',
            'transaction_id' => $transactionId,
            'amount' => $plan->price,
        ]);

        $gatewayUrl = $sslCommerz->initiatePayment($transactionId, $plan->price, $plan->name, $tenant);

        if ($gatewayUrl) {
            return redirect()->away($gatewayUrl);
        }

        return redirect()->route('dashboard.billing')->with('error', 'Payment gateway error. Please try again.');
    }

    public function paymentSuccess(Request $request, SslCommerzService $sslCommerz)
    {
        $valId = $request->input('val_id');
        $tranId = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'BDT');

        $subscription = Subscription::where('transaction_id', $tranId)->with('plan')->first();

        if (!$subscription || $subscription->status !== 'pending') {
            return redirect()->route('dashboard.billing')->with('error', 'Invalid transaction.');
        }

        $validation = $sslCommerz->validatePayment($valId, $amount, $currency);

        if ($validation) {
            // Cancel previous active subscriptions
            Subscription::where('tenant_id', $subscription->tenant_id)
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            $endsAt = $subscription->plan->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth();

            $subscription->update([
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $endsAt,
            ]);

            return redirect()->route('dashboard.billing')->with('success', 'Payment successful! Subscription activated.');
        }

        return redirect()->route('dashboard.billing')->with('error', 'Payment validation failed.');
    }

    public function paymentFail(Request $request)
    {
        $tranId = $request->input('tran_id');
        $subscription = Subscription::where('transaction_id', $tranId)->first();

        if ($subscription && $subscription->status === 'pending') {
            $subscription->update(['status' => 'cancelled']);
        }

        return redirect()->route('dashboard.billing')->with('error', 'Payment failed.');
    }

    public function paymentCancel(Request $request)
    {
        $tranId = $request->input('tran_id');
        $subscription = Subscription::where('transaction_id', $tranId)->first();

        if ($subscription && $subscription->status === 'pending') {
            $subscription->update(['status' => 'cancelled']);
        }

        return redirect()->route('dashboard.billing')->with('warning', 'Payment cancelled.');
    }

    public function paymentIpn(Request $request, SslCommerzService $sslCommerz)
    {
        // Handle IPN asynchronously
        $valId = $request->input('val_id');
        $tranId = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'BDT');
        $status = $request->input('status');

        if ($status !== 'VALID') {
            return response()->json(['status' => 'failed']);
        }

        $subscription = Subscription::where('transaction_id', $tranId)->with('plan')->first();

        if (!$subscription || $subscription->status !== 'pending') {
            return response()->json(['status' => 'invalid']);
        }

        $validation = $sslCommerz->validatePayment($valId, $amount, $currency);

        if ($validation) {
            Subscription::where('tenant_id', $subscription->tenant_id)
                ->where('id', '!=', $subscription->id)
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            $endsAt = $subscription->plan->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth();

            $subscription->update([
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $endsAt,
            ]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'failed']);
    }
}
