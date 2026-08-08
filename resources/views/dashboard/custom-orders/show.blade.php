<x-layouts.admin title="Order Details - {{ $order->order_number }}">

    <!-- Header Section -->
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:16px;">
        <div>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                <h2 style="font-size:26px;font-weight:800;color:#0f172a;margin:0;">Order <span style="color:var(--primary);">{{ $order->order_number }}</span></h2>
                @php
                    $statusColors = [
                        'Pending' => 'background:#fef3c7;color:#d97706;',
                        'Confirmed' => 'background:#dcfce7;color:#15803d;',
                        'In Progress' => 'background:#e0f2fe;color:#0369a1;',
                        'Completed' => 'background:#f3e8ff;color:#7e22ce;',
                        'Cancelled' => 'background:#fee2e2;color:#b91c1c;',
                    ];
                    $badgeStyle = $statusColors[$order->status] ?? 'background:#f1f5f9;color:#64748b;';
                @endphp
                <span style="{{ $badgeStyle }} padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;">
                    {{ $order->status }}
                </span>
            </div>
            <p style="font-size:14px;color:#64748b;margin:0;">Review order specifications, payment details, and update status.</p>
        </div>
        <div style="display:flex;gap:12px;">
            <a href="{{ route('dashboard.custom-orders.print', $order->id) }}" target="_blank" class="btn btn-outline" style="background:#fff;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                <i class="bi bi-printer"></i> Print Slip
            </a>
            <a href="{{ route('dashboard.custom-orders') }}" class="btn btn-primary" style="box-shadow:0 4px 12px rgba(99,102,241,0.2);">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:24px;align-items:start;">
        
        <!-- Left Column: Order Info -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- Customer & Delivery -->
            <div class="card" style="box-shadow:0 4px 20px rgba(0,0,0,0.03);border:none;">
                <div class="card-header" style="background:#fff;border-bottom:1px solid #f1f5f9;padding:20px 24px;">
                    <span style="font-weight:700;font-size:16px;display:flex;align-items:center;color:#0f172a;">
                        <i class="bi bi-person-lines-fill" style="color:var(--primary);margin-right:10px;font-size:18px;"></i> Customer Information
                    </span>
                </div>
                <div class="card-body" style="padding:24px;">
                    <div style="display:flex;flex-direction:column;gap:20px;">
                        <div>
                            <span style="display:block;font-size:12px;color:#64748b;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">Customer Name</span>
                            <span style="font-size:16px;font-weight:700;color:#1e293b;">{{ $order->customer_name }}</span>
                        </div>
                        <div>
                            <span style="display:block;font-size:12px;color:#64748b;font-weight:600;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">Delivery Date</span>
                            <div style="display:inline-flex;align-items:center;gap:8px;background:#eff6ff;color:#1d4ed8;padding:8px 16px;border-radius:8px;font-weight:600;font-size:14px;">
                                <i class="bi bi-calendar-event"></i> {{ $order->delivery_date->format('l, F j, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div class="card" style="box-shadow:0 4px 20px rgba(0,0,0,0.03);border:none;">
                <div class="card-header" style="background:#fff;border-bottom:1px solid #f1f5f9;padding:20px 24px;">
                    <span style="font-weight:700;font-size:16px;display:flex;align-items:center;color:#0f172a;">
                        <i class="bi bi-card-text" style="color:var(--primary);margin-right:10px;font-size:18px;"></i> Order Specifications
                    </span>
                </div>
                <div class="card-body" style="padding:24px;background:#f8fafc;">
                    <p style="margin:0;color:#334155;line-height:1.7;white-space:pre-wrap;font-size:14.5px;">{{ $order->details }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Financials & Actions -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            
            <!-- Financial Summary -->
            <div class="card" style="box-shadow:0 4px 20px rgba(0,0,0,0.03);border:none;background:linear-gradient(to bottom right, #ffffff, #f8fafc);">
                <div class="card-header" style="background:transparent;border-bottom:1px solid #f1f5f9;padding:20px 24px;">
                    <span style="font-weight:700;font-size:16px;display:flex;align-items:center;color:#0f172a;">
                        <i class="bi bi-wallet2" style="color:var(--primary);margin-right:10px;font-size:18px;"></i> Payment Summary
                    </span>
                </div>
                <div class="card-body" style="padding:24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:16px;border-bottom:1px dashed #cbd5e1;margin-bottom:16px;">
                        <span style="color:#64748b;font-weight:500;font-size:14.5px;">Total Cost</span>
                        <span style="font-weight:800;color:#0f172a;font-size:16px;">৳ {{ number_format($order->total_price, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:16px;border-bottom:1px dashed #cbd5e1;margin-bottom:16px;">
                        <span style="color:#64748b;font-weight:500;font-size:14.5px;">Advance Paid</span>
                        <span style="font-weight:800;color:var(--success);font-size:16px;">৳ {{ number_format($order->advance_payment, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;background:#fff;padding:16px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.04);border:1px solid #f1f5f9;">
                        <span style="color:#334155;font-weight:700;font-size:15px;">Due Amount</span>
                        <span style="font-weight:800;color:var(--danger);font-size:20px;">
                            ৳ {{ number_format($order->total_price - $order->advance_payment, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions (Payment & Status) -->
            <div style="display:flex;flex-direction:column;gap:16px;">
                @if($order->status !== 'Completed')
                <div class="card" style="box-shadow:0 4px 20px rgba(0,0,0,0.03);border:1px solid #e2e8f0;border-left:4px solid var(--primary);">
                    <div class="card-body" style="padding:24px;">
                        <h5 style="margin:0 0 16px 0;font-size:15px;color:#0f172a;font-weight:700;display:flex;align-items:center;">
                            <i class="bi bi-pencil-square" style="color:var(--primary);margin-right:8px;font-size:18px;"></i> Update Status
                        </h5>
                        <form action="{{ route('dashboard.custom-orders.status', $order->id) }}" method="POST" style="margin:0;">
                            @csrf
                            @method('PATCH')
                            <div style="display:flex;gap:12px;">
                                <select name="status" class="form-control" style="flex:1;height:44px;border-radius:8px;font-weight:500;color:#334155;">
                                    <option value="Pending" {{ $order->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Confirmed" {{ $order->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="In Progress" {{ $order->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary" style="height:44px;border-radius:8px;padding:0 24px;box-shadow:0 4px 12px rgba(99,102,241,0.2);">Update</button>
                            </div>
                            @error('status')
                                <div style="color:var(--danger);font-size:12.5px;margin-top:12px;font-weight:500;display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}
                                </div>
                            @enderror
                        </form>
                    </div>
                </div>
                @else
                <div class="card" style="box-shadow:0 4px 20px rgba(0,0,0,0.03);border:1px solid #dcfce7;border-left:4px solid var(--success);background:#f0fdf4;">
                    <div class="card-body" style="padding:20px 24px;display:flex;align-items:center;gap:16px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-check-lg" style="color:var(--success);font-size:24px;"></i>
                        </div>
                        <div>
                            <h5 style="margin:0 0 4px 0;font-size:15px;color:#166534;font-weight:700;">Order Completed</h5>
                            <p style="margin:0;font-size:13.5px;color:#15803d;">This order has been completed and the status cannot be changed.</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($order->total_price > $order->advance_payment)
                <div class="card" x-data="{ showPaymentModal: false }" style="box-shadow:0 4px 20px rgba(0,0,0,0.03);border:1px solid #e2e8f0;border-left:4px solid var(--warning);position:relative;overflow:hidden;">
                    <div style="position:absolute;right:-20px;top:-20px;font-size:120px;color:rgba(245,158,11,0.05);z-index:0;pointer-events:none;"><i class="bi bi-cash-stack"></i></div>
                    <div class="card-body" style="padding:24px;position:relative;z-index:1;">
                        <h5 style="margin:0 0 16px 0;font-size:15px;color:#0f172a;font-weight:700;display:flex;align-items:center;">
                            <i class="bi bi-cash-coin" style="color:var(--warning);margin-right:8px;font-size:18px;"></i> Collect Due Payment
                        </h5>
                        <div style="display:flex;gap:12px;align-items:flex-end;">
                            <div style="flex:1;">
                                <label style="display:block;font-size:12px;color:#64748b;font-weight:600;margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">Amount to Pay</label>
                                <div style="position:relative;">
                                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#64748b;font-weight:600;">৳</span>
                                    <input type="number" step="0.01" value="{{ number_format($order->total_price - $order->advance_payment, 2, '.', '') }}" class="form-control" style="padding-left:32px;height:44px;border-radius:8px;font-weight:600;color:#0f172a;background:#f8fafc;" disabled>
                                </div>
                            </div>
                            <button type="button" @click="showPaymentModal = true" class="btn" style="height:44px;border-radius:8px;padding:0 24px;background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);color:white;border:none;font-weight:700;box-shadow:0 4px 12px rgba(245,158,11,0.25);display:flex;align-items:center;gap:6px;transition:all 0.2s ease;cursor:pointer;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(245,158,11,0.35)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.25)';">
                                <i class="bi bi-credit-card-fill" style="font-size:14px;"></i> Pay Now
                            </button>
                        </div>

                        <!-- Payment Modal -->
                        <div x-show="showPaymentModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:9999;" x-transition.opacity>
                            <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:20px;">
                                <div class="card" @click.outside="showPaymentModal = false" style="width:100%;max-width:420px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);display:flex;flex-direction:column;border:none;border-radius:16px;overflow:hidden;" x-transition.scale.origin.bottom>
                                    <div style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div style="width:36px;height:36px;background:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.05);color:var(--primary);font-size:18px;">
                                                <i class="bi bi-wallet2"></i>
                                            </div>
                                            <span style="font-weight:800;font-size:17px;color:#0f172a;">Process Payment</span>
                                        </div>
                                        <button type="button" @click="showPaymentModal = false" style="background:#fff;border:1px solid #e2e8f0;border-radius:50%;width:32px;height:32px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;transition:all 0.2s;" onmouseover="this.style.background='#f1f5f9';this.style.color='#0f172a';" onmouseout="this.style.background='#fff';this.style.color='#64748b';"><i class="bi bi-x" style="font-size:20px;line-height:0;"></i></button>
                                    </div>
                                    <form action="{{ route('dashboard.custom-orders.payment', $order->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <div class="card-body" style="padding:24px;">
                                            <div class="form-group" style="margin-bottom:20px;">
                                                <label class="form-label" style="color:#475569;font-weight:600;font-size:13px;">Payment Amount (৳) <span style="color:var(--danger)">*</span></label>
                                                <div style="position:relative;">
                                                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#64748b;font-weight:600;font-size:16px;">৳</span>
                                                    <input type="number" step="0.01" max="{{ $order->total_price - $order->advance_payment }}" name="payment_amount" value="{{ number_format($order->total_price - $order->advance_payment, 2, '.', '') }}" class="form-control" style="padding-left:36px;height:48px;border-radius:10px;font-size:16px;font-weight:700;color:#0f172a;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,0.05);" required>
                                                </div>
                                            </div>
                                            <div class="form-group" style="margin-bottom:0;">
                                                <label class="form-label" style="color:#475569;font-weight:600;font-size:13px;">Payment Method <span style="color:var(--danger)">*</span></label>
                                                <select name="payment_method" class="form-control" style="height:48px;border-radius:10px;font-size:15px;color:#334155;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,0.05);" required>
                                                    <option value="cash">💵 Cash</option>
                                                    <option value="card">💳 Card</option>
                                                    <option value="digital">📱 Digital (Mobile Banking)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div style="padding:20px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:12px;">
                                            <button type="button" class="btn btn-outline" @click="showPaymentModal = false" style="height:44px;border-radius:8px;padding:0 20px;font-weight:600;">Cancel</button>
                                            <button type="submit" class="btn btn-primary" style="height:44px;border-radius:8px;padding:0 24px;font-weight:600;box-shadow:0 4px 12px rgba(99,102,241,0.25);">Confirm Payment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.admin>
