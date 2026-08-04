<x-layouts.saas title="Manage Subscriptions">

    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px; }
        .page-title-wrap { display: flex; align-items: center; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .header-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #dcfce7, #86efac); color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.5); }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fff; }
        .card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        
        .table-responsive { width: 100%; overflow-x: auto; }
        .table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table th { background: #f8fafc; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 14px 24px; text-align: left; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; }
        .table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; vertical-align: middle; }
        .table tr:last-child td { border-bottom: none; }
        
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 600; }
        .badge.active { background: rgba(16,185,129,0.1); color: #047857; }
        .badge.expired { background: rgba(239,68,68,0.1); color: #b91c1c; }
        .badge.cancelled { background: #f1f5f9; color: #64748b; }
        
        .filter-bar { display: flex; gap: 12px; align-items: center; }
        .form-control { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13.5px; width: 100%; outline: none; transition: border-color 0.2s; }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; text-decoration: none; }
        .btn-outline { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
        .btn-outline:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { background: #4f46e5; box-shadow: 0 4px 12px rgba(99,102,241,0.2); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── Action Buttons ── */
        .action-btns { display: flex; gap: 6px; justify-content: flex-end; }
        .btn-icon {
            width: 32px; height: 32px; border-radius: 7px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 14px; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #475569; cursor: pointer;
            transition: all 0.15s; text-decoration: none;
        }
        .btn-icon:hover { background: #6366f1; border-color: #6366f1; color: #fff; }

        /* ── Modal ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.55); z-index: 1000;
            align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff; border-radius: 16px;
            width: 100%; max-width: 520px; max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,0.18);
            animation: modalIn 0.2s ease;
        }
        @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(-10px); } to { opacity:1; transform:scale(1) translateY(0); } }
        .modal-header {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 24px; border-bottom: 1px solid #f1f5f9;
        }
        .modal-header-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .modal-title { font-size: 16px; font-weight: 700; color: #0f172a; }
        .modal-sub   { font-size: 12.5px; color: #64748b; margin-top: 2px; }
        .modal-body  { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 8px; }
        .modal-close { background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; margin-left: auto; }
        .modal-close:hover { color: #ef4444; }

        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <div class="header-icon"><i class="bi bi-card-checklist"></i></div>
            <div>
                <h1>Tenant Subscriptions</h1>
                <p>View all active, expired, and cancelled subscriptions across the platform.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; border-left:4px solid #10b981; font-size:14px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fef2f2; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px; border-left:4px solid #ef4444; font-size:14px; font-weight:600;">
            @foreach($errors->all() as $err)
                <div style="margin-bottom:4px;">{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-header" style="display:flex; align-items:center;">
            <i class="bi bi-table" style="color:#6366f1;font-size:18px;margin-right:8px;"></i>
            <span class="card-title">Subscriptions List</span>
            <form method="GET" action="{{ route('saas.subscriptions.index') }}" class="filter-bar" style="margin-left:auto;margin-bottom:0;">
                <input type="text" name="search" class="form-control" placeholder="Search tenant..." value="{{ request('search') }}" style="max-width:250px;">
                <select name="status" class="form-control" style="max-width:160px;">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-search"></i> Search</button>
                @if(request()->hasAny(['search','status']))
                    <a href="{{ route('saas.subscriptions.index') }}" class="btn btn-outline btn-sm" style="color:#ef4444;"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tenant</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Starts At</th>
                        <th>Ends At</th>
                        <th style="text-align:right;">Actions</th>
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
                        <td style="font-size:12.5px; color:#64748b;">{{ $sub->starts_at ? $sub->starts_at->format('d M Y') : 'N/A' }}</td>
                        <td style="font-size:12.5px; color:#64748b;">
                            {{ $sub->ends_at ? $sub->ends_at->format('d M Y') : 'N/A' }}
                            @if($sub->ends_at && $sub->ends_at < now() && $sub->status == 'active')
                                <i class="bi bi-exclamation-triangle-fill text-danger" title="Expired"></i>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('saas.subscriptions.show', $sub->id) }}" class="btn-icon" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn-icon" title="Edit Subscription"
                                    onclick="openEditModal({{ $sub->id }}, '{{ addslashes($sub->tenant->name ?? 'N/A') }}', {{ $sub->plan_id ?? 'null' }}, '{{ $sub->status }}', '{{ $sub->starts_at ? $sub->starts_at->format('Y-m-d') : '' }}', '{{ $sub->ends_at ? $sub->ends_at->format('Y-m-d') : '' }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:48px;color:#94a3b8;">
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

    {{-- EDIT SUBSCRIPTION MODAL --}}
    <div class="modal-overlay" id="editSubModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="modal-title">Edit Subscription</div>
                    <div class="modal-sub" id="editSubTitle">Update details</div>
                </div>
                <button class="modal-close" type="button" onclick="closeModal('editSubModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" id="editSubForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Plan *</label>
                        <select name="plan_id" id="editPlan" class="form-control" required>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} (${{ $plan->price }}/{{ $plan->billing_cycle }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select name="status" id="editStatus" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div style="display:flex; gap:16px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Starts At *</label>
                            <input type="date" name="starts_at" id="editStartsAt" class="form-control" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Ends At *</label>
                            <input type="date" name="ends_at" id="editEndsAt" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('editSubModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id)  { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        function openEditModal(id, tenantName, planId, status, startsAt, endsAt) {
            document.getElementById('editSubForm').action = '/superadmin/subscriptions/' + id;
            document.getElementById('editSubTitle').textContent = 'Editing subscription for ' + tenantName;
            
            document.getElementById('editPlan').value = planId;
            document.getElementById('editStatus').value = status;
            document.getElementById('editStartsAt').value = startsAt;
            document.getElementById('editEndsAt').value = endsAt;
            
            openModal('editSubModal');
        }
    </script>
</x-layouts.saas>
