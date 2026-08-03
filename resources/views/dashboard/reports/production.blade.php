<x-layouts.admin title="Production Report">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.reports.index') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Production Report</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Track production orders, output quantities, and costs.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.reports.production') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
                <div class="form-group" style="margin:0;flex:1;min-width:180px;">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:180px;">
                    <label class="form-label" for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:180px;">
                    <label class="form-label" for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="planned"     {{ request('status') == 'planned'     ? 'selected' : '' }}>Planned</option>
                        <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed"   {{ request('status') == 'completed'   ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled"   {{ request('status') == 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('dashboard.reports.production') }}" class="btn btn-outline">Clear</a>
                    <button type="button" onclick="window.print()" class="btn btn-outline"><i class="bi bi-printer"></i> Print</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:24px;">
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">Total Orders</div>
                <div style="font-size:24px;font-weight:800;color:#0f172a;">{{ $summary['total_orders'] }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">Total Planned Qty</div>
                <div style="font-size:24px;font-weight:800;color:#3b82f6;">{{ number_format(floatval($summary['total_planned_qty']), 2) }} <span style="font-size:14px;color:#64748b;">units</span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding:16px 20px;">
                <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;margin-bottom:6px;">Total Output (Completed)</div>
                <div style="font-size:24px;font-weight:800;color:#10b981;">{{ number_format(floatval($summary['total_produced_qty']), 2) }} <span style="font-size:14px;color:#64748b;">units</span></div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13.5px;text-align:left;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:12px 16px;">Reference</th>
                        <th style="padding:12px 16px;">Recipe</th>
                        <th style="padding:12px 16px;">Planned Date</th>
                        <th style="padding:12px 16px;text-align:center;">Planned Qty</th>
                        <th style="padding:12px 16px;text-align:center;">Actual Qty</th>
                        <th style="padding:12px 16px;text-align:right;">Total Cost</th>
                        <th style="padding:12px 16px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 16px;font-family:monospace;font-weight:600;color:#475569;font-size:12px;">{{ $order->reference_no }}</td>
                            <td style="padding:12px 16px;font-weight:700;color:#0f172a;">{{ $order->recipe?->name ?? '—' }}</td>
                            <td style="padding:12px 16px;color:#64748b;">{{ $order->planned_date?->format('M d, Y') ?? '—' }}</td>
                            <td style="padding:12px 16px;text-align:center;color:#64748b;">{{ number_format(floatval($order->planned_quantity), 2) }}</td>
                            <td style="padding:12px 16px;text-align:center;font-weight:700;color:#10b981;">
                                {{ $order->actual_quantity > 0 ? number_format(floatval($order->actual_quantity), 2) : '—' }}
                            </td>
                            <td style="padding:12px 16px;text-align:right;font-weight:600;color:#0f172a;">
                                {{ $order->total_cost > 0 ? number_format($order->total_cost, 2) : '—' }}
                            </td>
                            <td style="padding:12px 16px;">
                                @if($order->status === 'completed')
                                    <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Completed</span>
                                @elseif($order->status === 'in-progress')
                                    <span style="background:#eff6ff;color:#1d4ed8;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">In Progress</span>
                                @elseif($order->status === 'planned')
                                    <span style="background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Planned</span>
                                @else
                                    <span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:30px;text-align:center;color:#64748b;">No production orders found for the selected criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style type="text/css" media="print">
        @page { size: portrait; }
        body { background: #fff !important; }
        .sidebar, .topbar, .btn-topbar, .card form, .btn { display: none !important; }
        .main-wrapper { margin: 0 !important; }
        .card { box-shadow: none !important; border: none !important; }
        .card-body { padding: 0 !important; }
    </style>
</x-layouts.admin>
