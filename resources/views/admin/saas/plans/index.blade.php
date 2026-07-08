<x-layouts.saas title="Pricing Plans">

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
        .table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .table th {
            padding: 11px 16px; text-align: left; background: #f8fafc; color: #475569;
            font-size: 11.5px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;
        }
        .table td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: middle; }
        
        /* ── Action Buttons ── */
        .action-btns { display: flex; gap: 6px; }
        .btn-icon {
            width: 32px; height: 32px; border-radius: 7px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 14px; border: 1px solid #e2e8f0;
            background: #f8fafc; color: #475569; cursor: pointer;
            transition: all 0.15s; text-decoration: none;
        }
        .btn-icon:hover { background: #6366f1; border-color: #6366f1; color: #fff; }
        .btn-icon.danger:hover { background: #ef4444; border-color: #ef4444; color: #fff; }

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
    </style>

    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-tags"></i></div>
        <div class="page-header-info">
            <h1>Pricing Plans</h1>
            <p>Manage subscription plans and their limits</p>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px;">
            <button class="btn btn-primary" onclick="openModal('createPlanModal')">
                <i class="bi bi-plus-lg"></i> Add Plan
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display:flex; align-items:center;">
            <i class="bi bi-table" style="color:#6366f1;font-size:18px;margin-right:8px;"></i>
            <span class="card-title">All Plans</span>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Billing Cycle</th>
                        <th>Product Limit</th>
                        <th>User Limit</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td style="font-weight:600;">{{ $plan->name }}</td>
                        <td>${{ number_format($plan->price, 2) }}</td>
                        <td style="text-transform:capitalize;">{{ $plan->billing_cycle }}</td>
                        <td>{{ $plan->limit_products }}</td>
                        <td>{{ $plan->limit_users }}</td>
                        <td>
                            <div class="action-btns" style="justify-content:flex-end;">
                                <button class="btn-icon" title="Edit Plan"
                                    onclick="openEditModal({{ $plan->id }}, '{{ addslashes($plan->name) }}', '{{ $plan->price }}', '{{ $plan->billing_cycle }}', {{ $plan->limit_products }}, {{ $plan->limit_users }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-icon danger" title="Delete Plan"
                                    onclick="confirmDelete({{ $plan->id }}, '{{ addslashes($plan->name) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:48px;color:#94a3b8;">
                            <i class="bi bi-tags" style="font-size:36px;display:block;margin-bottom:10px;"></i>
                            <div>No plans found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())
        <div style="padding:14px 22px;border-top:1px solid #f1f5f9;">
            {{ $plans->links() }}
        </div>
        @endif
    </div>

    {{-- CREATE PLAN MODAL --}}
    <div class="modal-overlay" id="createPlanModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                    <i class="bi bi-plus-lg"></i>
                </div>
                <div>
                    <div class="modal-title">Add New Plan</div>
                    <div class="modal-sub">Create a new pricing tier</div>
                </div>
                <button class="modal-close" onclick="closeModal('createPlanModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('saas.plans.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Plan Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group" style="display:flex;gap:15px;">
                        <div style="flex:1;">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div style="flex:1;">
                            <label class="form-label">Billing Cycle *</label>
                            <select name="billing_cycle" class="form-control" required>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="display:flex;gap:15px;">
                        <div style="flex:1;">
                            <label class="form-label">Max Products *</label>
                            <input type="number" name="limit_products" class="form-control" required>
                        </div>
                        <div style="flex:1;">
                            <label class="form-label">Max Users *</label>
                            <input type="number" name="limit_users" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('createPlanModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Create Plan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT PLAN MODAL --}}
    <div class="modal-overlay" id="editPlanModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:rgba(16,185,129,0.1);color:#10b981;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="modal-title">Edit Plan</div>
                    <div class="modal-sub" id="editPlanSubtitle">Update plan details</div>
                </div>
                <button class="modal-close" onclick="closeModal('editPlanModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" id="editPlanForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Plan Name *</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="form-group" style="display:flex;gap:15px;">
                        <div style="flex:1;">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" name="price" id="editPrice" class="form-control" required>
                        </div>
                        <div style="flex:1;">
                            <label class="form-label">Billing Cycle *</label>
                            <select name="billing_cycle" id="editCycle" class="form-control" required>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="display:flex;gap:15px;">
                        <div style="flex:1;">
                            <label class="form-label">Max Products *</label>
                            <input type="number" name="limit_products" id="editLimitProducts" class="form-control" required>
                        </div>
                        <div style="flex:1;">
                            <label class="form-label">Max Users *</label>
                            <input type="number" name="limit_users" id="editLimitUsers" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('editPlanModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden delete form --}}
    <form id="deletePlanForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <script>
        function openModal(id)  { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        function openEditModal(id, name, price, cycle, limitProd, limitUser) {
            document.getElementById('editPlanForm').action = '/saas/plans/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editPrice').value = price;
            document.getElementById('editCycle').value = cycle;
            document.getElementById('editLimitProducts').value = limitProd;
            document.getElementById('editLimitUsers').value = limitUser;
            document.getElementById('editPlanSubtitle').textContent = 'Editing: ' + name;
            openModal('editPlanModal');
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Delete Plan?',
                html: `Are you sure you want to delete <strong>${name}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete',
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deletePlanForm');
                    form.action = '/saas/plans/' + id;
                    form.submit();
                }
            });
        }
    </script>
</x-layouts.saas>
