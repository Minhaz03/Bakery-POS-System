<x-layouts.admin title="Custom Bakery Orders">

    <div x-data="{ showModal: {{ request('openModal') ? 'true' : 'false' }} }" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Custom Orders</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage custom cake requests, delivery dates, client specifications, and down payments.</p>
        </div>
        <button class="btn btn-primary" @click="showModal = true" style="margin-left:auto;">
            <i class="bi bi-calendar-plus"></i> New Custom Order
        </button>

        <!-- Create Custom Order Modal -->
        <div x-show="showModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;">
            <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:20px;">
                <div class="card" @click.outside="showModal = false" style="width:100%;max-width:600px;box-shadow:0 10px 25px rgba(0,0,0,0.2);max-height:90vh;display:flex;flex-direction:column;">
                    <div class="card-header" style="justify-content:space-between;padding:16px 20px;flex-shrink:0;">
                        <span style="font-weight:700;font-size:16px;">New Custom Order</span>
                        <button @click="showModal = false" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;"><i class="bi bi-x"></i></button>
                    </div>
                    <form action="{{ route('dashboard.custom-orders.store') }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;">
                        @csrf
                        <div class="card-body" style="padding:20px;overflow-y:auto;">
                            <div class="form-group">
                                <label class="form-label">Customer <span style="color:var(--danger)">*</span></label>
                                <select name="customer_id" class="form-control" onchange="document.getElementById('customer_name_input').style.display = this.value ? 'none' : 'block'">
                                    <option value="">Walk-in / Enter Name Manually...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})</option>
                                    @endforeach
                                </select>
                                <input type="text" id="customer_name_input" name="customer_name" class="form-control" placeholder="Customer Name" style="margin-top:8px;">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Specifications / Details <span style="color:var(--danger)">*</span></label>
                                <textarea name="details" class="form-control" rows="4" placeholder="Cake flavor, writing, theme, dietary requirements..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Delivery Date <span style="color:var(--danger)">*</span></label>
                                <input type="date" name="delivery_date" class="form-control" required>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Total Cost <span style="color:var(--danger)">*</span></label>
                                    <div style="position:relative;">
                                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;">৳</span>
                                        <input type="number" step="0.01" name="total_price" class="form-control" style="padding-left:28px;" required>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Advance Payment <span style="color:var(--danger)">*</span></label>
                                    <div style="position:relative;">
                                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;">৳</span>
                                        <input type="number" step="0.01" name="advance_payment" class="form-control" style="padding-left:28px;" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;flex-shrink:0;">
                            <button type="button" class="btn btn-outline" @click="showModal = false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Orders Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:16px 20px;">Order ID</th>
                        <th style="padding:16px 20px;">Customer</th>
                        <th style="padding:16px 20px;">Specifications / Details</th>
                        <th style="padding:16px 20px;text-align:right;">Total Cost</th>
                        <th style="padding:16px 20px;text-align:right;">Advance Paid</th>
                        <th style="padding:16px 20px;text-align:center;">Status</th>
                        <th style="padding:16px 20px;">Delivery Date</th>
                        <th style="padding:16px 20px;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @foreach($orders as $order)
                    <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 20px;font-weight:700;color:#0f172a;font-family:monospace;">{{ $order['id'] }}</td>
                        <td style="padding:14px 20px;font-weight:600;color:#334155;">{{ $order['customer'] }}</td>
                        <td style="padding:14px 20px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $order['details'] }}">
                            <i class="bi bi-chat-text" style="color:#94a3b8;margin-right:4px;"></i>{{ $order['details'] }}
                        </td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;color:#0f172a;">৳ {{ number_format($order['price'], 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;color:#16a34a;">৳ {{ number_format($order['advance'], 2) }}</td>
                        <td style="padding:14px 20px;text-align:center;">
                            @if($order['status'] === 'Confirmed')
                                <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;">Confirmed</span>
                            @elseif($order['status'] === 'In Progress')
                                <span style="background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;">In Progress</span>
                            @elseif($order['status'] === 'Completed')
                                <span style="background:#f3e8ff;color:#7e22ce;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;">Completed</span>
                            @elseif($order['status'] === 'Cancelled')
                                <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;">Cancelled</span>
                            @else
                                <span style="background:#fef3c7;color:#d97706;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;">{{ $order['status'] }}</span>
                            @endif
                        </td>
                        <td style="padding:14px 20px;font-weight:600;color:#4f46e5;"><i class="bi bi-calendar-event"></i> {{ $order['delivery_date'] }}</td>
                        <td style="padding:14px 20px;text-align:center;font-size:16px;">
                            <a href="{{ route('dashboard.custom-orders.show', $order['real_id']) }}" style="color:#10b981;margin-right:12px;" title="View Details"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('dashboard.custom-orders.print', $order['real_id']) }}" target="_blank" style="color:#6366f1;margin-right:12px;" title="Print Slip"><i class="bi bi-printer"></i></a>
                            
                            @if($order['status'] !== 'Cancelled')
                                <form id="form-cancel-{{ $order['real_id'] }}" action="{{ route('dashboard.custom-orders.cancel', $order['real_id']) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" onclick="confirmCancel({{ $order['real_id'] }}, '{{ $order['id'] }}')" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:0;" title="Cancel Order"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmCancel(realId, orderId) {
            Swal.fire({
                title: 'Cancel Order?',
                html: `<p style="color:#475569;font-size:14px;">Are you sure you want to cancel Custom Order <strong style="font-family:monospace;">${orderId}</strong>?</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Cancel it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-cancel-${realId}`).submit();
                }
            });
        }
    </script>

</x-layouts.admin>
