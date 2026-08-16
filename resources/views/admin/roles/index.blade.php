<x-layouts.admin title="Roles & Permissions">

    <style>
        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .page-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
        }

        .page-header-info h1 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        .page-header-info p {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 0;
        }

        /* ── Create form compact ── */
        .create-row {
            display: flex;
            gap: 8px;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }

        .create-row input.form-control {
            flex: 1;
            height: 36px;
            font-size: 13px;
            padding: 0 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            outline: none;
            transition: border-color 0.15s;
        }

        .create-row input.form-control:focus {
            border-color: #8b5cf6;
        }

        .create-row .btn-create {
            height: 36px;
            padding: 0 16px;
            border-radius: 8px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.15s;
        }

        .create-row .btn-create:hover {
            opacity: 0.9;
        }

        /* ── Roles Table ── */
        .roles-table-wrap {
            background: #fafafa;
            padding-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .roles-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .roles-table thead th {
            background: #f8fafc;
            padding: 9px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .roles-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.1s;
        }

        .roles-table tbody tr:last-child {
            border-bottom: none;
        }

        .roles-table tbody tr:hover {
            background: #fafbff;
        }

        .roles-table td {
            padding: 10px 14px;
            vertical-align: middle;
        }

        /* role name cell */
        .role-name-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-icon-sm {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #fff;
        }

        .role-name-text {
            font-weight: 700;
            color: #0f172a;
        }

        .role-meta-text {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .sys-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
            vertical-align: middle;
            margin-left: 5px;
        }

        /* perm tags in row */
        .perm-tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            max-width: 480px;
        }

        .ptag {
            font-size: 10.5px;
            padding: 2px 7px;
            border-radius: 999px;
            font-weight: 600;
            white-space: nowrap;
        }

        .ptag.view {
            background: rgba(16, 185, 129, 0.10);
            color: #047857;
        }

        .ptag.create {
            background: rgba(99, 102, 241, 0.10);
            color: #4f46e5;
        }

        .ptag.edit {
            background: rgba(245, 158, 11, 0.10);
            color: #b45309;
        }

        .ptag.delete {
            background: rgba(239, 68, 68, 0.10);
            color: #b91c1c;
        }

        .ptag.manage {
            background: rgba(139, 92, 246, 0.10);
            color: #7c3aed;
        }

        .ptag.other {
            background: rgba(100, 116, 139, 0.10);
            color: #475569;
        }

        .perm-more {
            font-size: 10.5px;
            color: #94a3b8;
            padding: 2px 4px;
            align-self: center;
            white-space: nowrap;
        }

        .no-perms-txt {
            font-size: 12px;
            color: #94a3b8;
            font-style: italic;
        }

        /* actions column */
        .row-actions {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
        }

        .btn-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-icon:hover {
            background: #6366f1;
            border-color: #6366f1;
            color: #fff;
        }

        .btn-icon.danger:hover {
            background: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }

        /* empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state i {
            font-size: 36px;
            color: #cbd5e1;
            display: block;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 13px;
            color: #94a3b8;
            margin: 0;
        }

        /* ── Modal Perfected Scrolling & View ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            padding: 16px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 680px;
            max-height: 88vh;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
            animation: modalIn 0.2s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(-8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
            flex-shrink: 0;
        }

        .modal-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .modal-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .modal-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 1px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #94a3b8;
            cursor: pointer;
            margin-left: auto;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .modal-close:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        /* Form styling inside modal */
        #permForm {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .modal-body {
            padding: 18px 20px;
            overflow-y: auto;
            flex: 1;
            min-height: 0;
        }

        /* Custom scrollbar for modal body */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .modal-footer {
            padding: 14px 20px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fff;
            flex-shrink: 0;
        }

        .selected-counter {
            font-size: 12px;
            font-weight: 600;
            color: #6366f1;
            background: rgba(99, 102, 241, 0.08);
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* Permission filter & tools */
        .modal-tools {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 14px;
        }

        .perm-search-wrap {
            position: relative;
            flex: 1;
        }

        .perm-search-wrap i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        .perm-search-input {
            width: 100%;
            height: 34px;
            padding: 0 10px 0 30px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.15s;
        }

        .perm-search-input:focus {
            border-color: #6366f1;
        }

        .btn-global-toggle {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 0 10px;
            height: 34px;
            border-radius: 8px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s;
        }

        .btn-global-toggle:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Permission groups in modal */
        .perm-group {
            margin-bottom: 16px;
        }

        .perm-group:last-child {
            margin-bottom: 0;
        }

        .perm-group-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #475569;
            padding: 6px 10px;
            background: #f8fafc;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .group-toggle {
            font-size: 10.5px;
            color: #6366f1;
            cursor: pointer;
            font-weight: 600;
            margin-left: auto;
            user-select: none;
        }

        .group-toggle:hover {
            text-decoration: underline;
        }

        .perm-chips-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .perm-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 12px;
            cursor: pointer;
            background: #f8fafc;
            color: #475569;
            user-select: none;
            transition: all 0.12s;
        }

        .perm-chip input[type=checkbox] {
            display: none;
        }

        .perm-chip.checked {
            background: rgba(99, 102, 241, 0.08);
            border-color: #6366f1;
            color: #4f46e5;
            font-weight: 600;
        }

        .perm-chip:hover {
            border-color: #a5b4fc;
        }

        .info-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }
    </style>

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="page-header-info">
            <h1>Roles &amp; Permissions</h1>
            <p>Define roles and assign granular permissions</p>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline" style="font-size:12.5px;padding:6px 14px;">
                <i class="bi bi-people"></i> Manage Users
            </a>
        </div>
    </div>

    {{-- ── Create Role – compact inline bar ── --}}
    <form method="POST" action="{{ route('dashboard.roles.store') }}">
        @csrf
        <div class="create-row">
            <i class="bi bi-shield-plus" style="color:#8b5cf6;font-size:16px;flex-shrink:0;"></i>
            <input type="text" name="name" class="form-control"
                placeholder="New role name (e.g. Manager, Cashier, Accountant)" required>
            <button type="submit" class="btn-create">
                <i class="bi bi-plus-lg"></i> Create Role
            </button>
        </div>
    </form>

    {{-- ── Roles Table ── --}}
    <div class="roles-table-wrap">
        @if ($roles->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield-x"></i>
                <p>No roles defined yet. Create your first role above.</p>
            </div>
        @else
            <table class="roles-table">
                <thead>
                    <tr>
                        <th style="width:200px;">Role</th>
                        <th>Permissions</th>
                        <th style="width:80px;text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        @php
                            $rolePerms = $role->permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
                            $isSystem = in_array($role->name, ['Admin', 'super-admin']);
                            $allPerms = $role->permissions;
                            $shown = $allPerms->take(10);
                            $extra = $allPerms->count() - $shown->count();
                        @endphp
                        <tr>
                            {{-- Name column --}}
                            <td>
                                <div class="role-name-cell">
                                    <div class="role-icon-sm"><i class="bi bi-person-badge-fill"></i></div>
                                    <div>
                                        <div class="role-name-text">
                                            {{ $role->name }}
                                            @if ($isSystem)
                                                <span class="sys-badge">System</span>
                                            @endif
                                        </div>
                                        <div class="role-meta-text">
                                            {{ $role->permissions->count() }}
                                            perm{{ $role->permissions->count() !== 1 ? 's' : '' }}
                                            &bull; {{ $role->users()->count() }}
                                            user{{ $role->users()->count() !== 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Permissions column --}}
                            <td>
                                @if ($allPerms->isEmpty())
                                    <span class="no-perms-txt">No permissions — click edit to assign</span>
                                @else
                                    <div class="perm-tag-row">
                                        @foreach ($shown as $perm)
                                            @php $action = explode('.', $perm->name)[1] ?? 'other'; @endphp
                                            <span
                                                class="ptag {{ in_array($action, ['view', 'create', 'edit', 'delete', 'manage']) ? $action : 'other' }}">
                                                {{ $perm->name }}
                                            </span>
                                        @endforeach
                                        @if ($extra > 0)
                                            <span class="perm-more">+{{ $extra }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            {{-- Actions column --}}
                            <td>
                                <div class="row-actions">
                                    <button class="btn-icon" title="Edit Permissions"
                                        onclick="openPermModal({{ $role->id }}, '{{ addslashes($role->name) }}', {{ json_encode($role->permissions->pluck('name')) }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if (!$isSystem)
                                        <button class="btn-icon danger" title="Delete Role"
                                            onclick="confirmDeleteRole({{ $role->id }}, '{{ addslashes($role->name) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ── Edit Permissions Modal ── --}}
    <div class="modal-overlay" id="permModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="bi bi-shield-lock-fill"></i></div>
                <div>
                    <div class="modal-title">Edit Permissions</div>
                    <div class="modal-sub" id="permModalSubtitle">Assign permissions to this role</div>
                </div>
                <button type="button" class="modal-close" onclick="closeModal('permModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" id="permForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="info-banner">
                        <i class="bi bi-info-circle-fill"></i>
                        Users assigned to this role will automatically inherit these permissions.
                    </div>

                    {{-- Search & Global Toggle Tool --}}
                    <div class="modal-tools">
                        <div class="perm-search-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="perm-search-input" id="permSearchInput"
                                placeholder="Search permissions..." onkeyup="filterPermissions(this.value)">
                        </div>
                        <button type="button" class="btn-global-toggle" id="globalToggleBtn" onclick="toggleAllPermissions()">
                            Select All
                        </button>
                    </div>

                    @foreach ($groupedPermissions as $group => $perms)
                        <div class="perm-group" data-group-name="{{ strtolower($group) }}">
                            <div class="perm-group-title">
                                <i class="bi bi-folder2-open"></i>
                                {{ $groupLabels[$group] ?? ucfirst($group) }}
                                <span class="group-toggle" onclick="toggleGroup(this, '{{ $group }}')">Select All</span>
                            </div>
                            <div class="perm-chips-grid">
                                @foreach ($perms as $perm)
                                    <label class="perm-chip" data-group="{{ $group }}" data-perm-name="{{ strtolower($perm->name) }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" onchange="handleCheckboxChange(this)">
                                        <i class="bi bi-{{ str_contains($perm->name, 'view') ? 'eye' : (str_contains($perm->name, 'create') ? 'plus-circle' : (str_contains($perm->name, 'delete') ? 'trash' : (str_contains($perm->name, 'manage') ? 'sliders' : 'pencil'))) }}"
                                            style="font-size:11px;"></i>
                                        <span>{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if ($permissions->isEmpty())
                        <div style="text-align:center;padding:28px;color:#94a3b8;">
                            <i class="bi bi-key" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                            <div style="font-size:12px;">No permissions found. Run <code>php artisan db:seed</code> to seed permissions.</div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <span class="selected-counter" id="selectedCounter">0 selected</span>
                    <div style="display:flex;gap:8px;">
                        <button type="button" class="btn btn-outline" style="font-size:12.5px;padding:6px 14px;"
                            onclick="closeModal('permModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-size:12.5px;padding:6px 14px;"><i
                                class="bi bi-shield-check"></i> Save Permissions</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden delete form --}}
    <form id="deleteRoleForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        function handleCheckboxChange(cb) {
            const chip = cb.closest('.perm-chip');
            if (chip) {
                chip.classList.toggle('checked', cb.checked);
            }
            updateCounts();
        }

        function openPermModal(roleId, roleName, currentPerms) {
            document.getElementById('permForm').action = '/admin/roles/' + roleId + '/permissions';
            document.getElementById('permModalSubtitle').textContent = 'Editing: ' + roleName;

            // Reset search input
            const searchInput = document.getElementById('permSearchInput');
            if (searchInput) {
                searchInput.value = '';
                filterPermissions('');
            }

            document.querySelectorAll('#permForm .perm-chip').forEach(chip => {
                const cb = chip.querySelector('input[type=checkbox]');
                const isChecked = currentPerms.includes(cb.value);
                cb.checked = isChecked;
                chip.classList.toggle('checked', isChecked);
            });

            updateCounts();
            openModal('permModal');
        }

        function toggleGroup(toggleEl, group) {
            const chips = document.querySelectorAll(`#permForm .perm-chip[data-group="${group}"]`);
            const allChecked = [...chips].every(c => c.querySelector('input[type=checkbox]').checked);
            chips.forEach(chip => {
                const cb = chip.querySelector('input[type=checkbox]');
                cb.checked = !allChecked;
                chip.classList.toggle('checked', !allChecked);
            });
            updateCounts();
        }

        function toggleAllPermissions() {
            const chips = document.querySelectorAll('#permForm .perm-chip');
            const visibleChips = [...chips].filter(c => c.style.display !== 'none');
            const allChecked = visibleChips.every(c => c.querySelector('input[type=checkbox]').checked);
            
            visibleChips.forEach(chip => {
                const cb = chip.querySelector('input[type=checkbox]');
                cb.checked = !allChecked;
                chip.classList.toggle('checked', !allChecked);
            });
            updateCounts();
        }

        function updateCounts() {
            // Update per-group toggle labels
            document.querySelectorAll('.group-toggle').forEach(el => {
                const groupTitle = el.closest('.perm-group-title');
                if (!groupTitle) return;
                const group = groupTitle.nextElementSibling;
                if (!group) return;
                const chips = group.querySelectorAll('.perm-chip');
                const checked = [...chips].filter(c => c.querySelector('input[type=checkbox]').checked).length;
                el.textContent = (checked === chips.length && chips.length > 0) ? 'Deselect All' : 'Select All';
            });

            // Update global counter
            const totalChecked = document.querySelectorAll('#permForm input[type=checkbox]:checked').length;
            const counterEl = document.getElementById('selectedCounter');
            if (counterEl) {
                counterEl.textContent = totalChecked + ' selected';
            }

            // Update global toggle button label
            const globalBtn = document.getElementById('globalToggleBtn');
            if (globalBtn) {
                const totalVisible = [...document.querySelectorAll('#permForm .perm-chip')].filter(c => c.style.display !== 'none').length;
                const visibleChecked = [...document.querySelectorAll('#permForm .perm-chip')].filter(c => c.style.display !== 'none' && c.querySelector('input[type=checkbox]').checked).length;
                globalBtn.textContent = (visibleChecked === totalVisible && totalVisible > 0) ? 'Deselect All' : 'Select All';
            }
        }

        function filterPermissions(query) {
            const q = query.toLowerCase().trim();
            document.querySelectorAll('#permForm .perm-group').forEach(groupEl => {
                let hasVisibleChip = false;
                groupEl.querySelectorAll('.perm-chip').forEach(chip => {
                    const name = chip.getAttribute('data-perm-name');
                    if (!q || name.includes(q)) {
                        chip.style.display = 'inline-flex';
                        hasVisibleChip = true;
                    } else {
                        chip.style.display = 'none';
                    }
                });
                groupEl.style.display = hasVisibleChip ? 'block' : 'none';
            });
            updateCounts();
        }

        function confirmDeleteRole(roleId, roleName) {
            Swal.fire({
                title: 'Delete Role?',
                html: `Deleting <strong>${roleName}</strong> will remove it from all users. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete it',
            }).then(result => {
                if (result.isConfirmed) {
                    const f = document.getElementById('deleteRoleForm');
                    f.action = '/admin/roles/' + roleId;
                    f.submit();
                }
            });
        }
    </script>

</x-layouts.admin>
