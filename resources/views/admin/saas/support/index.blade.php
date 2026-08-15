<x-layouts.saas title="Support Tickets">
    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-title-wrap { display: flex; align-items: center; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .header-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; }
        .stat-value { font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 4px; }
        .stat-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 24px; }
        .table-responsive { width: 100%; overflow-x: auto; }
        .table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table th { background: #f8fafc; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 14px 20px; text-align: left; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; }
        .table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #334155; vertical-align: middle; }
        
        .action-btns { display: flex; gap: 6px; justify-content: flex-end; }
        .btn-icon { width: 32px; height: 32px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; cursor: pointer; transition: all 0.15s; text-decoration: none; }
        .btn-icon:hover { background: #6366f1; border-color: #6366f1; color: #fff; }
        .btn-icon.delete:hover { background: #ef4444; border-color: #ef4444; color: #fff; }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <div class="header-icon">
                <i class="bi bi-headset"></i>
            </div>
            <div>
                <h1>Support Tickets Console</h1>
                <p>Monitor, assign, and respond to tenant customer inquiries and issues in real-time.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <!-- KPI Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total System Tickets</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div style="width:46px;height:46px;border-radius:10px;background:rgba(99,102,241,0.1);color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:22px;">
                <i class="bi bi-ticket-detailed"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Open / Active</div>
                <div class="stat-value" style="color:#d97706;">{{ number_format($stats['open']) }}</div>
            </div>
            <div style="width:46px;height:46px;border-radius:10px;background:rgba(245,158,11,0.1);color:#f59e0b;display:flex;align-items:center;justify-content:center;font-size:22px;">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Pending Admin Action</div>
                <div class="stat-value" style="color:#dc2626;">{{ number_format($stats['pending_admin']) }}</div>
            </div>
            <div style="width:46px;height:46px;border-radius:10px;background:rgba(239,68,68,0.1);color:#ef4444;display:flex;align-items:center;justify-content:center;font-size:22px;">
                <i class="bi bi-exclamation-circle"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Resolved / Closed</div>
                <div class="stat-value" style="color:#059669;">{{ number_format($stats['resolved']) }}</div>
            </div>
            <div style="width:46px;height:46px;border-radius:10px;background:rgba(16,185,129,0.1);color:#10b981;display:flex;align-items:center;justify-content:center;font-size:22px;">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom:20px;">
        <div style="padding:16px 20px;">
            <form method="GET" action="{{ route('saas.tickets.index') }}" style="display:grid;grid-template-columns:minmax(180px, 2fr) minmax(140px, 1fr) minmax(130px, 1fr) minmax(120px, 1fr) minmax(130px, 1fr) auto;gap:12px;align-items:center;">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search ticket #, subject, tenant..." style="font-size:13.5px;">
                </div>
                <div>
                    <select name="tenant_id" class="form-control" style="font-size:13.5px;">
                        <option value="">All Tenants</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
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
                    <select name="assigned_to" class="form-control" style="font-size:13.5px;">
                        <option value="">All Staff</option>
                        <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                        @foreach($admins as $a)
                            <option value="{{ $a->id }}" {{ request('assigned_to') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:6px;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding:8px 14px;">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'tenant_id', 'status', 'priority', 'assigned_to', 'category']))
                        <a href="{{ route('saas.tickets.index') }}" class="btn btn-outline btn-sm" style="padding:8px 10px;" title="Clear filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Tenant / Customer</th>
                        <th>Subject & Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned Staff</th>
                        <th>Last Activity</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        @php
                            $statusBadge = $ticket->status_badge;
                            $priorityBadge = $ticket->priority_badge;
                        @endphp
                        <tr>
                            <td>
                                <span style="font-family:monospace;font-weight:700;color:#4f46e5;background:#eef2ff;padding:4px 8px;border-radius:6px;font-size:12.5px;">
                                    {{ $ticket->ticket_number }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:700;color:#0f172a;font-size:13.5px;">
                                    {{ $ticket->tenant?->name ?? 'Tenant #' . $ticket->tenant_id }}
                                </div>
                                <div style="font-size:12px;color:#64748b;">
                                    {{ $ticket->user?->name ?? 'User' }} &bull; {{ $ticket->user?->email }}
                                </div>
                            </td>
                            <td style="max-width:280px;">
                                <a href="{{ route('saas.tickets.show', $ticket) }}" style="font-weight:700;color:#0f172a;text-decoration:none;font-size:13.5px;display:block;margin-bottom:3px;" onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#0f172a'">
                                    {{ $ticket->subject }}
                                </a>
                                <span style="font-size:11.5px;color:#64748b;display:inline-flex;align-items:center;gap:4px;">
                                    <i class="bi bi-tag"></i> {{ $ticket->category_label }}
                                </span>
                            </td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;padding:3px 9px;border-radius:20px;background:{{ $priorityBadge['bg'] }};color:{{ $priorityBadge['color'] }};">
                                    <i class="bi {{ $priorityBadge['icon'] }}"></i> {{ $priorityBadge['label'] }}
                                </span>
                            </td>
                            <td>
                                <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['color'] }};">
                                    <i class="bi {{ $statusBadge['icon'] }}"></i> {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->assignedAdmin)
                                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:12.5px;font-weight:600;color:#334155;">
                                        <i class="bi bi-person-badge" style="color:#6366f1;"></i> {{ $ticket->assignedAdmin->name }}
                                    </span>
                                @else
                                    <span style="font-size:12px;color:#94a3b8;font-style:italic;">Unassigned</span>
                                @endif
                            </td>
                            <td style="font-size:12.5px;color:#64748b;">
                                <div><i class="bi bi-clock"></i> {{ $ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : $ticket->created_at->diffForHumans() }}</div>
                            </td>
                            <td style="text-align:right;">
                                <div class="action-btns">
                                    <a href="{{ route('saas.tickets.show', $ticket) }}" class="btn-icon" title="View & Reply">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('saas.tickets.destroy', $ticket) }}" method="POST" style="margin:0;" onsubmit="return confirm('Permanently delete this support ticket and all messages?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon delete" title="Delete Ticket">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:60px 24px;text-align:center;color:#64748b;">
                                <i class="bi bi-inbox" style="font-size:44px;color:#cbd5e1;display:block;margin-bottom:12px;"></i>
                                <div style="font-size:16px;font-weight:700;color:#1e293b;">No support tickets found</div>
                                <p style="font-size:13px;color:#64748b;margin-top:4px;">No tickets currently match the selected criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-layouts.saas>
