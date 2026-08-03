<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\ProductionOrder;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('dashboard.reports.index');
    }

    public function salesReport(Request $request)
    {
        $query = Sale::with('customer', 'items.product')->orderBy('sale_date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('sale_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('sale_date', '<=', $request->end_date);
        }

        $sales = $query->get();

        $summary = [
            'total_sales' => $sales->sum('grand_total'),
            'total_paid' => $sales->sum('amount_tendered'),
            'total_due' => $sales->sum(function($sale) {
                return $sale->grand_total - $sale->amount_tendered;
            }),
            'total_discount' => $sales->sum('discount_amount'),
            'total_tax' => $sales->sum('tax_amount'),
        ];

        return view('dashboard.reports.sales', compact('sales', 'summary'));
    }

    public function purchasesReport(Request $request)
    {
        $query = Purchase::with('supplier', 'items.product')->orderBy('purchase_date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }

        $purchases = $query->get();

        $summary = [
            'total_purchases' => $purchases->sum('grand_total'),
            'total_paid' => $purchases->sum('amount_paid'),
            'total_due' => $purchases->sum(function($p) {
                return $p->grand_total - $p->amount_paid;
            }),
            'total_discount' => $purchases->sum('discount_amount'),
            'total_tax' => $purchases->sum('tax_amount'),
        ];

        return view('dashboard.reports.purchases', compact('purchases', 'summary'));
    }

    public function stockReport(Request $request)
    {
        $query = Product::with('category', 'unit')->orderBy('name', 'asc');

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();

        $summary = [
            'total_items' => $products->count(),
            'total_value' => $products->sum(function($p) {
                return $p->stock_qty * $p->cost_price;
            }),
            'low_stock_items' => $products->where('stock_qty', '<=', 10)->count(), // arbitrary low stock threshold
        ];

        $categories = \App\Models\Category::all();

        return view('dashboard.reports.stock', compact('products', 'summary', 'categories'));
    }

    public function productionReport(Request $request)
    {
        $query = ProductionOrder::with('recipe', 'creator')->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('planned_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('planned_date', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        $summary = [
            'total_orders'        => $orders->count(),
            'total_produced_qty'  => $orders->where('status', 'completed')->sum('actual_quantity'),
            'total_planned_qty'   => $orders->sum('planned_quantity'),
        ];

        return view('dashboard.reports.production', compact('orders', 'summary'));
    }

    public function profitLossReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $salesQuery = Sale::with('items.product')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);
        
        $sales = $salesQuery->get();

        $totalSalesRevenue = $sales->sum('grand_total');
        
        // Calculate Cost of Goods Sold (COGS) based on current product cost
        $totalCOGS = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $costPrice = $item->product ? $item->product->cost_price : 0;
                $totalCOGS += ($item->quantity * $costPrice);
            }
        }

        $grossProfit = $totalSalesRevenue - $totalCOGS;

        $summary = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'revenue' => $totalSalesRevenue,
            'cogs' => $totalCOGS,
            'gross_profit' => $grossProfit,
            'margin_percentage' => $totalSalesRevenue > 0 ? ($grossProfit / $totalSalesRevenue) * 100 : 0
        ];

        return view('dashboard.reports.profit_loss', compact('summary'));
    }
}
