<x-layouts.saas title="Tenant Details">

    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px; }
        .page-title-wrap { display: flex; align-items: center; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .tenant-icon-lg { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.5); }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        @media(max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
        
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; align-items: center; gap: 10px; }
        .card-title { font-size: 16px; font-weight: 700; color: #0f172a; }
        .card-body { padding: 24px; }
        
        .info-list { list-style: none; margin: 0; padding: 0; }
        .info-list li { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #e2e8f0; }
        .info-list li:last-child { border-bottom: none; }
        .info-label { color: #64748b; font-size: 13.5px; font-weight: 500; }
        .info-value { color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; }
        
        .table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table th { background: #f8fafc; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 12px 20px; text-align: left; }
        .table td { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
        
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 600; }
        .badge.active { background: rgba(16,185,129,0.1); color: #047857; }
        .badge.expired { background: rgba(239,68,68,0.1); color: #b91c1c; }
        .badge.cancelled { background: #f1f5f9; color: #64748b; }
        
        .btn-outline { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; background: #fff; border: 1px solid #e2e8f0; color: #475569; text-decoration: none; transition: all 0.2s; }
        .btn-outline:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <div class="tenant-icon-lg"><i class="bi bi-shop"></i></div>
            <div>
                <h1>{{ $tenant->name }}</h1>
                <p>Tenant ID: {{ $tenant->id }} • Registered {{ $tenant->created_at->format('M d, Y') }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('saas.tenants.index') }}" class="btn-outline">
                <i class="bi bi-arrow-left"></i> Back to Tenants
            </a>
        </div>
    </div>

    <div class="grid-2">
        <!-- Tenant Info -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle text-primary" style="font-size:18px;"></i>
                <span class="card-title">Business Information</span>
            </div>
            <div class="card-body">
                <ul class="info-list">
                    <li>
                        <span class="info-label">Business Name</span>
                        <span class="info-value">{{ $tenant->name }}</span>
                    </li>
                    <li>
                        <span class="info-label">Domain</span>
                        <span class="info-value">{{ $tenant->domain ?: 'Not Set' }}</span>
                    </li>
                    <li>
                        <span class="info-label">Database connection</span>
                        <span class="info-value">{{ config('database.default') }} (Single DB Multi-tenant)</span>
                    </li>
                    <li>
                        <span class="info-label">Total Users</span>
                        <span class="info-value">{{ $tenant->users->count() }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-card-checklist text-success" style="font-size:18px;"></i>
                <span class="card-title">Current Subscription</span>
            </div>
            <div class="card-body">
                @php
                    $activeSub = $tenant->subscriptions->where('status', 'active')->first();
                @endphp
                @if($activeSub)
                    <div style="text-align:center; padding: 10px 0 20px;">
                        <div style="font-size:24px; font-weight:800; color:#0f172a;">{{ $activeSub->plan->name ?? 'Unknown' }}</div>
                        <div style="color:#64748b; font-size:14px;">${{ number_format($activeSub->plan->price ?? 0, 2) }} / {{ $activeSub->plan->billing_cycle ?? 'month' }}</div>
                        <div style="margin-top:10px;"><span class="badge active">Active</span></div>
                    </div>
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Starts At</span>
                            <span class="info-value">{{ $activeSub->starts_at->format('M d, Y') }}</span>
                        </li>
                        <li>
                            <span class="info-label">Ends At</span>
                            <span class="info-value">{{ $activeSub->ends_at->format('M d, Y') }}</span>
                        </li>
                    </ul>
                @else
                    <div style="text-align:center; padding: 40px 20px; color:#64748b;">
                        <i class="bi bi-exclamation-triangle" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
                        No active subscription found.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-people text-info" style="font-size:18px;"></i>
            <span class="card-title">Associated Users</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenant->users as $user)
                        <tr>
                            <td style="font-weight:600; color:#0f172a;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <form action="{{ route('saas.impersonate', $user->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-outline" style="padding: 4px 10px; font-size:12px;" onclick="return confirm('Log in as {{ $user->name }}?')">
                                        <i class="bi bi-box-arrow-in-right"></i> Login As
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 24px; color:#64748b;">No users found for this tenant.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.saas>
