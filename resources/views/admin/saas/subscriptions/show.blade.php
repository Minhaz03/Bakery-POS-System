<x-layouts.saas title="Subscription Details">

    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px; }
        .page-title-wrap { display: flex; align-items: center; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .header-icon { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #dcfce7, #86efac); color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.5); }
        
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
        
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 600; }
        .badge.active { background: rgba(16,185,129,0.1); color: #047857; }
        .badge.expired { background: rgba(239,68,68,0.1); color: #b91c1c; }
        .badge.cancelled { background: #f1f5f9; color: #64748b; }
        
        .btn-outline { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; background: #fff; border: 1px solid #e2e8f0; color: #475569; text-decoration: none; transition: all 0.2s; }
        .btn-outline:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <div class="header-icon"><i class="bi bi-card-checklist"></i></div>
            <div>
                <h1>Subscription #{{ $subscription->id }}</h1>
                <p>Status: <span class="badge {{ $subscription->status }}">{{ ucfirst($subscription->status) }}</span> • Plan: {{ $subscription->plan->name ?? 'Unknown' }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('saas.subscriptions.index') }}" class="btn-outline">
                <i class="bi bi-arrow-left"></i> Back to Subscriptions
            </a>
        </div>
    </div>

    <div class="grid-2">
        <!-- Plan Details -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-tags text-primary" style="font-size:18px;"></i>
                <span class="card-title">Plan Details</span>
            </div>
            <div class="card-body">
                <ul class="info-list">
                    <li>
                        <span class="info-label">Plan Name</span>
                        <span class="info-value">{{ $subscription->plan->name ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="info-label">Price</span>
                        <span class="info-value">${{ number_format($subscription->plan->price ?? 0, 2) }}</span>
                    </li>
                    <li>
                        <span class="info-label">Billing Cycle</span>
                        <span class="info-value" style="text-transform: capitalize;">{{ $subscription->plan->billing_cycle ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="info-label">Max Products</span>
                        <span class="info-value">{{ $subscription->plan->limit_products ?? 0 }}</span>
                    </li>
                    <li>
                        <span class="info-label">Max Users</span>
                        <span class="info-value">{{ $subscription->plan->limit_users ?? 0 }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Subscription Timing -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar3 text-success" style="font-size:18px;"></i>
                <span class="card-title">Subscription Lifecycle</span>
            </div>
            <div class="card-body">
                <ul class="info-list">
                    <li>
                        <span class="info-label">Tenant</span>
                        <span class="info-value">
                            @if($subscription->tenant)
                                <a href="{{ route('saas.tenants.show', $subscription->tenant->id) }}" style="color: #6366f1; text-decoration: none;">
                                    {{ $subscription->tenant->name }} <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            @else
                                N/A
                            @endif
                        </span>
                    </li>
                    <li>
                        <span class="info-label">Created At</span>
                        <span class="info-value">{{ $subscription->created_at->format('M d, Y h:i A') }}</span>
                    </li>
                    <li>
                        <span class="info-label">Starts At</span>
                        <span class="info-value">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="info-label">Ends At</span>
                        <span class="info-value">
                            {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'N/A' }}
                            @if($subscription->ends_at && $subscription->ends_at < now() && $subscription->status == 'active')
                                <i class="bi bi-exclamation-triangle-fill text-danger" title="Expired" style="margin-left: 5px;"></i>
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if($subscription->tenant)
    <!-- Billing & Payment Information (Stub) -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-credit-card text-info" style="font-size:18px;"></i>
            <span class="card-title">Latest Payment Information</span>
        </div>
        <div class="card-body">
            @if($subscription->transaction_id)
                <ul class="info-list">
                    <li>
                        <span class="info-label">Transaction ID</span>
                        <span class="info-value">{{ $subscription->transaction_id }}</span>
                    </li>
                    <li>
                        <span class="info-label">Payment Method</span>
                        <span class="info-value">{{ $subscription->payment_method ?? 'N/A' }}</span>
                    </li>
                    <li>
                        <span class="info-label">Payment Status</span>
                        <span class="info-value">
                            <span class="badge {{ strtolower($subscription->payment_status) == 'paid' ? 'active' : 'cancelled' }}">
                                {{ ucfirst($subscription->payment_status ?? 'Pending') }}
                            </span>
                        </span>
                    </li>
                </ul>
            @else
                <div style="text-align:center; padding: 20px 0; color:#64748b;">
                    <i class="bi bi-credit-card" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
                    No detailed payment record attached to this subscription.
                </div>
            @endif
        </div>
    </div>
    
    @if($subscription->gateway_response)
    <!-- Gateway Response JSON -->
    <div class="card mt-4" style="margin-top: 24px;">
        <div class="card-header">
            <i class="bi bi-braces text-secondary" style="font-size:18px;"></i>
            <span class="card-title">Payment Gateway Response (Raw)</span>
        </div>
        <div class="card-body" style="background-color: #f8fafc; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <pre style="margin: 0; font-size: 13px; color: #334155; overflow-x: auto;"><code>{{ json_encode($subscription->gateway_response, JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>
    @endif
    @endif

</x-layouts.saas>
