<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

        return view('dashboard.billing', compact('tenant', 'activeSubscription', 'plans', 'productsCount', 'usersCount', 'history'));
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $tenant = auth()->user()->tenant;
        $plan = Plan::findOrFail($request->plan_id);

        // Cancel previous subscriptions if any active
        Subscription::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Create new active subscription valid for 1 month
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        return redirect()->route('dashboard.billing')->with('success', "Subscribed to {$plan->name} successfully!");
    }
}
