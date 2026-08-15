<x-layouts.admin title="Support Tickets">
    <div class="topbar">
        <h2 class="topbar-title"><i class="bi bi-headset" style="color:var(--primary);margin-right:8px;"></i> Support Tickets</h2>
        <a href="{{ route('dashboard.tickets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Open New Ticket
        </a>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:24px;">
            <div class="card" style="padding:18px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Total Tickets</div>
                        <div style="font-size:26px;font-weight:800;color:#0f172a;margin-top:4px;">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(99,102,241,0.1);color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:20px;">
                        <i class="bi bi-ticket-detailed"></i>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:18px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Open & In Progress</div>
                        <div style="font-size:26px;font-weight:800;color:#d97706;margin-top:4px;">{{ number_format($stats['open']) }}</div>
                    </div>
                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(245,158,11,0.1);color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:20px;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:18px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Staff Replied</div>
                        <div style="font-size:26px;font-weight:800;color:#4f46e5;margin-top:4px;">{{ number_format($stats['answered']) }}</div>
                    </div>
                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(79,70,229,0.1);color:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:20px;">
                        <i class="bi bi-reply-all-fill"></i>
                    </div>
                </div>
            </div>

            <div class="card" style="padding:18px 20px;border-radius:12px;border:1px solid #e2e8f0;background:#fff;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Resolved / Closed</div>
                        <div style="font-size:26px;font-weight:800;color:#059669;margin-top:4px;">{{ number_format($stats['closed']) }}</div>
                    </div>
                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(16,185,129,0.1);color:#10b981;display:flex;align-items:center;justify-content:center;font-size:20px;">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card" style="margin-bottom:20px;border-radius:12px;border:1px solid #e2e8f0;">
            <div class="card-body" style="padding:16px 20px;">
                <form method="GET" action="{{ route('dashboard.tickets.index') }}" style="display:grid;grid-template-columns:minmax(200px, 2fr) 1fr 1fr 1fr auto;gap:12px;align-items:center;">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search ticket # or subject..." style="font-size:13.5px;">
                    </div>
                    <div>
                        <select name="status" class="form-control" style="font-size:13.5px;">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Active (Open / In Progress)</option>
                            <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Staff Answered</option>
                            <option value="tenant_reply" {{ request('status') == 'tenant_reply' ? 'selected' : '' }}>Customer Reply</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <select name="priority" class="form-control" style="font-size:13.5px;">
                            <option value="">All Priorities</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div>
                        <select name="category" class="form-control" style="font-size:13.5px;">
                            <option value="">All Categories</option>
                            <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical Support</option>
                            <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>Billing & Payment</option>
                            <option value="feature_request" {{ request('category') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                            <option value="bug_report" {{ request('category') == 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                            <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit" class="btn btn-primary" style="padding:8px 14px;font-size:13.5px;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'priority', 'category']))
                            <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-outline" style="padding:8px 12px;font-size:13.5px;" title="Reset filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Tickets Table Card -->
        <div class="card" style="border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;">
            <div class="card-body" style="padding:0;">
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#64748b;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;">
                                <th style="padding:14px 20px;">Ticket #</th>
                                <th style="padding:14px 20px;">Subject & Category</th>
                                <th style="padding:14px 20px;">Priority</th>
                                <th style="padding:14px 20px;">Status</th>
                                <th style="padding:14px 20px;">Last Activity</th>
                                <th style="padding:14px 20px;text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody style="color:#334155;">
                            @forelse($tickets as $ticket)
                                @php
                                    $statusBadge = $ticket->status_badge;
                                    $priorityBadge = $ticket->priority_badge;
                                @endphp
                                <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:16px 20px;vertical-align:middle;">
                                        <span style="display:inline-block;font-family:monospace;font-weight:700;color:#4f46e5;background:#eef2ff;padding:4px 8px;border-radius:6px;font-size:12.5px;">
                                            {{ $ticket->ticket_number }}
                                        </span>
                                    </td>
                                    <td style="padding:16px 20px;vertical-align:middle;max-width:340px;">
                                        <a href="{{ route('dashboard.tickets.show', $ticket) }}" style="font-weight:700;color:#0f172a;text-decoration:none;font-size:14px;display:block;margin-bottom:3px;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#0f172a'">
                                            {{ $ticket->subject }}
                                        </a>
                                        <span style="font-size:12px;color:#64748b;display:inline-flex;align-items:center;gap:4px;">
                                            <i class="bi bi-tag"></i> {{ $ticket->category_label }}
                                        </span>
                                    </td>
                                    <td style="padding:16px 20px;vertical-align:middle;">
                                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:3px 9px;border-radius:20px;background:{{ $priorityBadge['bg'] }};color:{{ $priorityBadge['color'] }};">
                                            <i class="bi {{ $priorityBadge['icon'] }}"></i> {{ $priorityBadge['label'] }}
                                        </span>
                                    </td>
                                    <td style="padding:16px 20px;vertical-align:middle;">
                                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['color'] }};">
                                            <i class="bi {{ $statusBadge['icon'] }}"></i> {{ $ticket->status_label }}
                                        </span>
                                    </td>
                                    <td style="padding:16px 20px;vertical-align:middle;color:#64748b;font-size:12.5px;">
                                        <div><i class="bi bi-clock"></i> {{ $ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : $ticket->created_at->diffForHumans() }}</div>
                                        <div style="font-size:11px;color:#94a3b8;">Created {{ $ticket->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td style="padding:16px 20px;vertical-align:middle;text-align:right;">
                                        <a href="{{ route('dashboard.tickets.show', $ticket) }}" class="btn btn-outline btn-sm" style="font-weight:600;">
                                            <i class="bi bi-chat-text"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding:60px 24px;text-align:center;color:#64748b;">
                                        <div style="width:64px;height:64px;border-radius:50%;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;color:#94a3b8;font-size:28px;">
                                            <i class="bi bi-chat-square-dots"></i>
                                        </div>
                                        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:4px;">No support tickets found</div>
                                        <p style="font-size:13.5px;color:#64748b;margin:0 0 16px 0;">Need assistance with your bakery POS or subscription? Open a ticket with our support team.</p>
                                        <a href="{{ route('dashboard.tickets.create') }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-lg"></i> Create Your First Ticket
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($tickets->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
