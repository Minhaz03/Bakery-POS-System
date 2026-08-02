<x-layouts.saas title="Global Users">

    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px; }
        .page-title-wrap { display: flex; align-items: center; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .header-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #fcd34d, #f59e0b); color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.5); }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; align-items: center; gap: 10px; }
        .card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        
        .table-responsive { width: 100%; overflow-x: auto; }
        .table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table th { background: #f8fafc; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; padding: 14px 24px; text-align: left; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; }
        .table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; vertical-align: middle; }
        .table tr:last-child td { border-bottom: none; }
        
        .user-avatar { width: 36px; height: 36px; border-radius: 10px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; border: 1px solid #fef3c7; }
        .tenant-badge { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; font-size: 12.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; text-decoration: none; }
        .btn-outline { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
        .btn-outline:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { background: #4f46e5; box-shadow: 0 4px 12px rgba(99,102,241,0.2); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .filter-bar { display: flex; gap: 12px; align-items: center; }
        .form-control { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13.5px; width: 100%; outline: none; transition: border-color 0.2s; }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <div class="header-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <h1>Global Users</h1>
                <p>Manage and monitor all users across all tenants.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-person-lines-fill" style="color:#6366f1;font-size:18px;"></i>
            <span class="card-title">All Users</span>
            <form method="GET" action="{{ route('saas.users.index') }}" class="filter-bar" style="margin-left:auto;margin-bottom:0;">
                <input type="text" name="search" class="form-control" placeholder="Search name, email, tenant..." value="{{ request('search') }}" style="max-width:250px;">
                <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-search"></i> Search</button>
                @if(request()->has('search'))
                    <a href="{{ route('saas.users.index') }}" class="btn btn-outline btn-sm" style="color:#ef4444;"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email Address</th>
                        <th>Associated Tenant</th>
                        <th>Registration Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div style="font-weight:700;color:#0f172a;">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="tenant-badge">
                                <i class="bi bi-shop"></i> {{ $user->tenant->name ?? 'No Tenant' }}
                            </span>
                        </td>
                        <td>
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:8px; justify-content:flex-end;">
                                <form action="{{ route('saas.impersonate', $user->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Log in as {{ $user->name }}?')">
                                        <i class="bi bi-box-arrow-in-right"></i> Login As
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:48px;color:#64748b;">
                            <i class="bi bi-people" style="font-size:36px;color:#cbd5e1;display:block;margin-bottom:12px;"></i>
                            No users found matching your search.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div style="padding:16px 24px;border-top:1px solid #f1f5f9;">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</x-layouts.saas>
