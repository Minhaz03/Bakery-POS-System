<x-layouts.admin title="Dashboard">

    <!-- KPI Cards -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:28px;">
        @php
            $kpis = [
                ['icon'=>'bi-cash-stack',      'color'=>'#6366f1','bg'=>'rgba(99,102,241,0.1)', 'label'=>"Today's Sales",       'value'=>'৳ ' . number_format($todaysSales, 2), 'href'=>route('dashboard.sales')],
                ['icon'=>'bi-clipboard-check', 'color'=>'#10b981','bg'=>'rgba(16,185,129,0.1)', 'label'=>'Production Today',    'value'=>($productionToday ?? 0) . ' items', 'href'=>route('dashboard.production')],
                ['icon'=>'bi-exclamation-triangle','color'=>'#f59e0b','bg'=>'rgba(245,158,11,0.1)','label'=>'Low Stock Alerts', 'value'=>$lowStockAlerts, 'href'=>route('dashboard.products')],
                ['icon'=>'bi-calendar-event',  'color'=>'#ef4444','bg'=>'rgba(239,68,68,0.1)',  'label'=>'Pending Orders',      'value'=>$pendingOrders, 'href'=>route('dashboard.custom-orders')],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <a href="{{ $kpi['href'] ?? '#' }}" style="text-decoration:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;display:flex;align-items:center;gap:16px;transition:all 0.2s;cursor:{{ isset($kpi['href']) ? 'pointer' : 'default' }};" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -4px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
            <div style="width:50px;height:50px;border-radius:12px;background:{{ $kpi['bg'] }};display:flex;align-items:center;justify-content:center;font-size:24px;color:{{ $kpi['color'] }};">
                <i class="bi {{ $kpi['icon'] }}"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:0.04em;">{{ $kpi['label'] }}</div>
                <div style="font-size:24px;font-weight:800;color:#0f172a;letter-spacing:-0.5px;">{{ $kpi['value'] }}</div>
            </div>
        </a>
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
                <a href="{{ route('dashboard.sales') }}" style="margin-left:auto;font-size:12px;color:#6366f1;text-decoration:none;">View all</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($recentSales->count() > 0)
                    <ul style="list-style:none;margin:0;padding:0;">
                        @foreach($recentSales as $sale)
                        <li style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;transition:background 0.2s;cursor:default;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <div style="display:flex;align-items:center;gap:14px;">
                                <div style="width:42px;height:42px;border-radius:50%;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;color:#10b981;font-size:18px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#1e293b;font-size:14px;letter-spacing:-0.2px;">{{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</div>
                                    <div style="font-size:12.5px;color:#64748b;margin-top:2px;">{{ $sale->invoice_no }}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700;color:#0f172a;font-size:14.5px;letter-spacing:-0.3px;">৳ {{ number_format($sale->grand_total, 2) }}</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:2px;">{{ $sale->created_at->diffForHumans() }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding:40px;text-align:center;color:#94a3b8;">
                        <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                        <div style="font-size:14px;">No sales yet</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-check" style="color:#f59e0b;font-size:18px;"></i>
                <span class="card-title">Today's Production Schedule</span>
                <a href="{{ route('dashboard.production') }}" style="margin-left:auto;font-size:12px;color:#6366f1;text-decoration:none;">View all</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($productionSchedule->count() > 0)
                    <ul style="list-style:none;margin:0;padding:0;">
                        @foreach($productionSchedule as $batch)
                        <li style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;transition:background 0.2s;cursor:default;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <div style="display:flex;align-items:center;gap:14px;">
                                <div style="width:42px;height:42px;border-radius:50%;background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;color:#f59e0b;font-size:18px;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#1e293b;font-size:14px;letter-spacing:-0.2px;">{{ $batch->batch_code }}</div>
                                    <div style="font-size:12.5px;color:#64748b;margin-top:2px;">{{ $batch->recipe ? $batch->recipe->name : 'Unknown Recipe' }}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700;color:#0f172a;font-size:14.5px;letter-spacing:-0.3px;">{{ $batch->qty }} units</div>
                                <div style="margin-top:2px;">
                                    <span style="font-size:11px;padding:3px 8px;border-radius:12px;font-weight:600;
                                        @if($batch->status == 'completed') background:#dcfce7;color:#166534;
                                        @elseif($batch->status == 'cancelled') background:#fee2e2;color:#991b1b;
                                        @else background:#fef3c7;color:#92400e; @endif">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding:40px;text-align:center;color:#94a3b8;">
                        <i class="bi bi-calendar-x" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                        <div style="font-size:14px;">No production scheduled</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Extra Data Row: Top Selling Products + Low Stock Items -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:18px;">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-star-fill" style="color:#ef4444;font-size:18px;"></i>
                <span class="card-title">Top Selling Products</span>
            </div>
            <div class="card-body" style="padding:0;">
                @if($topProducts->count() > 0)
                    <ul style="list-style:none;margin:0;padding:0;">
                        @foreach($topProducts as $item)
                        <li style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;transition:all 0.2s;cursor:default;" onmouseover="this.style.background='#fff1f2';this.style.transform='scale(1.01)'" onmouseout="this.style.background='transparent';this.style.transform='scale(1)'">
                            <div style="display:flex;align-items:center;gap:14px;">
                                <div style="width:42px;height:42px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:18px;border:1px solid #f1f5f9;">
                                    <i class="bi bi-bag-check"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#1e293b;font-size:14px;">{{ $item->product ? $item->product->name : 'Unknown' }}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700;color:#ef4444;font-size:14.5px;">{{ (float) $item->total_qty }} Sold</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding:40px;text-align:center;color:#94a3b8;">
                        <i class="bi bi-bag-x" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                        <div style="font-size:14px;">No sales data yet</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-exclamation-triangle" style="color:#f59e0b;font-size:18px;"></i>
                <span class="card-title">Low Stock Alerts</span>
                <a href="{{ route('dashboard.products') }}" style="margin-left:auto;font-size:12px;color:#6366f1;text-decoration:none;">Manage Stock</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($lowStockItems->count() > 0)
                    <ul style="list-style:none;margin:0;padding:0;">
                        @foreach($lowStockItems as $product)
                        <li style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;transition:all 0.2s;cursor:default;" onmouseover="this.style.background='#fffbeb';this.style.transform='scale(1.01)'" onmouseout="this.style.background='transparent';this.style.transform='scale(1)'">
                            <div style="display:flex;align-items:center;gap:14px;">
                                <div style="width:42px;height:42px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:18px;">
                                    <i class="bi bi-exclamation-circle"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#1e293b;font-size:14px;">{{ $product->name }}</div>
                                    <div style="font-size:12.5px;color:#64748b;margin-top:2px;">SKU: {{ $product->sku }}</div>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700;color:#b45309;font-size:14.5px;">{{ (float) $product->stock_qty }} Left</div>
                                <div style="font-size:12px;color:#d97706;margin-top:2px;">Alert at {{ (float) $product->alert_qty }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding:40px;text-align:center;color:#94a3b8;">
                        <i class="bi bi-check-circle" style="font-size:36px;display:block;margin-bottom:8px;color:#10b981;"></i>
                        <div style="font-size:14px;">Stock levels are healthy!</div>
                    </div>
                @endif
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
                    labels: {!! json_encode($salesChart['labels']) !!},
                    datasets: [{
                        label: 'Sales (৳)',
                        data: {!! json_encode($salesChart['data']) !!},
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
