<x-layouts.saas title="Manage Subscriptions">

    <style>
        .page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 26px; }
        .page-header-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: linear-gradient(135deg,#6366f1,#818cf8);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff; flex-shrink: 0;
        }
        .page-header-info h1 { font-size:20px; font-weight:800; color:#0f172a; margin:0; }
        .page-header-info p  { font-size:13px; color:#64748b; margin:3px 0 0; }
        .filter-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 18px; }
        .table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .table th {
            padding: 11px 16px; text-align: left; background: #f8fafc; color: #475569;
            font-size: 11.5px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;
        }
        .table td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: middle; }
        .table tbody tr:hover td { background: #fafbff; }
        .badge {
            padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 600;
        }
        .badge.active { background: rgba(16,185,129,0.1); color: #047857; }
        .badge.expired { background: rgba(239,68,68,0.1); color: #b91c1c; }
        .badge.cancelled { background: #f1f5f9; color: #64748b; }
    </style>

    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-card-list"></i></div>
        <div class="page-header-info">
            <h1>Tenant Subscriptions</h1>
            <p>View all active, expired, and cancelled subscriptions across the platform</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display:flex; align-items:center;">
            <i class="bi bi-table" style="color:#6366f1;font-size:18px;margin-right:8px;"></i>
            <span class="card-title">Subscriptions List</span>
            <form method="GET" action="{{ route('saas.subscriptions.index') }}" class="filter-bar" style="margin-left:auto;margin-bottom:0;">
                <input type="text" name="search" class="form-control" placeholder="Search tenant..." value="{{ request('search') }}" style="max-width:210px;">
                <select name="status" class="form-control" style="max-width:160px;">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                @if(request()->hasAny(['search','status']))
                    <a href="{{ route('saas.subscriptions.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tenant</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Starts At</th>
                        <th>Ends At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                    <tr>
                        <td style="color:#94a3b8;">#{{ $sub->id }}</td>
                        <td style="font-weight:600;">{{ $sub->tenant->name ?? 'N/A' }}</td>
                        <td>{{ $sub->plan->name ?? 'N/A' }} <br>
                            <small style="color:#64748b;">{{ number_format($sub->plan->price ?? 0, 2) }} / {{ $sub->plan->billing_cycle ?? 'month' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                        </td>
                        <td style="font-size:12.5px; color:#64748b;">{{ $sub->starts_at->format('d M Y') }}</td>
                        <td style="font-size:12.5px; color:#64748b;">
                            {{ $sub->ends_at->format('d M Y') }}
                            @if($sub->ends_at < now() && $sub->status == 'active')
                                <i class="bi bi-exclamation-triangle-fill text-danger" title="Expired"></i>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">
                            <i class="bi bi-card-list" style="font-size:36px;display:block;margin-bottom:10px;"></i>
                            <div>No subscriptions found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
        <div style="padding:14px 22px;border-top:1px solid #f1f5f9;">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</x-layouts.saas>
