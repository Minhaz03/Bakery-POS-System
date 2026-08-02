<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalTenants = Tenant::count();
        
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        
        // Calculate estimated MRR (Monthly Recurring Revenue) based on active subscriptions
        $mrr = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price');

        $recentUsers = User::latest()->take(5)->with('tenant')->get();
        $recentTenants = Tenant::latest()->take(5)->get();

        return view('admin.saas.dashboard', compact(
            'totalTenants',
            'activeSubscriptions',
            'mrr',
            'recentUsers',
            'recentTenants'
        ));
    }
}
