<x-layouts.admin title="Invoice — {{ $sale->invoice_no }}">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('dashboard.sales') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
            <div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Sales Invoice</h2>
                <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">
                    <span style="font-family:monospace;font-weight:700;color:#6366f1;">{{ $sale->invoice_no }}</span>
                    &nbsp;·&nbsp; {{ $sale->sale_date->format('d M, Y') }}
                </p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;" class="no-print">
            @if($sale->status === 'completed')
                <a href="{{ route('dashboard.sales.edit', $sale) }}" class="btn btn-outline" style="text-decoration:none;">
                    <i class="bi bi-pencil-square"></i> Edit Invoice
                </a>
            @endif
            <a href="{{ route('dashboard.sales.print', $sale) }}" target="_blank" class="btn btn-outline">
                <i class="bi bi-printer"></i> Print Invoice
            </a>
            <form id="delete-form" method="POST" action="{{ route('dashboard.sales.destroy', $sale) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="button" class="btn" style="background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Void Invoice
                </button>
            </form>
        </div>
    </div>

    {{-- Status Banner --}}
    @if($sale->status === 'refunded')
        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:12px 20px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#92400e;font-weight:600;" class="no-print">
            <i class="bi bi-arrow-counterclockwise" style="font-size:20px;"></i>
            This invoice has been marked as <strong>Refunded</strong>.
        </div>
    @elseif($sale->status === 'voided')
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 20px;margin-bottom:20px;display:flex;align-items:center;gap:10px;color:#b91c1c;font-weight:600;" class="no-print">
            <i class="bi bi-slash-circle" style="font-size:20px;"></i>
            This invoice has been <strong>Voided</strong>.
        </div>
    @endif

    {{-- Main Grid --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

        {{-- LEFT: Invoice Body --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Invoice Header Info --}}
            <div class="card">
                <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:6px;">Customer</div>
                        @if($sale->customer)
                            <div style="font-weight:700;color:#0f172a;font-size:14px;">{{ $sale->customer->name }}</div>
                            @if($sale->customer->phone)
                                <div style="font-size:12.5px;color:#64748b;margin-top:2px;"><i class="bi bi-telephone"></i> {{ $sale->customer->phone }}</div>
                            @endif
                        @else
                            <div style="color:#64748b;font-size:14px;">Walk-in Customer</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:6px;">Sale Date</div>
                        <div style="font-weight:700;color:#0f172a;font-size:14px;"><i class="bi bi-calendar3"></i> {{ $sale->sale_date->format('d M, Y') }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">Created: {{ $sale->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;color:#94a3b8;margin-bottom:6px;">Served By</div>
                        <div style="font-weight:700;color:#0f172a;font-size:14px;"><i class="bi bi-person"></i> {{ $sale->creator?->name ?? 'System' }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">
                            Payment:
                            @if($sale->payment_method === 'cash') <span style="font-weight:600;color:#374151;"><i class="bi bi-cash"></i> Cash</span>
                            @elseif($sale->payment_method === 'card') <span style="font-weight:600;color:#0369a1;"><i class="bi bi-credit-card"></i> Card</span>
                            @elseif($sale->payment_method === 'mobile_pay') <span style="font-weight:600;color:#6b21a8;"><i class="bi bi-phone"></i> Mobile Pay</span>
                            @else <span style="font-weight:600;color:#c2410c;"><i class="bi bi-person-lines-fill"></i> Credit</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-bag"></i> Purchased Items ({{ $sale->items->count() }} {{ $sale->items->count() === 1 ? 'item' : 'items' }})</span>
                </div>
                <div class="card-body" style="padding:0;overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                                <th style="padding:14px 20px;">#</th>
                                <th style="padding:14px 20px;">Product</th>
                                <th style="padding:14px 20px;text-align:center;">SKU</th>
                                <th style="padding:14px 20px;text-align:center;">Quantity</th>
                                <th style="padding:14px 20px;text-align:right;">Unit Price</th>
                                <th style="padding:14px 20px;text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody style="color:#334155;">
                            @forelse($sale->items as $i => $item)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:13px 20px;color:#94a3b8;font-weight:600;">{{ $i + 1 }}</td>
                                <td style="padding:13px 20px;">
                                    <div style="font-weight:700;color:#0f172a;">{{ $item->product?->name ?? 'Deleted Product' }}</div>
                                </td>
                                <td style="padding:13px 20px;text-align:center;font-family:monospace;font-size:12px;color:#475569;">
                                    {{ $item->product?->sku ?? '—' }}
                                </td>
                                <td style="padding:13px 20px;text-align:center;font-weight:600;">
                                    {{ floatval($item->quantity) }} {{ $item->product?->unit?->short_name ?? 'pcs' }}
                                </td>
                                <td style="padding:13px 20px;text-align:right;">৳ {{ number_format($item->unit_price, 2) }}</td>
                                <td style="padding:13px 20px;text-align:right;font-weight:700;color:#0f172a;">৳ {{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="padding:32px;text-align:center;color:#94a3b8;">No line items found for this invoice.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            @if($sale->note)
                <div class="card">
                    <div class="card-header"><span class="card-title"><i class="bi bi-chat-left-text"></i> Notes</span></div>
                    <div class="card-body" style="color:#475569;line-height:1.7;white-space:pre-wrap;">{{ $sale->note }}</div>
                </div>
            @endif
        </div>

        {{-- RIGHT: Summary Cards --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Status Card --}}
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="bi bi-info-circle"></i> Invoice Status</span></div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;font-weight:600;">Invoice No</span>
                        <span style="font-family:monospace;font-weight:700;color:#6366f1;">{{ $sale->invoice_no }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;font-weight:600;">Status</span>
                        @if($sale->status === 'completed')
                            <span style="background:#dcfce7;color:#15803d;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:700;"><i class="bi bi-check-circle-fill"></i> Completed</span>
                        @elseif($sale->status === 'partial')
                            <span style="background:#fef3c7;color:#b45309;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:700;"><i class="bi bi-exclamation-circle-fill"></i> Partially Paid</span>
                        @elseif($sale->status === 'due')
                            <span style="background:#fee2e2;color:#b91c1c;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:700;"><i class="bi bi-x-circle-fill"></i> Due (Unpaid)</span>
                        @elseif($sale->status === 'refunded')
                            <span style="background:#f1f5f9;color:#475569;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:700;"><i class="bi bi-arrow-counterclockwise"></i> Refunded</span>
                        @else
                            <span style="background:#fee2e2;color:#b91c1c;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:700;"><i class="bi bi-slash-circle"></i> Voided</span>
                        @endif
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;color:#64748b;font-weight:600;">Payment Method</span>
                        <span style="font-size:13px;font-weight:700;color:#374151;text-transform:capitalize;">{{ str_replace('_', ' ', $sale->payment_method) }}</span>
                    </div>
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="card">
                <div class="card-header"><span class="card-title"><i class="bi bi-receipt"></i> Financial Summary</span></div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Subtotal:</span>
                        <span style="font-weight:600;color:#0f172a;">৳ {{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    @if($sale->discount_amount > 0)
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Discount:</span>
                        <span style="font-weight:600;color:#ef4444;">— ৳ {{ number_format($sale->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($sale->tax_amount > 0)
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Tax / VAT:</span>
                        <span style="font-weight:600;color:#0f172a;">+ ৳ {{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <hr style="border:0;border-top:2px solid #e2e8f0;margin:4px 0;">
                    <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:800;color:#0f172a;">
                        <span>Grand Total:</span>
                        <span style="color:#6366f1;">৳ {{ number_format($sale->grand_total, 2) }}</span>
                    </div>
                    <hr style="border:0;border-top:1px solid #f1f5f9;margin:4px 0;">
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Amount Paid:</span>
                        <span style="font-weight:600;color:#16a34a;">৳ {{ number_format($sale->amount_tendered, 2) }}</span>
                    </div>
                    @if($sale->change_amount > 0)
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Change Given:</span>
                        <span style="font-weight:700;color:#475569;">৳ {{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($sale->grand_total - $sale->amount_tendered > 0)
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Remaining Due:</span>
                        <span style="font-weight:700;color:#dc2626;">৳ {{ number_format($sale->grand_total - $sale->amount_tendered, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(in_array($sale->status, ['partial', 'due']) && ($sale->grand_total - $sale->amount_tendered > 0))
            <div class="card" style="border:1px solid #fed7aa;background:#fffaf5;">
                <div class="card-header" style="background:#ffedd5;border-bottom:1px solid #fed7aa;">
                    <span class="card-title" style="color:#c2410c;"><i class="bi bi-cash-coin"></i> Collect Due Payment</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.sales.collect-payment', $sale) }}">
                        @csrf
                        <div style="margin-bottom:12px;">
                            <label class="form-label" style="font-weight:600;color:#475569;">Remaining Due Amount</label>
                            <div style="font-size:20px;font-weight:800;color:#dc2626;">৳ {{ number_format($sale->grand_total - $sale->amount_tendered, 2) }}</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600;">Amount to Collect (৳) <span style="color:var(--danger)">*</span></label>
                            <input type="number" step="0.01" max="{{ $sale->grand_total - $sale->amount_tendered }}" min="0.01" name="amount_collected" value="{{ $sale->grand_total - $sale->amount_tendered }}" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;background:#ea580c;border-color:#ea580c;color:#fff;">
                            Collect & Update
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Print Receipt Template --}}
    <div id="print-receipt" style="display:none;">
        <div style="text-align:center;padding-bottom:16px;border-bottom:2px dashed #333;margin-bottom:16px;">
            <div style="font-size:20px;font-weight:900;">🏪 POS System</div>
            <div style="font-size:12px;color:#555;">Bakery Edition</div>
        </div>
        <div style="font-size:13px;margin-bottom:12px;">
            <div><strong>Invoice:</strong> {{ $sale->invoice_no }}</div>
            <div><strong>Date:</strong> {{ $sale->sale_date->format('d M Y') }} {{ $sale->created_at->format('h:i A') }}</div>
            <div><strong>Customer:</strong> {{ $sale->customer?->name ?? 'Walk-in Customer' }}</div>
            <div><strong>Served by:</strong> {{ $sale->creator?->name ?? 'System' }}</div>
        </div>
        <table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:12px;">
            <thead>
                <tr style="border-bottom:1px solid #333;">
                    <th style="text-align:left;padding:4px 0;">Item</th>
                    <th style="text-align:center;padding:4px;">Qty</th>
                    <th style="text-align:right;padding:4px 0;">Price</th>
                    <th style="text-align:right;padding:4px 0;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td style="padding:3px 0;">{{ $item->product?->name ?? 'N/A' }}</td>
                    <td style="text-align:center;padding:3px;">{{ floatval($item->quantity) }}</td>
                    <td style="text-align:right;padding:3px 0;">৳{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:right;padding:3px 0;">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="border-top:1px dashed #333;padding-top:10px;font-size:13px;">
            <div style="display:flex;justify-content:space-between;"><span>Subtotal:</span><span>৳{{ number_format($sale->subtotal, 2) }}</span></div>
            @if($sale->discount_amount > 0)<div style="display:flex;justify-content:space-between;"><span>Discount:</span><span>-৳{{ number_format($sale->discount_amount, 2) }}</span></div>@endif
            @if($sale->tax_amount > 0)<div style="display:flex;justify-content:space-between;"><span>Tax:</span><span>৳{{ number_format($sale->tax_amount, 2) }}</span></div>@endif
            <div style="display:flex;justify-content:space-between;font-weight:900;font-size:15px;border-top:1px solid #333;margin-top:8px;padding-top:6px;"><span>TOTAL:</span><span>৳{{ number_format($sale->grand_total, 2) }}</span></div>
            @if($sale->amount_tendered > 0)
            <div style="display:flex;justify-content:space-between;"><span>Tendered:</span><span>৳{{ number_format($sale->amount_tendered, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;"><span>Change:</span><span>৳{{ number_format($sale->change_amount, 2) }}</span></div>
            @endif
        </div>
        <div style="text-align:center;margin-top:16px;padding-top:12px;border-top:2px dashed #333;font-size:12px;color:#555;">
            Thank you for your purchase!<br>{{ now()->format('d M Y, h:i A') }}
        </div>
    </div>

    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Void this Invoice?',
                html: `<p style="color:#475569;font-size:14px;">Invoice <strong style="font-family:monospace;color:#0f172a;">{{ $sale->invoice_no }}</strong> will be permanently removed.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, void it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>

    <style>
        @media print {
            .no-print, .sidebar, .topbar, header { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            .page-content { padding: 0 !important; }
            /* Hide all main content and show only receipt */
            body > * { display: none !important; }
            #print-receipt { display: block !important; padding: 20px; font-family: monospace; max-width: 320px; margin: 0 auto; }
        }
    </style>

</x-layouts.admin>
