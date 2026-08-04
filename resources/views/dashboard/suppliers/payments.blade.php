<x-layouts.admin title="Supplier Payments">
    <div class="topbar">
        <h2 class="topbar-title">Supplier Payments</h2>
    </div>

    <div class="page-content" x-data="{ showEditPaymentModal: false, editPayment: { id: null, amount: 0, date: '', method: 'cash', description: '' } }">
        @if(session('success'))
            <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;">
                <i class="bi bi-check-circle-fill" style="margin-right:8px;"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;color:#b91c1c;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #fecaca;">
                <i class="bi bi-exclamation-circle-fill" style="margin-right:8px;"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard.suppliers.payments.index') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div style="flex:1;min-width:150px;">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="mobile_banking" {{ request('payment_method') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                        </select>
                    </div>

                    <div style="flex:1;min-width:150px;">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>

                    <div style="flex:1;min-width:150px;">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    
                    <div>
                        <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->anyFilled(['supplier_id', 'payment_method', 'start_date', 'end_date']))
                            <a href="{{ route('dashboard.suppliers.payments.index') }}" class="btn btn-outline" style="padding:10px 16px;">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:0;">
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                                <th style="padding:16px 20px;">Payment ID</th>
                                <th style="padding:16px 20px;">Supplier</th>
                                <th style="padding:16px 20px;">Date</th>
                                <th style="padding:16px 20px;">Method</th>
                                <th style="padding:16px 20px;">Description</th>
                                <th style="padding:16px 20px;text-align:right;">Amount</th>
                                <th style="padding:16px 20px;text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody style="color:#334155;">
                            @forelse($payments as $payment)
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:16px 20px;font-weight:600;color:#64748b;">#PAY-{{ sprintf('%04d', $payment->id) }}</td>
                                    <td style="padding:16px 20px;font-weight:600;">
                                        <a href="{{ route('dashboard.suppliers.show', $payment->supplier) }}" style="color:#0f172a;text-decoration:none;">
                                            {{ $payment->supplier->name }}
                                        </a>
                                    </td>
                                    <td style="padding:16px 20px;">{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td style="padding:16px 20px;text-transform:capitalize;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                    <td style="padding:16px 20px;color:#64748b;">{{ $payment->description ?: '-' }}</td>
                                    <td style="padding:16px 20px;text-align:right;font-weight:600;color:#10b981;">৳ {{ number_format($payment->amount, 2) }}</td>
                                    <td style="padding:16px 20px;text-align:center;">
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
                                    <td colspan="7" style="padding:48px;text-align:center;color:#64748b;">
                                        <i class="bi bi-receipt" style="font-size:32px;display:block;margin-bottom:12px;color:#cbd5e1;"></i>
                                        No payments found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($payments->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                    {{ $payments->links() }}
                </div>
            @endif
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
