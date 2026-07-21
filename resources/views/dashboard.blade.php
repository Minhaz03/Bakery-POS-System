<x-layouts.admin title="Dashboard">

    <!-- KPI Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:28px;">
        @php
            $kpis = [
                ['icon'=>'bi-cash-stack',      'color'=>'#6366f1','bg'=>'rgba(99,102,241,0.1)', 'label'=>"Today's Sales",       'value'=>'৳ 0'],
                ['icon'=>'bi-clipboard-check', 'color'=>'#10b981','bg'=>'rgba(16,185,129,0.1)', 'label'=>'Production Today',    'value'=>'0 items'],
                ['icon'=>'bi-exclamation-triangle','color'=>'#f59e0b','bg'=>'rgba(245,158,11,0.1)','label'=>'Low Stock Alerts', 'value'=>'0'],
                ['icon'=>'bi-calendar-event',  'color'=>'#ef4444','bg'=>'rgba(239,68,68,0.1)',  'label'=>'Pending Orders',      'value'=>'0'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;display:flex;align-items:center;gap:16px;">
            <div style="width:50px;height:50px;border-radius:12px;background:{{ $kpi['bg'] }};display:flex;align-items:center;justify-content:center;font-size:24px;color:{{ $kpi['color'] }};">
                <i class="bi {{ $kpi['icon'] }}"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:0.04em;">{{ $kpi['label'] }}</div>
                <div style="font-size:24px;font-weight:800;color:#0f172a;letter-spacing:-0.5px;">{{ $kpi['value'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Grid: Chart + Quick Actions -->
    <div style="display:grid;grid-template-columns:1fr 320px;gap:18px;margin-bottom:20px;">
        <!-- Sales Chart -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up" style="color:#6366f1;font-size:18px;"></i>
                <span class="card-title">Sales Overview</span>
                <span style="margin-left:auto;font-size:12px;color:#64748b;">Last 7 days</span>
            </div>
            <div class="card-body" style="height:260px;display:flex;align-items:center;justify-content:center;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <!-- Quick Links -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-charge" style="color:#f59e0b;font-size:18px;"></i>
                <span class="card-title">Quick Actions</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                @php
                    $quickActions = [
                        ['icon'=>'bi-calculator',     'label'=>'Open POS',          'href'=>route('dashboard.pos-terminal'),        'color'=>'#6366f1'],
                        ['icon'=>'bi-plus-circle',    'label'=>'New Purchase',       'href'=>route('dashboard.purchases.create'),    'color'=>'#10b981'],
                        ['icon'=>'bi-egg-fried',      'label'=>'New Production',     'href'=>route('dashboard.production'),          'color'=>'#f59e0b'],
                        ['icon'=>'bi-calendar-plus',  'label'=>'New Custom Order',   'href'=>route('dashboard.custom-orders') . '?openModal=1', 'color'=>'#ef4444'],
                        ['icon'=>'bi-box-seam',       'label'=>'Add Product',        'href'=>route('dashboard.products.create'),     'color'=>'#8b5cf6'],
                        ['icon'=>'bi-bar-chart-line', 'label'=>'View Reports',       'href'=>route('dashboard.reports.index'),       'color'=>'#06b6d4'],
                    ];
                @endphp
                @foreach($quickActions as $action)
                <a href="{{ $action['href'] }}" style="display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:8px;background:#f8fafc;border:1px solid #f1f5f9;text-decoration:none;color:#1e293b;font-size:13.5px;font-weight:500;transition:all 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                    <i class="bi {{ $action['icon'] }}" style="color:{{ $action['color'] }};font-size:16px;width:20px;text-align:center;"></i>
                    {{ $action['label'] }}
                    <i class="bi bi-chevron-right" style="margin-left:auto;font-size:12px;color:#94a3b8;"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Sales + Production Schedule -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt" style="color:#10b981;font-size:18px;"></i>
                <span class="card-title">Recent Sales</span>
                <a href="#" style="margin-left:auto;font-size:12px;color:#6366f1;text-decoration:none;">View all</a>
            </div>
            <div class="card-body" style="padding:0;">
                <div style="padding:40px;text-align:center;color:#94a3b8;">
                    <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                    <div style="font-size:14px;">No sales yet</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-check" style="color:#f59e0b;font-size:18px;"></i>
                <span class="card-title">Today's Production Schedule</span>
            </div>
            <div class="card-body" style="padding:0;">
                <div style="padding:40px;text-align:center;color:#94a3b8;">
                    <i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                    <div style="font-size:14px;">No production scheduled</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                    datasets: [{
                        label: 'Sales (৳)',
                        data: [0,0,0,0,0,0,0],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#6366f1',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        }
    </script>

</x-layouts.admin>
