<x-layouts.admin title="Sales Invoice Registry">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Sales Invoice Registry</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Browse and audit completed sales invoices, payment formats, and customer records.</p>
        </div>
        <a href="{{ route('dashboard.pos-terminal') }}" class="btn btn-primary" style="text-decoration:none;">
            <i class="bi bi-calculator"></i> Open POS Terminal
        </a>
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.sales') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:0;">
                <div style="flex:1;min-width:220px;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by invoice no. or customer name..." style="padding-left:36px;">
                    <i class="bi bi-search" style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <select name="payment_method" class="form-control" style="width:180px;cursor:pointer;" onchange="this.form.submit()">
                    <option value="">All Payment Methods</option>
                    <option value="cash"       {{ request('payment_method') === 'cash'       ? 'selected' : '' }}>Cash</option>
                    <option value="card"       {{ request('payment_method') === 'card'       ? 'selected' : '' }}>Card</option>
                    <option value="mobile_pay" {{ request('payment_method') === 'mobile_pay' ? 'selected' : '' }}>Mobile Pay</option>
                    <option value="credit"     {{ request('payment_method') === 'credit'     ? 'selected' : '' }}>Credit</option>
                </select>
                <select name="status" class="form-control" style="width:160px;cursor:pointer;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="refunded"  {{ request('status') === 'refunded'  ? 'selected' : '' }}>Refunded</option>
                    <option value="voided"    {{ request('status') === 'voided'    ? 'selected' : '' }}>Voided</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                @if(request()->anyFilled(['search', 'payment_method', 'status']))
                    <a href="{{ route('dashboard.sales') }}" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fecaca;text-decoration:none;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:16px 20px;">Invoice No</th>
                        <th style="padding:16px 20px;">Customer</th>
                        <th style="padding:16px 20px;text-align:center;">Items</th>
                        <th style="padding:16px 20px;text-align:right;">Subtotal</th>
                        <th style="padding:16px 20px;text-align:right;">Discount</th>
                        <th style="padding:16px 20px;text-align:right;">Tax</th>
                        <th style="padding:16px 20px;text-align:right;">Grand Total</th>
                        <th style="padding:16px 20px;text-align:center;">Payment</th>
                        <th style="padding:16px 20px;text-align:center;">Status</th>
                        <th style="padding:16px 20px;">Date</th>
                        <th style="padding:16px 20px;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @forelse($sales as $sale)
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 20px;font-weight:700;color:#0f172a;font-family:monospace;">{{ $sale->invoice_no }}</td>
                        <td style="padding:14px 20px;font-weight:600;color:#334155;">
                            {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            <span style="background:#eff6ff;color:#1d4ed8;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;">{{ $sale->items_count }} item{{ $sale->items_count !== 1 ? 's' : '' }}</span>
                        </td>
                        <td style="padding:14px 20px;text-align:right;color:#475569;">৳ {{ number_format($sale->subtotal, 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;color:#dc2626;">
                            @if($sale->discount_amount > 0) -৳ {{ number_format($sale->discount_amount, 2) }} @else <span style="color:#94a3b8;">—</span> @endif
                        </td>
                        <td style="padding:14px 20px;text-align:right;color:#64748b;">
                            @if($sale->tax_amount > 0) ৳ {{ number_format($sale->tax_amount, 2) }} @else <span style="color:#94a3b8;">—</span> @endif
                        </td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;color:#0f172a;">৳ {{ number_format($sale->grand_total, 2) }}</td>
                        <td style="padding:14px 20px;text-align:center;">
                            @if($sale->payment_method === 'cash')
                                <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;display:inline-block;"><i class="bi bi-cash"></i> Cash</span>
                            @elseif($sale->payment_method === 'card')
                                <span style="background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;display:inline-block;"><i class="bi bi-credit-card"></i> Card</span>
                            @elseif($sale->payment_method === 'mobile_pay')
                                <span style="background:#faf5ff;color:#6b21a8;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;display:inline-block;"><i class="bi bi-phone"></i> Mobile Pay</span>
                            @else
                                <span style="background:#fff7ed;color:#c2410c;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;display:inline-block;"><i class="bi bi-person-lines-fill"></i> Credit</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;text-align:center;">
                            @if($sale->status === 'completed')
                                <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-check-circle-fill"></i> Completed</span>
                            @elseif($sale->status === 'partial')
                                <span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-exclamation-circle-fill"></i> Partially Paid</span>
                            @elseif($sale->status === 'due')
                                <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-x-circle-fill"></i> Due (Unpaid)</span>
                            @elseif($sale->status === 'refunded')
                                <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-arrow-counterclockwise"></i> Refunded</span>
                            @else
                                <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-slash-circle"></i> Voided</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;color:#64748b;white-space:nowrap;">
                            <i class="bi bi-clock"></i> {{ $sale->sale_date->format('d M Y') }}<br>
                            <span style="font-size:11px;color:#94a3b8;">{{ $sale->created_at->format('h:i A') }}</span>
                        </td>
                        <td style="padding:14px 20px;text-align:center;font-size:16px;">
                            <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                <a href="{{ route('dashboard.sales.show', $sale) }}" style="color:#0ea5e9;" title="View Invoice"><i class="bi bi-eye"></i></a>
                                @if($sale->status === 'completed')
                                    <a href="{{ route('dashboard.sales.edit', $sale) }}" style="color:#6366f1;" title="Edit Sale"><i class="bi bi-pencil-square"></i></a>
                                @else
                                    <span style="color:#cbd5e1;cursor:not-allowed;" title="Cannot edit voided/refunded sales"><i class="bi bi-pencil-square"></i></span>
                                @endif
                                <form id="delete-form-{{ $sale->id }}" method="POST" action="{{ route('dashboard.sales.destroy', $sale) }}" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" onclick="confirmDelete('{{ $sale->id }}', '{{ $sale->invoice_no }}')" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;" title="Delete / Void Invoice"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="padding:60px;text-align:center;color:#64748b;">
                            <i class="bi bi-receipt" style="font-size:40px;display:block;margin-bottom:12px;color:#cbd5e1;"></i>
                            <div style="font-size:15px;font-weight:600;color:#94a3b8;margin-bottom:6px;">No sales invoices found</div>
                            <div style="font-size:13px;">Sales will appear here after completing a transaction in the POS Terminal.</div>
                            <a href="{{ route('dashboard.pos-terminal') }}" class="btn btn-primary" style="text-decoration:none;margin-top:16px;"><i class="bi bi-calculator"></i> Open POS Terminal</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px;">
        {{ $sales->links() }}
    </div>

    <script>
        function confirmDelete(id, invoiceNo) {
            Swal.fire({
                title: 'Void this Invoice?',
                html: `<p style="color:#475569;font-size:14px;">Invoice <strong style="font-family:monospace;color:#0f172a;">${invoiceNo}</strong> will be permanently removed from the registry.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, void it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

</x-layouts.admin>
