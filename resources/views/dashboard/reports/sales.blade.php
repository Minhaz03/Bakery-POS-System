<x-layouts.admin title="Sales Report">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.reports.index') }}" class="btn-topbar"
            style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Sales Report</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">View detailed sales performance and revenue.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.reports.sales') }}"
                style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
                <div class="form-group" style="margin:0;flex:1;min-width:200px;">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date') }}">
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:200px;">
                    <label class="form-label" for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date') }}">
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('dashboard.reports.sales') }}" class="btn btn-outline">Clear</a>
                    <button type="button" onclick="window.print()" class="btn btn-outline"><i
                            class="bi bi-printer"></i> Print</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:24px;">
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">
                    Total Sales (Gross)</div>
                <div style="font-size:24px;font-weight:800;color:#0f172a;">৳
                    {{ number_format($summary['total_sales'], 2) }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">
                    Total Paid</div>
                <div style="font-size:24px;font-weight:800;color:#10b981;">৳
                    {{ number_format($summary['total_paid'], 2) }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">
                    Total Due</div>
                <div style="font-size:24px;font-weight:800;color:#ef4444;">৳
                    {{ number_format($summary['total_due'], 2) }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">
                    Discounts Given</div>
                <div style="font-size:24px;font-weight:800;color:#f59e0b;">৳
                    {{ number_format($summary['total_discount'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13.5px;text-align:left;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;">
                        <th style="padding:12px 16px;">Date</th>
                        <th style="padding:12px 16px;">Customer</th>
                        <th style="padding:12px 16px;text-align:center;">Items Sold</th>
                        <th style="padding:12px 16px;text-align:right;">Grand Total (৳)</th>
                        <th style="padding:12px 16px;text-align:right;">Paid (৳)</th>
                        <th style="padding:12px 16px;text-align:right;">Due (৳)</th>
                        <th style="padding:12px 16px;text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 16px;">{{ $sale->sale_date->format('M d, Y') }}</td>
                            <td style="padding:12px 16px;">
                                {{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</td>
                            <td style="padding:12px 16px;text-align:center;">{{ $sale->items->sum('quantity') }}</td>
                            <td style="padding:12px 16px;text-align:right;font-weight:600;">
                                {{ number_format($sale->grand_total, 2) }}</td>
                            <td style="padding:12px 16px;text-align:right;color:#10b981;">
                                {{ number_format($sale->amount_tendered, 2) }}</td>
                            <td style="padding:12px 16px;text-align:right;color:#ef4444;">
                                {{ number_format($sale->grand_total - $sale->amount_tendered, 2) }}</td>
                            <td style="padding:12px 16px;text-align:center;">
                                @if ($sale->status == 'completed')
                                    <span
                                        style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Paid</span>
                                @elseif($sale->status == 'partial')
                                    <span
                                        style="background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Partial</span>
                                @else
                                    <span
                                        style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Unpaid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:30px;text-align:center;color:#64748b;">No sales records
                                found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style type="text/css" media="print">
        @page {
            size: landscape;
        }

        body {
            background: #fff !important;
        }

        .sidebar,
        .topbar,
        .btn-topbar,
        .card form,
        .btn {
            display: none !important;
        }

        .main-wrapper {
            margin: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: none !important;
        }

        .card-body {
            padding: 0 !important;
        }
    </style>
</x-layouts.admin>
