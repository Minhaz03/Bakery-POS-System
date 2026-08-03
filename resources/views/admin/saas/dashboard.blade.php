<x-layouts.saas title="Dashboard">

    <style>
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .dashboard-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
        }

        /* ── KPI Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            border-color: #e2e8f0;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-icon.primary { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
        .stat-icon.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .stat-icon.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .stat-icon.info { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }

        .stat-info { flex: 1; }
        .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 6px; }
        .stat-label { font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ── Split Grid for Recent Items ── */
        .recent-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .recent-grid { grid-template-columns: 1fr; }
        }

        .recent-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .recent-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .recent-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recent-card-title i { color: #6366f1; }
        .recent-card-body { padding: 0; }

        .list-group { margin: 0; padding: 0; list-style: none; }
        .list-group-item {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: background 0.15s;
        }
        .list-group-item:last-child { border-bottom: none; }
        .list-group-item:hover { background: #f8fafc; }

        .item-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
        }

        .item-avatar.tenant { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
        .item-avatar.user { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

        .item-details { flex: 1; min-width: 0; }
        .item-name { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-sub { font-size: 12.5px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-meta { font-size: 12px; color: #94a3b8; text-align: right; }
        
        .btn-link { color: #6366f1; font-size: 13px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 4px; }
        .btn-link:hover { color: #4f46e5; text-decoration: underline; }
    </style>

    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Overview</h1>
            <p class="dashboard-subtitle">Here's what's happening with your platform today.</p>
        </div>
        <div>
            <a href="{{ route('saas.tenants.index') }}" class="btn btn-primary" style="padding: 10px 20px; border-radius: 10px;">
                <i class="bi bi-buildings"></i> View All Tenants
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary"><i class="bi bi-buildings-fill"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalTenants) }}</div>
                <div class="stat-label">Total Tenants</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($activeSubscriptions) }}</div>
                <div class="stat-label">Active Subscriptions</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-info">
                <div class="stat-value">${{ number_format($mrr, 2) }}</div>
                <div class="stat-label">Estimated MRR</div>
            </div>
        </div>
    </div>

    <div class="recent-grid">
        <!-- Recent Tenants -->
        <div class="recent-card">
            <div class="recent-card-header">
                <div class="recent-card-title"><i class="bi bi-shop"></i> Recent Tenants</div>
                <a href="{{ route('saas.tenants.index') }}" class="btn-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="recent-card-body">
                <ul class="list-group">
                    @forelse($recentTenants as $tenant)
                        <li class="list-group-item">
                            <div class="item-avatar tenant"><i class="bi bi-shop"></i></div>
                            <div class="item-details">
                                <div class="item-name">{{ $tenant->name }}</div>
                                <div class="item-sub">{{ $tenant->domain }}</div>
                            </div>
                            <div class="item-meta">
                                {{ $tenant->created_at->diffForHumans() }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item" style="justify-content: center; padding: 32px;">
                            <span style="color: #94a3b8; font-size: 14px;">No tenants found.</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="recent-card">
            <div class="recent-card-header">
                <div class="recent-card-title"><i class="bi bi-people-fill"></i> Recent Users</div>
                <a href="{{ route('saas.users.index') }}" class="btn-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="recent-card-body">
                <ul class="list-group">
                    @forelse($recentUsers as $user)
                        <li class="list-group-item">
                            <div class="item-avatar user">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div class="item-details">
                                <div class="item-name">{{ $user->name }}</div>
                                <div class="item-sub">{{ $user->email }}</div>
                            </div>
                            <div class="item-meta">
                                <span style="display:block;color:#64748b;font-weight:600;margin-bottom:2px;">{{ $user->tenant->name ?? 'No Tenant' }}</span>
                                {{ $user->created_at->diffForHumans() }}
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item" style="justify-content: center; padding: 32px;">
                            <span style="color: #94a3b8; font-size: 14px;">No users found.</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

</x-layouts.saas>
