<x-layouts.admin title="Profit & Loss Report">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.reports.index') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Profit & Loss Report</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Analyze gross revenue against the cost of goods sold.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.reports.profit-loss') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
                <div class="form-group" style="margin:0;flex:1;min-width:200px;">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $summary['start_date'] }}">
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:200px;">
                    <label class="form-label" for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $summary['end_date'] }}">
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('dashboard.reports.profit-loss') }}" class="btn btn-outline">Reset to Current Month</a>
                    <button type="button" onclick="window.print()" class="btn btn-outline"><i class="bi bi-printer"></i> Print</button>
                </div>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:24px;">
        <!-- P&L Statement -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Statement ({{ \Carbon\Carbon::parse($summary['start_date'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($summary['end_date'])->format('M d, Y') }})</span>
            </div>
            <div class="card-body" style="padding:0;">
                <table style="width:100%;border-collapse:collapse;font-size:14.5px;">
                    <tbody>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:16px 20px;color:#475569;">Gross Sales Revenue</td>
                            <td style="padding:16px 20px;text-align:right;font-weight:700;color:#0f172a;">৳ {{ number_format($summary['revenue'], 2) }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                            <td style="padding:16px 20px;color:#64748b;">Less: Cost of Goods Sold (COGS)</td>
                            <td style="padding:16px 20px;text-align:right;font-weight:600;color:#ef4444;">- ৳ {{ number_format($summary['cogs'], 2) }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:20px;font-weight:700;font-size:15px;color:#0f172a;">Gross Profit</td>
                            <td style="padding:20px;text-align:right;font-weight:700;font-size:16px; {{ $summary['gross_profit'] >= 0 ? 'color:#10b981;' : 'color:#ef4444;' }}">
                                ৳ {{ number_format($summary['gross_profit'], 2) }}
                            </td>
                        </tr>
                        <tr style="border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                            <td style="padding:16px 20px;color:#64748b;">Less: Operating Expenses</td>
                            <td style="padding:16px 20px;text-align:right;font-weight:600;color:#ef4444;">- ৳ {{ number_format($summary['total_expenses'], 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:20px;font-weight:800;font-size:16px;color:#0f172a;">Net Profit</td>
                            <td style="padding:20px;text-align:right;font-weight:800;font-size:20px; {{ $summary['net_profit'] >= 0 ? 'color:#10b981;' : 'color:#ef4444;' }}">
                                ৳ {{ number_format($summary['net_profit'], 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Margin Cards -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <div class="card" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%);color:#fff;text-align:center;flex:1;">
                <div class="card-body">
                    <div style="font-size:13px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;font-weight:600;margin-bottom:8px;">Gross Profit Margin</div>
                    <div style="font-size:42px;font-weight:800;letter-spacing:-1px;color:{{ $summary['margin_percentage'] >= 0 ? '#34d399' : '#f87171' }}">
                        {{ number_format($summary['margin_percentage'], 2) }}%
                    </div>
                </div>
            </div>
            
            <div class="card" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);color:#fff;text-align:center;flex:1;">
                <div class="card-body">
                    <div style="font-size:13px;color:#bfdbfe;text-transform:uppercase;letter-spacing:1px;font-weight:600;margin-bottom:8px;">Net Profit Margin</div>
                    <div style="font-size:42px;font-weight:800;letter-spacing:-1px;color:{{ $summary['net_margin_percentage'] >= 0 ? '#60a5fa' : '#fca5a5' }}">
                        {{ number_format($summary['net_margin_percentage'], 2) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style type="text/css" media="print">
        @page { size: portrait; }
        body { background: #fff !important; }
        .sidebar, .topbar, .btn-topbar, .card form, .btn { display: none !important; }
        .main-wrapper { margin: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; margin-bottom:20px; }
    </style>
</x-layouts.admin>
