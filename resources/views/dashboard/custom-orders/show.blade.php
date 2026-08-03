<x-layouts.admin title="Order Details - {{ $order->order_number }}">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Custom Order: <span style="font-family:monospace;color:#6366f1;">{{ $order->order_number }}</span></h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Review order specifications, payment details, and update status.</p>
        </div>
        <div style="display:flex;gap:12px;">
            <a href="{{ route('dashboard.custom-orders.print', $order->id) }}" target="_blank" class="btn btn-outline">
                <i class="bi bi-printer"></i> Print Slip
            </a>
            <a href="{{ route('dashboard.custom-orders') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom:32px;">
        <div class="card-header" style="background:#f8fafc;padding:16px 20px;">
            <span style="font-weight:700;font-size:16px;display:flex;align-items:center;">
                <i class="bi bi-file-earmark-text" style="color:var(--primary);margin-right:8px;"></i> Order Information
            </span>
        </div>
        <div class="card-body" style="padding:24px;">
            <table style="width:100%;border-collapse:collapse;font-size:14.5px;margin-bottom:24px;">
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:16px 0;color:#64748b;font-weight:500;width:200px;">Customer Name</td>
                    <td style="padding:16px 0;font-weight:700;color:#0f172a;">{{ $order->customer_name }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:16px 0;color:#64748b;font-weight:500;">Delivery Date</td>
                    <td style="padding:16px 0;font-weight:600;color:#4f46e5;">
                        <i class="bi bi-calendar-event"></i> {{ $order->delivery_date->format('Y-m-d') }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:16px 0;color:#64748b;font-weight:500;vertical-align:top;">Specifications</td>
                    <td style="padding:16px 0;color:#334155;line-height:1.6;white-space:pre-wrap;">{{ $order->details }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:16px 0;color:#64748b;font-weight:500;">Total Cost</td>
                    <td style="padding:16px 0;font-weight:700;color:#0f172a;">৳ {{ number_format($order->total_price, 2) }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:16px 0;color:#64748b;font-weight:500;">Advance Paid</td>
                    <td style="padding:16px 0;font-weight:700;color:#16a34a;">৳ {{ number_format($order->advance_payment, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:16px 0;color:#64748b;font-weight:500;">Due Amount</td>
                    <td style="padding:16px 0;font-weight:800;color:#dc2626;font-size:18px;">
                        ৳ {{ number_format($order->total_price - $order->advance_payment, 2) }}
                    </td>
                </tr>
            </table>

            <form action="{{ route('dashboard.custom-orders.status', $order->id) }}" method="POST" style="margin:0;background:#f8fafc;padding:20px;border-radius:12px;border:1px solid #e2e8f0;display:inline-block;min-width:400px;">
                @csrf
                @method('PATCH')
                <h5 style="margin:0 0 16px 0;font-size:15px;color:#0f172a;font-weight:600;">
                    <i class="bi bi-pencil-square" style="color:var(--primary);margin-right:6px;"></i> Update Order Status
                </h5>
                <div style="display:flex;gap:12px;">
                    <select name="status" class="form-control" style="flex:1;height:42px;border-radius:8px;">
                        <option value="Pending" {{ $order->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ $order->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="In Progress" {{ $order->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="height:42px;border-radius:8px;padding:0 24px;">Update Status</button>
                </div>
                @error('status')
                    <div style="color:var(--danger);font-size:12.5px;margin-top:12px;font-weight:500;">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                    </div>
                @enderror
            </form>

            @if($order->total_price > $order->advance_payment)
            <form action="{{ route('dashboard.custom-orders.payment', $order->id) }}" method="POST" style="margin:0 0 0 20px;background:#f8fafc;padding:20px;border-radius:12px;border:1px solid #e2e8f0;display:inline-block;min-width:300px;vertical-align:top;">
                @csrf
                <h5 style="margin:0 0 16px 0;font-size:15px;color:#0f172a;font-weight:600;">
                    <i class="bi bi-cash-coin" style="color:var(--primary);margin-right:6px;"></i> Collect Due Payment
                </h5>
                <div style="display:flex;gap:12px;align-items:flex-end;">
                    <div style="flex:1;">
                        <label style="display:block;font-size:12px;color:#64748b;margin-bottom:4px;">Amount to Pay (৳)</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;">৳</span>
                            <input type="number" step="0.01" max="{{ $order->total_price - $order->advance_payment }}" name="payment_amount" value="{{ number_format($order->total_price - $order->advance_payment, 2, '.', '') }}" class="form-control" style="padding-left:28px;height:42px;border-radius:8px;" required>
                        </div>
                    </div>
                    <button type="submit" class="btn" style="height:42px;border-radius:8px;padding:0 24px;background:linear-gradient(135deg, #10b981 0%, #059669 100%);color:white;border:none;font-weight:600;box-shadow:0 4px 12px rgba(16, 185, 129, 0.2);display:flex;align-items:center;gap:6px;transition:all 0.2s ease;cursor:pointer;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.2)';">
                        <i class="bi bi-plus-circle" style="font-size:14px;"></i> Pay Now
                    </button>
                </div>
                @error('payment_amount')
                    <div style="color:var(--danger);font-size:12px;margin-top:8px;">{{ $message }}</div>
                @enderror
            </form>
            @endif
        </div>
    </div>

</x-layouts.admin>
