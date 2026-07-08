<x-layouts.admin title="Customer Profile — {{ $customer->name }}">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('dashboard.customers') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
            <div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Customer Profile</h2>
                <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">
                    <span style="font-weight:700;color:#6366f1;">{{ $customer->name }}</span>
                    &nbsp;·&nbsp; Member since {{ $customer->created_at->format('d M, Y') }}
                </p>
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('dashboard.customers.edit', $customer) }}" class="btn btn-primary" style="text-decoration:none;">
                <i class="bi bi-pencil-square"></i> Edit Profile
            </a>
        </div>
    </div>

    {{-- Customer Grid Layout --}}
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;align-items:start;">
        
        {{-- LEFT PANEL: Customer Info Card & Loyalty Summary --}}
        <div style="display:flex;flex-direction:column;gap:24px;">
            
            {{-- Contact Information --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-person-badge"></i> Contact Card</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px;font-size:13.5px;">
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Phone Number</div>
                        <div style="font-weight:700;color:#0f172a;">
                            @if($customer->phone)
                                <i class="bi bi-telephone"></i> {{ $customer->phone }}
                            @else
                                <span style="font-weight:400;color:#94a3b8;">Not provided</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Email Address</div>
                        <div style="font-weight:700;color:#0f172a;">
                            @if($customer->email)
                                <i class="bi bi-envelope"></i> {{ $customer->email }}
                            @else
                                <span style="font-weight:400;color:#94a3b8;">Not provided</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Home/Store Address</div>
                        <div style="font-weight:700;color:#0f172a;">
                            @if($customer->address)
                                <i class="bi bi-geo-alt"></i> {{ $customer->address }}
                            @else
                                <span style="font-weight:400;color:#94a3b8;">Not provided</span>
                            @endif
                        </div>
                    </div>
                    @if($customer->date_of_birth)
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">Birthday</div>
                        <div style="font-weight:700;color:#0f172a;"><i class="bi bi-gift"></i> {{ $customer->date_of_birth->format('d M, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Financial & Loyalty Summary --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-cash-coin"></i> Accounts & Loyalty</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:12px;font-size:13.5px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Loyalty Points:</span>
                        <span style="background:#f0fdf4;color:#166534;padding:2px 8px;border-radius:999px;font-weight:700;font-size:12px;border:1px solid #bbf7d0;">
                            <i class="bi bi-star-fill" style="color:#eab308;"></i> {{ $customer->loyalty_points }} pts
                        </span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Total Invoiced:</span>
                        <span style="font-weight:700;color:#0f172a;">৳ {{ number_format($totals->total_bill, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Total Paid:</span>
                        <span style="font-weight:700;color:#16a34a;">৳ {{ number_format($totals->total_paid, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;">Outstanding Due:</span>
                        <span style="font-weight:800;color:{{ $totals->total_due > 0 ? '#ef4444' : '#64748b' }};">৳ {{ number_format($totals->total_due, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT PANEL: Sales & Payments list --}}
        <div class="card">
            <div class="card-header" style="justify-content:space-between;display:flex;align-items:center;">
                <span class="card-title"><i class="bi bi-receipt"></i> Transaction & Payment History</span>
                <span style="font-size:12px;color:#64748b;font-weight:600;">Total Transactions: {{ $sales->total() }}</span>
            </div>
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                            <th style="padding:14px 18px;">Invoice No</th>
                            <th style="padding:14px 18px;">Date</th>
                            <th style="padding:14px 18px;text-align:center;">Method</th>
                            <th style="padding:14px 18px;text-align:center;">Status</th>
                            <th style="padding:14px 18px;text-align:right;">Grand Total</th>
                            <th style="padding:14px 18px;text-align:right;">Amount Paid</th>
                            <th style="padding:14px 18px;text-align:right;">Remaining Due</th>
                            <th style="padding:14px 18px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color:#334155;">
                        @forelse($sales as $sale)
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding:12px 18px;font-weight:700;font-family:monospace;color:#6366f1;">
                                <a href="{{ route('dashboard.sales.show', $sale) }}" style="text-decoration:none;color:#6366f1;">{{ $sale->invoice_no }}</a>
                            </td>
                            <td style="padding:12px 18px;color:#64748b;">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td style="padding:12px 18px;text-align:center;text-transform:capitalize;font-weight:600;font-size:11.5px;">
                                @if($sale->payment_method === 'cash')
                                    <span style="color:#475569;"><i class="bi bi-cash"></i> Cash</span>
                                @elseif($sale->payment_method === 'card')
                                    <span style="color:#0369a1;"><i class="bi bi-credit-card"></i> Card</span>
                                @elseif($sale->payment_method === 'mobile_pay')
                                    <span style="color:#6b21a8;"><i class="bi bi-phone"></i> Mobile Pay</span>
                                @else
                                    <span style="color:#c2410c;"><i class="bi bi-person-lines-fill"></i> Credit</span>
                                @endif
                            </td>
                            <td style="padding:12px 18px;text-align:center;">
                                @if($sale->status === 'completed')
                                    <span style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                @elseif($sale->status === 'partial')
                                    <span style="background:#fef3c7;color:#b45309;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-exclamation-circle-fill"></i> Partial</span>
                                @elseif($sale->status === 'due')
                                    <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-x-circle-fill"></i> Due</span>
                                @elseif($sale->status === 'refunded')
                                    <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-arrow-counterclockwise"></i> Refunded</span>
                                @else
                                    <span style="background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i class="bi bi-slash-circle"></i> Voided</span>
                                @endif
                            </td>
                            <td style="padding:12px 18px;text-align:right;font-weight:700;color:#0f172a;">৳ {{ number_format($sale->grand_total, 2) }}</td>
                            <td style="padding:12px 18px;text-align:right;font-weight:600;color:#16a34a;">৳ {{ number_format($sale->amount_tendered, 2) }}</td>
                            <td style="padding:12px 18px;text-align:right;font-weight:700;color:{{ ($sale->grand_total - $sale->amount_tendered) > 0 ? '#ef4444' : '#64748b' }};">
                                ৳ {{ number_format($sale->grand_total - $sale->amount_tendered, 2) }}
                            </td>
                            <td style="padding:12px 18px;text-align:center;font-size:15px;white-space:nowrap;">
                                <a href="{{ route('dashboard.sales.show', $sale) }}" style="color:#0ea5e9;text-decoration:none;margin-right:8px;" title="View Invoice"><i class="bi bi-eye"></i></a>
                                @if(in_array($sale->status, ['partial', 'due']))
                                    <a href="{{ route('dashboard.sales.show', $sale) }}" style="color:#ea580c;text-decoration:none;" title="Collect Due"><i class="bi bi-cash-coin"></i></a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="padding:48px;text-align:center;color:#64748b;">
                                <i class="bi bi-receipt" style="font-size:36px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                No transaction record found for this customer.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination footer --}}
            <div style="padding:16px 20px;border-top:1px solid #e2e8f0;background:#f8fafc;">
                {{ $sales->links() }}
            </div>
        </div>

    </div>

</x-layouts.admin>
