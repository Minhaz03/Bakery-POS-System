<x-layouts.admin title="Expenses">
    <div class="topbar">
        <h2 class="topbar-title">Expenses</h2>
        <button type="button" x-data @click="$dispatch('open-create-modal')" class="btn btn-primary" style="padding:8px 16px;">
            <i class="bi bi-plus-lg"></i> Add Expense
        </button>
    </div>

    <div class="page-content" x-data="{ showCreateModal: false }" @open-create-modal.window="showCreateModal = true">
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
                <form method="GET" action="{{ route('dashboard.expenses.index') }}" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <label class="form-label">Category</label>
                        <select name="expense_category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('expense_category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
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

                    <div style="flex:1;min-width:200px;">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Ref/Description..." class="form-control">
                    </div>
                    
                    <div>
                        <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->anyFilled(['expense_category_id', 'payment_method', 'start_date', 'end_date', 'search']))
                            <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-outline" style="padding:10px 16px;">Clear</a>
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
                                <th style="padding:16px 20px;">Ref No.</th>
                                <th style="padding:16px 20px;">Date</th>
                                <th style="padding:16px 20px;">Category</th>
                                <th style="padding:16px 20px;">Description</th>
                                <th style="padding:16px 20px;">Method</th>
                                <th style="padding:16px 20px;text-align:right;">Amount</th>
                                <th style="padding:16px 20px;text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody style="color:#334155;">
                            @forelse($expenses as $expense)
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:16px 20px;font-weight:600;color:#64748b;">{{ $expense->reference_no ?: '#EXP-' . sprintf('%04d', $expense->id) }}</td>
                                    <td style="padding:16px 20px;">{{ $expense->expense_date->format('M d, Y') }}</td>
                                    <td style="padding:16px 20px;font-weight:600;color:#0f172a;">{{ $expense->category->name }}</td>
                                    <td style="padding:16px 20px;color:#64748b;">{{ str($expense->description)->limit(40) }}</td>
                                    <td style="padding:16px 20px;text-transform:capitalize;">{{ str_replace('_', ' ', $expense->payment_method) }}</td>
                                    <td style="padding:16px 20px;text-align:right;font-weight:600;color:#ef4444;">৳ {{ number_format($expense->amount, 2) }}</td>
                                    <td style="padding:16px 20px;text-align:center;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                            <a href="{{ route('dashboard.expenses.show', $expense) }}" style="color:#0ea5e9;font-size:14.5px;" title="View Expense"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('dashboard.expenses.edit', $expense) }}" style="color:#6366f1;font-size:14.5px;" title="Edit Expense"><i class="bi bi-pencil-square"></i></a>
                                            <form method="POST" action="{{ route('dashboard.expenses.destroy', $expense) }}" id="delete-form-{{ $expense->id }}" style="margin:0;display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $expense->id }})" style="background:none;border:none;color:#ef4444;font-size:14.5px;cursor:pointer;padding:0;" title="Delete Expense">
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
                                        No expenses found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($expenses->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                    {{ $expenses->links() }}
                </div>
            @endif
        <!-- Create Expense Modal -->
        <div x-show="showCreateModal" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div style="display:flex; align-items:center; justify-content:center; min-height:100vh; padding:16px;">
                <!-- Backdrop -->
                <div x-show="showCreateModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreateModal = false" aria-hidden="true"></div>
                
                <!-- Modal Panel -->
                <div x-show="showCreateModal" style="background:#fff; border-radius:8px; width:100%; max-width:700px; position:relative; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); overflow:hidden;">
                    <form action="{{ route('dashboard.expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div style="padding:24px;">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" style="margin-bottom: 24px; font-weight: 700;">Record Expense</h3>
                            
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Expense Category *</label>
                                    <select name="expense_category_id" class="form-control" style="width:100%;" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Amount (৳) *</label>
                                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control" style="width:100%;" required>
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                                <div>
                                    <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Expense Date *</label>
                                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control" style="width:100%;" required>
                                </div>
                                
                                <div>
                                    <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Payment Method *</label>
                                    <select name="payment_method" class="form-control" style="width:100%;" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="mobile_banking">Mobile Banking</option>
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom:16px;">
                                <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Reference No (Optional)</label>
                                <input type="text" name="reference_no" class="form-control" style="width:100%;">
                            </div>

                            <div style="margin-bottom:16px;">
                                <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Description / Notes</label>
                                <textarea name="description" class="form-control" style="width:100%;" rows="3"></textarea>
                            </div>

                            <div style="margin-bottom:16px;">
                                <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Attachment (Receipt / Bill)</label>
                                <input type="file" name="attachment" class="form-control" style="width:100%;padding:8px;" accept=".jpg,.jpeg,.png,.pdf">
                                <small style="color:#64748b;display:block;margin-top:4px;font-size:11px;">Max size: 2MB. Allowed formats: JPG, PNG, PDF</small>
                            </div>
                        </div>
                        <div style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 24px;">
                                Save Expense
                            </button>
                            <button type="button" @click="showCreateModal = false" class="btn btn-outline" style="padding: 8px 24px;">
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
                text: "This will permanently delete the expense.",
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
