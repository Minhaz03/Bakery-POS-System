<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display Analytics.
     */
    public function analytics(Request $request): View
    {
        $days = (int) $request->input('days', 7);
        if (!in_array($days, [7, 14, 30])) {
            $days = 7;
        }

        $startDate = Carbon::today()->subDays($days - 1);

        $totalSales = Sale::whereDate('created_at', '>=', $startDate)->sum('grand_total');
        $ordersCount = Sale::whereDate('created_at', '>=', $startDate)->count();
        $avgOrderValue = $ordersCount > 0 ? $totalSales / $ordersCount : 0;

        // Top selling products
        $topSelling = SaleItem::with('product')
            ->whereHas('sale', function ($query) use ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            })
            ->select('product_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(subtotal) as revenue'))
            ->groupBy('product_id')
            ->orderBy('qty', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product ? $item->product->name : 'Unknown',
                    'qty' => $item->qty,
                    'revenue' => $item->revenue,
                ];
            })->toArray();

        // Sales by day
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            if ($days == 30) {
                $labels[] = $date->format('M d'); // e.g., Oct 01 for longer range
            } elseif ($days == 14) {
                $labels[] = $date->format('M d'); 
            } else {
                $labels[] = $date->format('D'); // Mon, Tue for 7 days
            }

            $daySales = Sale::whereDate('created_at', $date)->sum('grand_total');
            $data[] = (float) $daySales;
        }

        $analytics = [
            'total_sales' => $totalSales,
            'orders_count' => $ordersCount,
            'avg_order_value' => round($avgOrderValue, 2),
            'top_selling' => $topSelling,
            'sales_by_day' => [
                'labels' => $labels,
                'data' => $data,
            ]
        ];

        return view('dashboard.analytics', compact('analytics', 'days'));
    }
}
