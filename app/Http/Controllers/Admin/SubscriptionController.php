<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['tenant.users', 'plan']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $plans = \App\Models\Plan::all();
        $subscriptions = $query->latest()->paginate(15);
        return view('admin.saas.subscriptions.index', compact('subscriptions', 'plans'));
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['tenant.users', 'plan']);
        return view('admin.saas.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,expired,cancelled',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
        ]);

        $subscription->update($validated);

        return redirect()->back()->with('success', 'Subscription updated successfully.');
    }
}
