<x-layouts.admin title="Business Analytics">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Report & Analytics</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">View business sales trends, menu performance, and
                financial analytics.</p>
        </div>
        <form method="GET" style="position: relative; display: inline-block;">
            <div
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--primary); pointer-events: none; display: flex; align-items: center;">
                <i class="bi bi-calendar-date" style="font-size: 16px;"></i>
            </div>
            <select name="days"
                style="appearance: none; -webkit-appearance: none; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 40px 10px 44px; font-size: 14.5px; font-weight: 600; color: #334155; cursor: pointer; transition: all 0.2s ease; outline: none; box-shadow: 0 1px 2px rgba(0,0,0,0.02);"
                onchange="this.form.submit()"
                onmouseover="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f1f5f9';"
                onmouseout="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f8fafc';"
                onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.15)'; this.style.backgroundColor='#fff';">
                <option value="7" {{ request('days', 7) == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="14" {{ request('days') == 14 ? 'selected' : '' }}>Last 14 Days</option>
                <option value="30" {{ request('days') == 30 ? 'selected' : '' }}>Last 30 Days</option>
            </select>
            <div
                style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none; display: flex; align-items: center;">
                <i class="bi bi-chevron-down" style="font-size: 13px; stroke-width: 2px;"></i>
            </div>
        </form>
    </div>

    <!-- Analytics KPIs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin-bottom:24px;">
        <div
            style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div
                style="width:48px;height:48px;border-radius:12px;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;font-size:22px;color:var(--primary);">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div
                    style="font-size:11.5px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">
                    Total Sales Revenue</div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;">৳
                    {{ number_format($analytics['total_sales'], 2) }}</div>
            </div>
        </div>
        <div
            style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div
                style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;font-size:22px;color:#10b981;">
                <i class="bi bi-cart-check"></i>
            </div>
            <div>
                <div
                    style="font-size:11.5px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">
                    Completed Orders</div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ $analytics['orders_count'] }} invoices
                </div>
            </div>
        </div>
        <div
            style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;">
            <div
                style="width:48px;height:48px;border-radius:12px;background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;font-size:22px;color:#f59e0b;">
                <i class="bi bi-ticket-perforated"></i>
            </div>
            <div>
                <div
                    style="font-size:11.5px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">
                    Average Order Value</div>
                <div style="font-size:22px;font-weight:800;color:#0f172a;">৳
                    {{ number_format($analytics['avg_order_value'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px;">
        <!-- Sales Trend -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up" style="color:var(--primary);font-size:18px;"></i>
                <span class="card-title">Sales Revenue Trend</span>
            </div>
            <div class="card-body" style="height:300px;display:flex;align-items:center;justify-content:center;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        <!-- Category Share -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart" style="color:#10b981;font-size:18px;"></i>
                <span class="card-title">Sales by Category</span>
            </div>
            <div class="card-body" style="height:300px;display:flex;align-items:center;justify-content:center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom: Top Performing Products -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-award" style="color:#f59e0b;font-size:18px;"></i>
            <span class="card-title">Top Performing Products</span>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:16px 20px;">Rank</th>
                        <th style="padding:16px 20px;">Menu Item</th>
                        <th style="padding:16px 20px;text-align:center;">Quantity Sold</th>
                        <th style="padding:16px 20px;text-align:right;">Total Revenue</th>
                        <th style="padding:16px 20px;text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @foreach ($analytics['top_selling'] as $index => $item)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:14px 20px;font-weight:800;color:var(--primary);">#{{ $index + 1 }}
                            </td>
                            <td style="padding:14px 20px;font-weight:700;color:#0f172a;">{{ $item['name'] }}</td>
                            <td style="padding:14px 20px;text-align:center;font-weight:600;">{{ $item['qty'] }} units
                            </td>
                            <td style="padding:14px 20px;text-align:right;font-weight:700;color:#0f172a;">৳
                                {{ number_format($item['revenue'], 2) }}</td>
                            <td style="padding:14px 20px;text-align:center;">
                                <span
                                    style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;"><i
                                        class="bi bi-graph-up-arrow"></i> High Performer</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ChartJS Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Trend Line Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');

            // Create gradient for the fill
            let gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($analytics['sales_by_day']['labels']),
                    datasets: [{
                        label: 'Revenue (৳)',
                        data: @json($analytics['sales_by_day']['data']),
                        borderColor: '#6366f1',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#6366f1',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            displayColors: false,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ৳ ' + context.parsed.y.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '৳' + value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Category Doughnut Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Bread', 'Pastry', 'Cake', 'Beverage'],
                    datasets: [{
                        data: [40, 30, 20, 10],
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ec4899'],
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</x-layouts.admin>
