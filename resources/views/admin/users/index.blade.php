<x-layouts.admin title="User Management">

    {{-- ── Styles ── --}}
    <style>
        /* ── Page Header ── */
        .page-header {
            display: flex; align-items: center; gap: 16px;
            margin-bottom: 26px;
        }
        .page-header-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: linear-gradient(135deg,#6366f1,#818cf8);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff; flex-shrink: 0;
        }
        .page-header-info h1 { font-size:20px; font-weight:800; color:#0f172a; margin:0; }
        .page-header-info p  { font-size:13px; color:#64748b; margin:3px 0 0; }

        /* ── Filter Bar ── */
        .filter-bar {
            display: flex; gap: 10px; align-items: center;
            flex-wrap: wrap; margin-bottom: 18px;
        }
        .filter-bar .form-control { max-width: 220px; }
        .filter-bar select.form-control { max-width: 170px; }

        /* ── User Table ── */
        .user-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        .user-table th {
            padding: 11px 16px; text-align: left;
            background: #f8fafc; color: #475569;
            font-size: 11.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }
        .user-table td {
            padding: 13px 16px; vertical-align: middle;
            border-bottom: 1px solid #f1f5f9; color: #1e293b;
        }
        .user-table tr:last-child td { border-bottom: none; }
        .user-table tbody tr:hover td { background: #fafbff; }

        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg,#6366f1,#818cf8);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-name  { font-weight: 600; color: #0f172a; }
        .user-email { font-size: 12px; color: #64748b; }

        /* Role Badges */
        .role-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11.5px; font-weight: 600;
            background: rgba(99,102,241,0.1); color: #4f46e5;
        }
        .role-badge.admin    { background: rgba(239,68,68,0.1);   color: #b91c1c; }
        .role-badge.manager  { background: rgba(245,158,11,0.1);  color: #b45309; }
        .role-badge.cashier  { background: rgba(16,185,129,0.1);  color: #047857; }
        .role-badge.no-role  { background: #f1f5f9; color: #94a3b8; }

        .status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #10b981; display: inline-block; margin-right: 5px;
        }

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
        .modal-box.modal-lg { max-width: 700px; }
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

        /* ── Permissions Grid ── */
        .perm-group { margin-bottom: 18px; }
        .perm-group-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.07em; color: #475569;
            margin-bottom: 8px; padding-bottom: 5px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 6px;
        }
        .perm-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .perm-chip {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 8px;
            border: 1.5px solid #e2e8f0; font-size: 12.5px;
            cursor: pointer; background: #f8fafc; color: #475569;
            user-select: none; transition: all 0.15s;
        }
        .perm-chip input[type=checkbox] { display: none; }
        .perm-chip.checked {
            background: rgba(99,102,241,0.08);
            border-color: #6366f1; color: #4f46e5; font-weight: 600;
        }
        .perm-chip:hover { border-color: #6366f1; }

        /* ── Stats Row ── */
        .stats-row {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px; margin-bottom: 22px;
        }
        .stat-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 14px 18px; display: flex; align-items: center; gap: 12px;
        }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .stat-value { font-size: 22px; font-weight: 800; color: #0f172a; }
        .stat-label { font-size: 11.5px; color: #64748b; font-weight: 500; }

        /* ── Password field toggle ── */
        .input-group { position: relative; }
        .input-group .form-control { padding-right: 38px; }
        .input-toggle {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 15px;
        }
        .input-toggle:hover { color: #6366f1; }

        /* ── Pagination ── */
        .pag-wrap { margin-top: 18px; }
    </style>

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div class="page-header-icon"><i class="bi bi-people-fill"></i></div>
        <div class="page-header-info">
            <h1>User Management</h1>
            <p>Manage system users and assign role-based permissions</p>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px;">
            <a href="{{ route('dashboard.roles.index') }}" class="btn btn-outline">
                <i class="bi bi-shield-lock"></i> Manage Roles
            </a>
            <button class="btn btn-primary" onclick="openModal('createUserModal')">
                <i class="bi bi-person-plus-fill"></i> Add User
            </button>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="stats-row">
        @php
            $totalUsers   = $users->total();
            $adminCount   = \App\Models\User::role('Admin')->count();
            $noRoleCount  = \App\Models\User::doesntHave('roles')->count();
            $roleCount    = $roles->count();
        @endphp
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;"><i class="bi bi-people"></i></div>
            <div><div class="stat-value">{{ $totalUsers }}</div><div class="stat-label">Total Users</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;"><i class="bi bi-shield-fill"></i></div>
            <div><div class="stat-value">{{ $adminCount }}</div><div class="stat-label">Admins</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="bi bi-person-badge"></i></div>
            <div><div class="stat-value">{{ $roleCount }}</div><div class="stat-label">Roles Defined</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;"><i class="bi bi-person-x"></i></div>
            <div><div class="stat-value">{{ $noRoleCount }}</div><div class="stat-label">No Role Assigned</div></div>
        </div>
    </div>

    {{-- ── Users Table Card ── --}}
    <div class="card">
        <div class="card-header">
            <i class="bi bi-table" style="color:#6366f1;font-size:18px;"></i>
            <span class="card-title">All Users</span>
            <form method="GET" action="{{ route('dashboard.users.index') }}" class="filter-bar" style="margin-left:auto;margin-bottom:0;">
                <input type="text" name="search" class="form-control" placeholder="Search name or email…" value="{{ request('search') }}" style="max-width:210px;">
                <select name="role" class="form-control" style="max-width:160px;">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                @if(request()->hasAny(['search','role']))
                    <a href="{{ route('dashboard.users.index') }}" class="btn btn-outline btn-sm"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Roles</th>
                        <th>Joined</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td style="color:#94a3b8;font-size:12px;">{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="user-name">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="font-size:10px;background:#e0e7ff;color:#4338ca;padding:1px 6px;border-radius:4px;font-weight:600;margin-left:4px;">You</span>
                                        @endif
                                    </div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @forelse($user->roles as $r)
                                <span class="role-badge {{ strtolower($r->name) === 'admin' ? 'admin' : (str_contains(strtolower($r->name),'manager') ? 'manager' : (str_contains(strtolower($r->name),'cashier') ? 'cashier' : '')) }}">
                                    <i class="bi bi-circle-fill" style="font-size:5px;"></i> {{ $r->name }}
                                </span>
                            @empty
                                <span class="role-badge no-role"><i class="bi bi-dash"></i> No Role</span>
                            @endforelse
                        </td>
                        <td style="color:#64748b;font-size:12.5px;">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="action-btns" style="justify-content:flex-end;">
                                {{-- Assign Roles --}}
                                <button class="btn-icon"
                                    title="Assign Roles"
                                    onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ json_encode($user->roles->pluck('name')) }})">
                                    <i class="bi bi-shield-lock"></i>
                                </button>
                                {{-- Edit User --}}
                                <button class="btn-icon"
                                    title="Edit User"
                                    onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Delete --}}
                                @if($user->id !== auth()->id())
                                <button class="btn-icon danger"
                                    title="Delete User"
                                    onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:48px;color:#94a3b8;">
                            <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:10px;"></i>
                            <div style="font-size:14px;">No users found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div style="padding:14px 22px;border-top:1px solid #f1f5f9;">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- ── CREATE USER MODAL ── --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="modal-overlay" id="createUserModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <div>
                    <div class="modal-title">Add New User</div>
                    <div class="modal-sub">Create a new system account</div>
                </div>
                <button class="modal-close" onclick="closeModal('createUserModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:#ef4444;">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="createPwd" class="form-control" placeholder="Minimum 8 characters" required>
                            <button type="button" class="input-toggle" onclick="togglePwd('createPwd',this)"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span style="color:#ef4444;">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="createPwdConf" class="form-control" placeholder="Re-enter password" required>
                            <button type="button" class="input-toggle" onclick="togglePwd('createPwdConf',this)"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Assign Role</label>
                        <select name="role" class="form-control">
                            <option value="">— No Role —</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('createUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Create User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- ── EDIT USER MODAL ── --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="modal-overlay" id="editUserModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:rgba(16,185,129,0.1);color:#10b981;">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="modal-title">Edit User</div>
                    <div class="modal-sub" id="editUserSubtitle">Update user details</div>
                </div>
                <button class="modal-close" onclick="closeModal('editUserModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" id="editUserForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password <span style="font-size:11px;color:#94a3b8;">(leave blank to keep current)</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="editPwd" class="form-control" placeholder="••••••••">
                            <button type="button" class="input-toggle" onclick="togglePwd('editPwd',this)"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="editPwdConf" class="form-control" placeholder="••••••••">
                            <button type="button" class="input-toggle" onclick="togglePwd('editPwdConf',this)"><i class="bi bi-eye-slash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('editUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- ── ASSIGN ROLES MODAL ── --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="modal-overlay" id="assignRolesModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-header-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <div class="modal-title">Assign Roles</div>
                    <div class="modal-sub" id="assignRolesSubtitle">Select roles for user</div>
                </div>
                <button class="modal-close" onclick="closeModal('assignRolesModal')"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="POST" id="assignRolesForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div style="margin-bottom:14px;font-size:13px;color:#64748b;">
                        <i class="bi bi-info-circle"></i>
                        Select one or more roles. Users inherit all permissions from their assigned roles.
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;" id="rolesGrid">
                        @foreach($roles as $role)
                        <label class="perm-chip" id="roleChip_{{ $role->name }}" onclick="toggleChip(this)">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}">
                            <i class="bi bi-person-badge" style="font-size:14px;"></i>
                            <span>{{ $role->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @if($roles->isEmpty())
                        <div style="text-align:center;padding:24px;color:#94a3b8;font-size:13px;">
                            <i class="bi bi-shield-x" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            No roles defined yet. <a href="{{ route('dashboard.roles.index') }}" style="color:#6366f1;">Create roles first.</a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('assignRolesModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-shield-check"></i> Save Roles</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden delete form --}}
    <form id="deleteUserForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    <script>
        // ── Modal helpers ──
        function openModal(id)  { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        // Close on backdrop click
        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // ── Password toggle ──
        function togglePwd(inputId, btn) {
            const inp = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'bi bi-eye';
            } else {
                inp.type = 'password';
                icon.className = 'bi bi-eye-slash';
            }
        }

        // ── Edit User Modal ──
        function openEditModal(userId, name, email) {
            document.getElementById('editUserForm').action = '/admin/users/' + userId;
            document.getElementById('editName').value  = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editUserSubtitle').textContent = 'Editing: ' + name;
            document.getElementById('editPwd').value     = '';
            document.getElementById('editPwdConf').value = '';
            openModal('editUserModal');
        }

        // ── Assign Roles Modal ──
        function openRoleModal(userId, name, currentRoles) {
            document.getElementById('assignRolesForm').action = '/admin/users/' + userId + '/roles';
            document.getElementById('assignRolesSubtitle').textContent = 'Roles for: ' + name;

            // Reset all chips
            document.querySelectorAll('#rolesGrid .perm-chip').forEach(chip => {
                const cb = chip.querySelector('input[type=checkbox]');
                const isChecked = currentRoles.includes(cb.value);
                cb.checked = isChecked;
                chip.classList.toggle('checked', isChecked);
            });
            openModal('assignRolesModal');
        }

        // ── Permission chip toggle ──
        function toggleChip(chip) {
            const cb = chip.querySelector('input[type=checkbox]');
            cb.checked = !cb.checked;
            chip.classList.toggle('checked', cb.checked);
        }

        // ── Delete confirm ──
        function confirmDelete(userId, name) {
            Swal.fire({
                title: 'Delete User?',
                html: `Are you sure you want to delete <strong>${name}</strong>? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="bi bi-trash"></i> Yes, delete',
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteUserForm');
                    form.action = '/admin/users/' + userId;
                    form.submit();
                }
            });
        }

        // Auto-open create modal if validation failed
        @if($errors->any() && old('_intent') === 'create')
            openModal('createUserModal');
        @endif
    </script>

</x-layouts.admin>
