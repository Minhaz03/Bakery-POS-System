<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::latest()->paginate(15);
        return view('admin.saas.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.saas.plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:monthly,yearly',
            'limit_products' => 'required|integer|min:0',
            'limit_users' => 'required|integer|min:0',
        ]);

        Plan::create($validated);
        return redirect()->route('saas.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.saas.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:monthly,yearly',
            'limit_products' => 'required|integer|min:0',
            'limit_users' => 'required|integer|min:0',
        ]);

        $plan->update($validated);
        return redirect()->route('saas.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete plan because it has active subscriptions.');
        }

        $plan->delete();
        return redirect()->route('saas.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
