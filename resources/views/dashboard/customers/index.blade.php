<x-layouts.admin title="Customers Directory">

    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Loyalty Customers</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage store loyalty programs, customer contact cards, and order counts.</p>
        </div>
        <a href="{{ route('dashboard.customers.create') }}" class="btn btn-primary" style="margin-left:auto;text-decoration:none;">
            <i class="bi bi-person-plus"></i> Register Customer
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.customers') }}" style="display:flex;gap:12px;align-items:center;margin:0;">
                <div style="flex:1;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search customers by name, phone, email, address..." style="padding-left:36px;">
                    <i class="bi bi-search" style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <button type="submit" class="btn btn-outline btn-sm">Search</button>
                @if(request()->filled('search'))
                    <a href="{{ route('dashboard.customers') }}" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fecaca;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:16px 20px;">Customer Details</th>
                        <th style="padding:16px 20px;">Phone</th>
                        <th style="padding:16px 20px;">Email</th>
                        <th style="padding:16px 20px;text-align:center;">Loyalty Points</th>
                        <th style="padding:16px 20px;text-align:right;">Total Purchased</th>
                        <th style="padding:16px 20px;text-align:right;">Due Balance</th>
                        <th style="padding:16px 20px;text-align:center;">Status</th>
                        <th style="padding:16px 20px;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @forelse($customers as $customer)
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 20px;font-weight:700;color:#0f172a;">
                            <a href="{{ route('dashboard.customers.show', $customer) }}" style="color:#6366f1;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                {{ $customer->name }}
                            </a>
                            @if($customer->date_of_birth)
                                <small style="display:block;font-weight:400;color:#64748b;">DOB: {{ $customer->date_of_birth->format('d M, Y') }}</small>
                            @endif
                        </td>
                        <td style="padding:14px 20px;font-weight:600;">
                            @if($customer->phone)
                                <i class="bi bi-telephone-fill" style="color:#94a3b8;margin-right:6px;"></i>{{ $customer->phone }}
                            @else
                                <span style="color:#94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;color:#64748b;">
                            @if($customer->email)
                                <i class="bi bi-envelope-fill" style="color:#94a3b8;margin-right:6px;"></i>{{ $customer->email }}
                            @else
                                <span style="color:#94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <span style="background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:999px;font-weight:700;font-size:12px;border:1px solid #bbf7d0;display:inline-block;">
                                <i class="bi bi-star-fill" style="color:#eab308;"></i> {{ $customer->loyalty_points }} pts
                            </span>
                        </td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;color:#0f172a;">৳ {{ number_format($customer->total_spent, 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;color:{{ $customer->due_balance > 0 ? '#ef4444' : '#64748b' }};">
                            @if($customer->due_balance > 0)
                                ৳ {{ number_format($customer->due_balance, 2) }}
                            @else
                                <span style="color:#94a3b8;">৳ 0.00</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <span style="background:{{ $customer->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $customer->is_active ? '#15803d' : '#475569' }};padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding:14px 20px;text-align:center;font-size:16px;">
                            <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                <a href="{{ route('dashboard.customers.show', $customer) }}" style="color:#0ea5e9;" title="View Details"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('dashboard.customers.edit', $customer) }}" style="color:#6366f1;" title="Edit Customer"><i class="bi bi-pencil-square"></i></a>
                                @if($customer->phone !== '0000000000')
                                    <form method="POST" action="{{ route('dashboard.customers.destroy', $customer) }}" onsubmit="return confirm('Are you sure you want to delete this customer?')" style="margin:0;display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;"><i class="bi bi-trash"></i></button>
                                    </form>
                                @else
                                    <span style="color:#cbd5e1;cursor:not-allowed;" title="Cannot delete Walk-in Customer"><i class="bi bi-trash"></i></span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding:48px;text-align:center;color:#64748b;">
                            <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                            No loyalty customers registered.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
        {{ $customers->links() }}
    </div>

</x-layouts.admin>
