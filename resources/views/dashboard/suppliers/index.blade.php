<x-layouts.admin title="Suppliers">

    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Suppliers Directory</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage wholesale vendor contacts, addresses, and
                pending financial ledger balances.</p>
        </div>
        <a href="{{ route('dashboard.suppliers.create') }}" class="btn btn-primary"
            style="margin-left:auto;text-decoration:none;">
            <i class="bi bi-plus-circle"></i> Add New Supplier
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.suppliers') }}"
                style="display:flex;gap:12px;align-items:center;margin:0;">
                <div style="flex:1;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search suppliers by name, contact, city, phone..." style="padding-left:36px;">
                    <i class="bi bi-search"
                        style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <button type="submit" class="btn btn-outline btn-sm">Search</button>
                @if (request()->filled('search'))
                    <a href="{{ route('dashboard.suppliers') }}" class="btn btn-outline btn-sm"
                        style="color:#ef4444;border-color:#fecaca;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:16px 20px;">ID</th>
                        <th style="padding:16px 20px;">Supplier Company</th>
                        <th style="padding:16px 20px;">Contact Person</th>
                        <th style="padding:16px 20px;">Phone & Email</th>
                        <th style="padding:16px 20px;">Address & City</th>
                        <th style="padding:16px 20px;text-align:right;">Opening Balance</th>
                        <th style="padding:16px 20px;text-align:right;">Current Balance</th>
                        <th style="padding:16px 20px;text-align:center;">Status</th>
                        <th style="padding:16px 20px;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @forelse($suppliers as $supplier)
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
                            onmouseover="this.style.background='#f8fafc'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 20px;font-weight:600;color:#64748b;">
                                #SPL-{{ sprintf('%03d', $supplier->id) }}</td>
                            <td style="padding:14px 20px;font-weight:700;color:#0f172a;">{{ $supplier->name }}</td>
                            <td style="padding:14px 20px;">{{ $supplier->contact_person ?? 'N/A' }}</td>
                            <td style="padding:14px 20px;">
                                @if ($supplier->phone)
                                    <div style="font-weight:600;color:#0f172a;"><i class="bi bi-telephone"
                                            style="color:#64748b;"></i> {{ $supplier->phone }}</div>
                                @endif
                                @if ($supplier->email)
                                    <div style="font-size:12px;color:#64748b;"><i class="bi bi-envelope"
                                            style="color:#64748b;"></i> {{ $supplier->email }}</div>
                                @endif
                                @if (!$supplier->phone && !$supplier->email)
                                    <span style="color:#94a3b8;">No contact details</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px;">
                                @if ($supplier->address)
                                    <span><i class="bi bi-geo-alt" style="color:#64748b;"></i>
                                        {{ $supplier->address }}</span>
                                    @if ($supplier->city)
                                        <small style="color:#64748b;display:block;">{{ $supplier->city }}</small>
                                    @endif
                                @elseif($supplier->city)
                                    <span><i class="bi bi-geo-alt" style="color:#64748b;"></i>
                                        {{ $supplier->city }}</span>
                                @else
                                    <span style="color:#94a3b8;">No address info</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px;text-align:right;">৳
                                {{ number_format($supplier->opening_balance, 2) }}</td>
                            @php
                                $balance = $supplier->current_balance;
                                if (is_null($balance)) {
                                    $balanceColor = '#eab308'; // Yellow
                                    $balanceText = 'N/A';
                                } elseif ($balance < 0) {
                                    $balanceColor = '#ef4444'; // Red
                                    $balanceText = '৳ ' . number_format($balance, 2);
                                } elseif ($balance > 0) {
                                    $balanceColor = '#10b981'; // Green
                                    $balanceText = '৳ ' . number_format($balance, 2);
                                } else {
                                    $balanceColor = '#0f172a'; // Default slate/black for 0
                                    $balanceText = '৳ ' . number_format($balance, 2);
                                }
                            @endphp
                            <td style="padding:14px 20px;text-align:right;font-weight:700;color:{{ $balanceColor }}">
                                {{ $balanceText }}
                            </td>
                            <td style="padding:14px 20px;text-align:center;">
                                <span
                                    style="background:{{ $supplier->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $supplier->is_active ? '#15803d' : '#475569' }};padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">
                                    {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="padding:14px 20px;text-align:center;font-size:16px;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                    <a href="{{ route('dashboard.suppliers.show', $supplier) }}" style="color:#0ea5e9;"
                                        title="View Supplier"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('dashboard.suppliers.edit', $supplier) }}" style="color:#6366f1;"
                                        title="Edit Supplier"><i class="bi bi-pencil-square"></i></a>
                                    <form method="POST" action="{{ route('dashboard.suppliers.destroy', $supplier) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this supplier?')"
                                        style="margin:0;display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;"><i
                                                class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:48px;text-align:center;color:#64748b;">
                                <i class="bi bi-people" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                                No suppliers registered.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
        {{ $suppliers->links() }}
    </div>

</x-layouts.admin>
