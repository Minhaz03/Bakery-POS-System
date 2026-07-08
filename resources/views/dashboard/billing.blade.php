<x-layouts.admin title="Billing & Subscriptions">

    <div style="margin-bottom:24px;">
        <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Billing & Subscriptions</h2>
        <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage your bakery's subscription plans, review billing schedules, and monitor plan resource limits.</p>
    </div>

    @if(session('success'))
        <div class="toast-msg success" style="margin-bottom:20px;width:100%;max-width:none;animation:none;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="toast-msg error" style="margin-bottom:20px;width:100%;max-width:none;animation:none;background:#fffbeb;color:#b45309;border-color:#f59e0b;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('warning') }}
        </div>
    @endif

    {{-- Subscription status cards & limits --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;align-items:stretch;">
        
        {{-- Status card --}}
        <div class="card" style="display:flex;flex-direction:column;justify-content:space-between;border-left:5px solid {{ $activeSubscription ? 'var(--success)' : 'var(--danger)' }};">
            <div class="card-body">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:8px;">Current Status</div>
                @if($activeSubscription)
                    <h3 style="font-size:24px;font-weight:800;color:#0f172a;margin:0 0 4px 0;">{{ $activeSubscription->plan->name }}</h3>
                    <div style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;margin-bottom:12px;">
                        <i class="bi bi-check-circle-fill"></i> Active Subscription
                    </div>
                    <div style="font-size:13.5px;color:#64748b;">
                        <span>Renews/Expires on: </span>
                        <strong style="color:#1e293b;">{{ $activeSubscription->ends_at->format('d M, Y') }}</strong>
                    </div>
                @else
                    <h3 style="font-size:24px;font-weight:800;color:#dc2626;margin:0 0 4px 0;">No Active Subscription</h3>
                    <div style="display:inline-flex;align-items:center;gap:6px;background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;margin-bottom:12px;">
                        <i class="bi bi-x-circle-fill"></i> Suspended
                    </div>
                    <div style="font-size:13.5px;color:#64748b;">
                        Please subscribe to a plan below to unlock system functionality.
                    </div>
                @endif
            </div>
        </div>

        {{-- Limit counters --}}
        <div class="card">
            <div class="card-body" style="display:flex;flex-direction:column;gap:18px;">
                <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#94a3b8;">Plan Resource Usage</div>
                
                {{-- Product usage --}}
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:6px;">
                        <span style="color:#475569;"><i class="bi bi-box-seam"></i> Products Count</span>
                        @if($activeSubscription)
                            <span style="color:#0f172a;">{{ $productsCount }} / {{ $activeSubscription->plan->limit_products >= 9999 ? 'Unlimited' : $activeSubscription->plan->limit_products }}</span>
                        @else
                            <span style="color:#0f172a;">{{ $productsCount }} / 0</span>
                        @endif
                    </div>
                    <div style="width:100%;height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
                        @php
                            $prodLimit = $activeSubscription ? $activeSubscription->plan->limit_products : 0;
                            $prodPercent = $prodLimit > 0 ? min(100, ($productsCount / $prodLimit) * 100) : 100;
                        @endphp
                        <div style="width:{{ $prodPercent }}%;height:100%;background:{{ $prodPercent >= 90 ? 'var(--danger)' : 'var(--primary)' }};border-radius:999px;"></div>
                    </div>
                </div>

                {{-- User usage --}}
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:6px;">
                        <span style="color:#475569;"><i class="bi bi-people"></i> Users Count</span>
                        @if($activeSubscription)
                            <span style="color:#0f172a;">{{ $usersCount }} / {{ $activeSubscription->plan->limit_users >= 9999 ? 'Unlimited' : $activeSubscription->plan->limit_users }}</span>
                        @else
                            <span style="color:#0f172a;">{{ $usersCount }} / 0</span>
                        @endif
                    </div>
                    <div style="width:100%;height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden;">
                        @php
                            $userLimit = $activeSubscription ? $activeSubscription->plan->limit_users : 0;
                            $userPercent = $userLimit > 0 ? min(100, ($usersCount / $userLimit) * 100) : 100;
                        @endphp
                        <div style="width:{{ $userPercent }}%;height:100%;background:{{ $userPercent >= 90 ? 'var(--danger)' : 'var(--success)' }};border-radius:999px;"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Plans Comparison Grid --}}
    <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-grid-3x3-gap-fill" style="color:var(--primary);"></i> Available Subscription Plans
    </h3>

    <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:24px;align-items:stretch;">
        @foreach($plans as $plan)
            @php
                $isCurrent = $activeSubscription && $activeSubscription->subscription_plan_id === $plan->id;
            @endphp
            <div class="card" style="display:flex;flex-direction:column;justify-content:space-between;position:relative;border:{{ $isCurrent ? '2px solid var(--primary)' : '1px solid #e2e8f0' }};box-shadow:{{ $isCurrent ? '0 12px 24px rgba(99,102,241,0.08)' : 'none' }}">
                @if($isCurrent)
                    <div style="position:absolute;top:0;right:20px;transform:translateY(-50%);background:var(--primary);color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;padding:3px 12px;border-radius:999px;letter-spacing:0.5px;">Current Plan</div>
                @endif
                
                <div class="card-body" style="padding:28px;">
                    <div style="font-size:15px;font-weight:800;color:#475569;margin-bottom:6px;">{{ $plan->name }}</div>
                    <div style="margin-bottom:20px;">
                        <span style="font-size:32px;font-weight:900;color:#0f172a;">৳{{ number_format($plan->price, 0) }}</span>
                        <span style="color:#64748b;font-size:13.5px;"> /month</span>
                    </div>

                    <hr style="border:0;border-top:1px solid #f1f5f9;margin-bottom:20px;">

                    <ul style="list-style:none;padding:0;margin:0 0 28px 0;display:flex;flex-direction:column;gap:12px;font-size:13.5px;color:#334155;">
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-check2-circle" style="color:var(--success);font-size:16px;"></i>
                            <span>Up to <strong>{{ $plan->limit_products >= 9999 ? 'Unlimited' : $plan->limit_products }}</strong> Products</span>
                        </li>
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-check2-circle" style="color:var(--success);font-size:16px;"></i>
                            <span>Up to <strong>{{ $plan->limit_users >= 9999 ? 'Unlimited' : $plan->limit_users }}</strong> Users</span>
                        </li>
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-check2-circle" style="color:var(--success);font-size:16px;"></i>
                            <span>Real-time POS Terminal</span>
                        </li>
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-check2-circle" style="color:var(--success);font-size:16px;"></i>
                            <span>Baking Recipes & Production</span>
                        </li>
                        <li style="display:flex;align-items:center;gap:8px;">
                            <i class="bi bi-check2-circle" style="color:var(--success);font-size:16px;"></i>
                            <span>Reports & Live Dashboard</span>
                        </li>
                    </ul>
                </div>

                <div style="padding:0 28px 28px 28px;">
                    @if($isCurrent)
                        <button type="button" class="btn btn-outline" style="width:100%;justify-content:center;background:#f8fafc;color:#94a3b8;cursor:not-allowed;" disabled>
                            Active Plan
                        </button>
                    @else
                        <form method="POST" action="{{ route('dashboard.billing.subscribe') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;background:{{ $plan->id === 3 ? 'var(--primary)' : '#475569' }};border-color:{{ $plan->id === 3 ? 'var(--primary)' : '#475569' }}">
                                Subscribe & Pay
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @endforeach
    </div>

    {{-- Subscription History --}}
    <h3 style="font-size:18px;font-weight:800;color:#0f172a;margin:32px 0 16px 0;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-clock-history" style="color:var(--primary);"></i> Subscription History
    </h3>
    
    <div class="card" style="margin-bottom: 40px; overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 14px 24px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Plan</th>
                        <th style="padding: 14px 24px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Status</th>
                        <th style="padding: 14px 24px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Starts At</th>
                        <th style="padding: 14px 24px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Ends At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $sub)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 16px 24px; font-size: 14px; font-weight: 600; color: #334155;">
                                {{ $sub->plan->name }}
                            </td>
                            <td style="padding: 16px 24px;">
                                @if($sub->status === 'active')
                                    <span style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;">Active</span>
                                @elseif($sub->status === 'cancelled')
                                    <span style="background:#fee2e2;color:#b91c1c;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;">Cancelled</span>
                                @else
                                    <span style="background:#f1f5f9;color:#475569;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;">{{ ucfirst($sub->status) }}</span>
                                @endif
                            </td>
                            <td style="padding: 16px 24px; font-size: 14px; color: #475569;">
                                {{ $sub->starts_at->format('M d, Y') }}
                            </td>
                            <td style="padding: 16px 24px; font-size: 14px; color: #475569;">
                                {{ $sub->ends_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 30px; text-align: center; color: #94a3b8; font-size: 14px;">
                                No subscription history available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.admin>
