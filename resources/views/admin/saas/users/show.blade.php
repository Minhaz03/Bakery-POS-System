<x-layouts.saas title="User Details">

    <style>
        .page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 26px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; align-items: center; }
        .card-title { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
        .card-body { padding: 24px; }
        
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .info-group { margin-bottom: 16px; }
        .info-label { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.5px; }
        .info-value { font-size: 15px; color: #0f172a; font-weight: 600; }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; text-decoration: none; }
        .btn-outline { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
        .btn-outline:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        
        .tenant-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
    </style>

    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('saas.users.index') }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
        
        <form action="{{ route('saas.impersonate', $user->id) }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-primary" onclick="return confirm('Log in as {{ $user->name }}?')">
                <i class="bi bi-box-arrow-in-right"></i> Login as User
            </button>
        </form>
    </div>

    <div class="page-header">
        <div style="width:56px;height:56px;background:#e0e7ff;border-radius:16px;display:flex;align-items:center;justify-content:center;color:#4f46e5;font-size:24px;font-weight:700;">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }} • Registered {{ $user->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- User Details -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge" style="color:#6366f1;font-size:18px;margin-right:8px;"></i>
                <h3 class="card-title">User Information</h3>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-group">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email Verified</div>
                        <div class="info-value">
                            @if($user->email_verified_at)
                                <span style="color:#10b981;"><i class="bi bi-check-circle-fill"></i> {{ $user->email_verified_at->format('M d, Y') }}</span>
                            @else
                                <span style="color:#f59e0b;"><i class="bi bi-exclamation-circle-fill"></i> Unverified</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ $user->updated_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Details -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-shop" style="color:#6366f1;font-size:18px;margin-right:8px;"></i>
                <h3 class="card-title">Associated Tenant</h3>
            </div>
            <div class="card-body">
                @if($user->tenant)
                    <div class="tenant-card">
                        <div class="info-group" style="margin-bottom: 20px;">
                            <div class="info-label">Tenant Name</div>
                            <div class="info-value" style="font-size: 20px;">{{ $user->tenant->name }}</div>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-group">
                                <div class="info-label">Tenant Domain</div>
                                <div class="info-value">{{ $user->tenant->domain ?? 'N/A' }}</div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Current Plan</div>
                                <div class="info-value">
                                    @php
                                        $activeSub = $user->tenant->activeSubscription();
                                    @endphp
                                    @if($activeSub)
                                        <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:999px;font-size:12px;">{{ $activeSub->plan->name }}</span>
                                    @else
                                        <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:12px;">None / Expired</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="text-align:center;padding:30px;color:#94a3b8;">
                        <i class="bi bi-shop-window" style="font-size:32px;margin-bottom:12px;display:block;"></i>
                        No tenant associated with this user.
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.saas>
