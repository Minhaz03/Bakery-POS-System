<x-layouts.admin title="Supplier Details">

    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Supplier: {{ $supplier->name }}</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">View supplier profile and purchase history.</p>
        </div>
        <a href="{{ route('dashboard.suppliers') }}" class="btn btn-outline" style="text-decoration:none;">
            <i class="bi bi-arrow-left"></i> Back to Directory
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;margin-bottom:24px;">
        <!-- Supplier Info Card -->
        <div class="card">
            <div class="card-body" style="padding:24px;">
                <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 16px 0;border-bottom:1px solid #e2e8f0;padding-bottom:12px;">Contact Information</h3>
                
                <div style="margin-bottom:16px;">
                    <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Contact Person</div>
                    <div style="font-size:14px;color:#334155;font-weight:500;">{{ $supplier->contact_person ?: 'N/A' }}</div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Phone</div>
                    <div style="font-size:14px;color:#334155;font-weight:500;">{{ $supplier->phone ?: 'N/A' }}</div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Email</div>
                    <div style="font-size:14px;color:#334155;font-weight:500;">
                        @if($supplier->email)
                            <a href="mailto:{{ $supplier->email }}" style="color:#3b82f6;text-decoration:none;">{{ $supplier->email }}</a>
                        @else
                            N/A
                        @endif
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Address</div>
                    <div style="font-size:14px;color:#334155;font-weight:500;">
                        {{ $supplier->address ?: 'N/A' }}<br>
                        {{ $supplier->city }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Balance Card -->
        <div class="card">
            <div class="card-body" style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:24px;background:linear-gradient(to right bottom, #f8fafc, #f1f5f9);">
                <!-- Left Column: Balance Info -->
                <div style="display:flex;flex-direction:column;justify-content:center;align-items:center;border-right:1px solid #e2e8f0;padding-right:24px;">
                    <div style="text-align:center;">
                        <div style="font-size:13px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Current Ledger Balance</div>
                        @php
                            $balance = $supplier->current_balance;
                            $balanceColor = '#0f172a';
                            if ($balance > 0) {
                                $balanceColor = '#ef4444'; // Red for payable
                            } elseif ($balance < 0) {
                                $balanceColor = '#10b981'; // Green for receivable/advance
                            }
                        @endphp
                        <div style="font-size:36px;font-weight:800;color:{{ $balanceColor }};letter-spacing:-0.02em;">
                            ৳ {{ number_format(abs($balance), 2) }}
                            @if($balance > 0)
                                <span style="font-size:14px;color:#ef4444;margin-left:4px;">(Payable)</span>
                            @elseif($balance < 0)
                                <span style="font-size:14px;color:#10b981;margin-left:4px;">(Advance)</span>
                            @else
                                <span style="font-size:14px;color:#64748b;margin-left:4px;">(Settled)</span>
                            @endif
                        </div>
                        
                        <div style="margin-top:24px;padding-top:24px;border-top:1px solid #e2e8f0;display:flex;gap:32px;justify-content:center;">
                            <div>
                                <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Opening Balance</div>
                                <div style="font-size:16px;font-weight:700;color:#475569;">৳ {{ number_format($supplier->opening_balance, 2) }}</div>
                            </div>
                            <div>
                                <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Status</div>
                                <div>
                                    <span style="background:{{ $supplier->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $supplier->is_active ? '#15803d' : '#475569' }};padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                        {{ $supplier->is_active ? 'Active Supplier' : 'Inactive Supplier' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top:24px; display:flex; gap:12px; justify-content:center; align-items:center;">
                            <a href="{{ route('dashboard.suppliers.edit', $supplier) }}" class="btn btn-primary" style="padding:8px 24px;">
                                <i class="bi bi-pencil-square"></i> Edit Supplier
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Payment Form -->
                <div>
                    @if($balance > 0)
                        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 16px 0;">Record Payment</h3>
                        <form action="{{ route('dashboard.suppliers.pay', $supplier) }}" method="POST">
                            @csrf
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                                <div>
                                    <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Payment Amount (৳)</label>
                                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $balance }}" class="form-control" style="width: 100%;" value="{{ abs($balance) }}" required>
                                </div>
                                <div>
                                    <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control" style="width: 100%;" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 16px;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Payment Method</label>
                                <select name="payment_method" class="form-control" style="width: 100%;" required>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="mobile_banking">Mobile Banking</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 16px;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Description / Notes</label>
                                <textarea name="description" class="form-control" style="width: 100%;" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                            
                            <div style="text-align:right;">
                                <button type="submit" style="background-color:#10b981;color:#ffffff;border:none;border-radius:6px;padding:10px 24px;font-weight:600;font-size:14px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background-color 0.2s;" onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10b981'">
                                    <i class="bi bi-check-circle" style="font-size:16px;"></i> Confirm Payment
                                </button>
                            </div>
                        </form>
                    @else
                        <div style="height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;color:#64748b;">
                            <i class="bi bi-check-circle" style="font-size:36px;color:#10b981;margin-bottom:12px;"></i>
                            <div style="font-weight:600;">No Payment Due</div>
                            <div style="font-size:13px;text-align:center;margin-top:4px;">This supplier's ledger is fully settled or has an advance balance.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases History Table -->
    <div class="card">
        <div class="card-body" style="padding:24px;">
            <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 16px 0;">Purchase History</h3>
            
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                            <th style="padding:12px 16px;">Reference No.</th>
                            <th style="padding:12px 16px;">Date</th>
                            <th style="padding:12px 16px;text-align:right;">Grand Total</th>
                            <th style="padding:12px 16px;text-align:right;">Paid</th>
                            <th style="padding:12px 16px;text-align:right;">Due</th>
                            <th style="padding:12px 16px;text-align:center;">Status</th>
                            <th style="padding:12px 16px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color:#334155;">
                        @forelse($supplier->purchases as $purchase)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px 16px;font-weight:600;color:#0ea5e9;">{{ $purchase->reference_no }}</td>
                                <td style="padding:12px 16px;">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td style="padding:12px 16px;text-align:right;font-weight:600;">৳ {{ number_format($purchase->grand_total, 2) }}</td>
                                <td style="padding:12px 16px;text-align:right;color:#10b981;">৳ {{ number_format($purchase->amount_paid, 2) }}</td>
                                <td style="padding:12px 16px;text-align:right;color:#ef4444;">৳ {{ number_format($purchase->amount_due, 2) }}</td>
                                <td style="padding:12px 16px;text-align:center;">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg' => '#fef3c7', 'color' => '#d97706'],
                                            'received' => ['bg' => '#dcfce7', 'color' => '#15803d'],
                                        ];
                                        $sc = $statusConfig[$purchase->status] ?? ['bg' => '#f1f5f9', 'color' => '#475569'];
                                    @endphp
                                    <span style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;text-transform:capitalize;">
                                        {{ $purchase->status }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px;text-align:center;">
                                    <a href="{{ route('dashboard.purchases.show', $purchase) }}" style="color:#3b82f6;text-decoration:none;font-weight:500;font-size:12.5px;">View Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:32px;text-align:center;color:#64748b;">
                                    <i class="bi bi-cart-x" style="font-size:24px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                    No purchases found from this supplier.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    <!-- Payment History Table -->
    <div class="card" style="margin-top: 24px;" x-data="{ showEditPaymentModal: false, editPayment: { id: null, amount: 0, date: '', method: 'cash', description: '' } }">
        <div class="card-body" style="padding:24px;">
            <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 16px 0;">Payment History</h3>
            
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                            <th style="padding:12px 16px;">Payment ID</th>
                            <th style="padding:12px 16px;">Date</th>
                            <th style="padding:12px 16px;">Method</th>
                            <th style="padding:12px 16px;">Description</th>
                            <th style="padding:12px 16px;text-align:right;">Amount</th>
                            <th style="padding:12px 16px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color:#334155;">
                        @forelse($supplier->payments as $payment)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:12px 16px;font-weight:600;color:#64748b;">#PAY-{{ sprintf('%04d', $payment->id) }}</td>
                                <td style="padding:12px 16px;">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td style="padding:12px 16px;text-transform:capitalize;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                <td style="padding:12px 16px;color:#64748b;">{{ $payment->description ?: '-' }}</td>
                                <td style="padding:12px 16px;text-align:right;font-weight:600;color:#10b981;">৳ {{ number_format($payment->amount, 2) }}</td>
                                <td style="padding:12px 16px;text-align:center;">
                                    <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                        <button type="button" @click="editPayment = { id: {{ $payment->id }}, amount: {{ $payment->amount }}, date: '{{ $payment->payment_date->format('Y-m-d') }}', method: '{{ $payment->payment_method }}', description: '{{ addslashes($payment->description) }}' }; showEditPaymentModal = true" style="background:none;border:none;color:#6366f1;font-size:14.5px;cursor:pointer;padding:0;" title="Edit Payment">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('dashboard.suppliers.payments.destroy', $payment) }}" id="delete-form-{{ $payment->id }}" style="margin:0;display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $payment->id }})" style="background:none;border:none;color:#ef4444;font-size:14.5px;cursor:pointer;padding:0;" title="Delete Payment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:32px;text-align:center;color:#64748b;">
                                    <i class="bi bi-receipt" style="font-size:24px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                    No payment history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Payment Modal -->
        <div x-show="showEditPaymentModal" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditPaymentModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditPaymentModal = false" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEditPaymentModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="`/dashboard/suppliers/payments/${editPayment.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" style="margin-bottom: 1rem; font-weight: 700;">Edit Supplier Payment</h3>
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Payment Amount (৳)</label>
                                <input type="number" name="amount" x-model="editPayment.amount" step="0.01" min="0.01" class="form-control" style="width: 100%;" required>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Payment Date</label>
                                <input type="date" name="payment_date" x-model="editPayment.date" class="form-control" style="width: 100%;" required>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Payment Method</label>
                                <select name="payment_method" x-model="editPayment.method" class="form-control" style="width: 100%;" required>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="mobile_banking">Mobile Banking</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Description / Notes</label>
                                <textarea name="description" x-model="editPayment.description" class="form-control" style="width: 100%;" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
                            <button type="submit" class="btn btn-success" style="background-color:#10b981;color:#ffffff;border:none;">
                                Save Changes
                            </button>
                            <button type="button" @click="showEditPaymentModal = false" class="btn btn-outline">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the payment and update the supplier balance and unpaid purchases.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-layouts.admin>
