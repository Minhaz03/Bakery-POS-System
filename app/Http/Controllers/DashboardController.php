<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Sale;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\CustomOrder;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(Request $request): View
    {
        // 1. Today's Sales
        $todaysSales = Sale::whereDate('created_at', Carbon::today())->sum('grand_total');

        // 2. Production Today
        $productionToday = ProductionBatch::whereDate('created_at', Carbon::today())->sum('qty');

        // 3. Low Stock Alerts
        $lowStockAlerts = Product::whereColumn('stock_qty', '<=', 'alert_qty')->count();

        // 4. Pending Orders
        $pendingOrders = CustomOrder::where('status', 'pending')->count();

        // 5. Sales Chart
        $days = (int) $request->get('days', 7);
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $days <= 7 ? $date->format('D') : $date->format('M d'); // Mon, Tue, or Jan 01
            $daySales = Sale::whereDate('created_at', $date)->sum('grand_total');
            $data[] = (float) $daySales;
        }
        $salesChart = [
            'labels' => $labels,
            'data' => $data,
            'days' => $days,
        ];

        // 6. Recent Sales
        $recentSales = Sale::with('customer')->orderBy('created_at', 'desc')->take(5)->get();

        // 7. Today's Production Schedule
        $productionSchedule = ProductionBatch::with('recipe')->whereDate('scheduled_at', Carbon::today())->take(5)->get();

        // 8. Top Selling Products
        $topProducts = \App\Models\SaleItem::with('product')
            ->select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // 9. Low Stock Items List
        $lowStockItems = Product::whereColumn('stock_qty', '<=', 'alert_qty')->take(5)->get();

        return view('dashboard', compact(
            'todaysSales',
            'productionToday',
            'lowStockAlerts',
            'pendingOrders',
            'salesChart',
            'recentSales',
            'productionSchedule',
            'topProducts',
            'lowStockItems'
        ));
    }
}
