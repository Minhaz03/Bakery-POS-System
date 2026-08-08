<x-layouts.admin title="Expenses">
    <div class="topbar">
        <h2 class="topbar-title">Expenses</h2>
    </div>

    <div class="page-content">
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

        <div style="display:grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start;">
            
            <!-- LEFT PANEL: Add Expense Form -->
            <div class="card" style="position: sticky; top: 24px;">
                <div class="card-header" style="background:#f8fafc;padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                    <span class="card-title" style="font-weight:800;font-size:16px;color:#0f172a;"><i class="bi bi-receipt" style="color:var(--primary);margin-right:8px;"></i> Record Expense</span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <form action="{{ route('dashboard.expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div style="margin-bottom:16px;">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Expense Category *</label>
                            <select name="expense_category_id" class="form-control" style="width:100%;" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div style="margin-bottom:16px;">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Amount (৳) *</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#64748b;font-weight:600;">৳</span>
                                <input type="number" name="amount" step="0.01" min="0.01" class="form-control" style="width:100%;padding-left:28px;" placeholder="0.00" required>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                            <div>
                                <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Date *</label>
                                <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control" style="width:100%;" required>
                            </div>
                            
                            <div>
                                <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Method *</label>
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
                            <input type="text" name="reference_no" class="form-control" placeholder="Receipt or Bill No..." style="width:100%;">
                        </div>

                        <div style="margin-bottom:16px;">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Description / Notes</label>
                            <textarea name="description" class="form-control" style="width:100%;" rows="2" placeholder="What was this expense for?"></textarea>
                        </div>

                        <div style="margin-bottom:20px;">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:#334155;margin-bottom:4px;">Attachment</label>
                            <input type="file" name="attachment" class="form-control" style="width:100%;padding:8px;" accept=".jpg,.jpeg,.png,.pdf">
                            <small style="color:#94a3b8;display:block;margin-top:4px;font-size:11px;">Max: 2MB (JPG, PNG, PDF)</small>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;padding:10px;font-weight:700;">
                            <i class="bi bi-save"></i> Save Expense
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT PANEL: Expense List & Filters -->
            <div style="display:flex; flex-direction:column; gap:24px;">
                
                <!-- Filter Card -->
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.expenses.index') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                            <div style="flex:1;min-width:140px;">
                                <label class="form-label" style="font-size:12px;color:#64748b;">Category</label>
                                <select name="expense_category_id" class="form-control" style="padding:6px 10px;font-size:13px;">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ request('expense_category_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div style="flex:1;min-width:130px;">
                                <label class="form-label" style="font-size:12px;color:#64748b;">Method</label>
                                <select name="payment_method" class="form-control" style="padding:6px 10px;font-size:13px;">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="mobile_banking" {{ request('payment_method') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                                </select>
                            </div>

                            <div style="flex:1;min-width:130px;">
                                <label class="form-label" style="font-size:12px;color:#64748b;">Start Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" style="padding:6px 10px;font-size:13px;">
                            </div>

                            <div style="flex:1;min-width:130px;">
                                <label class="form-label" style="font-size:12px;color:#64748b;">End Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="padding:6px 10px;font-size:13px;">
                            </div>

                            <div style="flex:2;min-width:180px;">
                                <label class="form-label" style="font-size:12px;color:#64748b;">Search</label>
                                <div style="display:flex;gap:8px;">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Ref/Desc..." class="form-control" style="padding:6px 10px;font-size:13px;">
                                    <button type="submit" class="btn btn-primary" style="padding:6px 14px;font-size:13px;">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                    @if(request()->anyFilled(['expense_category_id', 'payment_method', 'start_date', 'end_date', 'search']))
                                        <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-outline" style="padding:6px 14px;font-size:13px;" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card">
                    <div class="card-body" style="padding:0;">
                        <div style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:700;">
                                        <th style="padding:14px 16px;">Ref No.</th>
                                        <th style="padding:14px 16px;">Date</th>
                                        <th style="padding:14px 16px;">Category</th>
                                        <th style="padding:14px 16px;">Description</th>
                                        <th style="padding:14px 16px;">Method</th>
                                        <th style="padding:14px 16px;text-align:right;">Amount</th>
                                        <th style="padding:14px 16px;text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody style="color:#334155;">
                                    @forelse($expenses as $expense)
                                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                            <td style="padding:14px 16px;font-weight:600;color:#64748b;">{{ $expense->reference_no ?: '#EXP-' . sprintf('%04d', $expense->id) }}</td>
                                            <td style="padding:14px 16px;color:#475569;">{{ $expense->expense_date->format('M d, Y') }}</td>
                                            <td style="padding:14px 16px;font-weight:600;color:#0f172a;">{{ $expense->category->name }}</td>
                                            <td style="padding:14px 16px;color:#64748b;">{{ str($expense->description)->limit(30) }}</td>
                                            <td style="padding:14px 16px;text-transform:capitalize;">
                                                <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;">
                                                    {{ str_replace('_', ' ', $expense->payment_method) }}
                                                </span>
                                            </td>
                                            <td style="padding:14px 16px;text-align:right;font-weight:700;color:#ef4444;font-size:14px;">৳ {{ number_format($expense->amount, 2) }}</td>
                                            <td style="padding:14px 16px;text-align:center;">
                                                <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                                    <a href="{{ route('dashboard.expenses.show', $expense) }}" style="color:#0ea5e9;font-size:15px;padding:4px;" title="View Expense"><i class="bi bi-eye"></i></a>
                                                    <a href="{{ route('dashboard.expenses.edit', $expense) }}" style="color:#6366f1;font-size:15px;padding:4px;" title="Edit Expense"><i class="bi bi-pencil-square"></i></a>
                                                    <form method="POST" action="{{ route('dashboard.expenses.destroy', $expense) }}" id="delete-form-{{ $expense->id }}" style="margin:0;display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete({{ $expense->id }})" style="background:none;border:none;color:#ef4444;font-size:15px;cursor:pointer;padding:4px;" title="Delete Expense">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" style="padding:60px 20px;text-align:center;color:#64748b;">
                                                <i class="bi bi-receipt" style="font-size:40px;display:block;margin-bottom:16px;color:#cbd5e1;"></i>
                                                <div style="font-size:15px;font-weight:600;color:#475569;">No expenses recorded yet</div>
                                                <p style="font-size:13px;margin-top:4px;">Use the form on the left to add your first expense.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($expenses->hasPages())
                        <div style="padding:12px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;">
                            {{ $expenses->links() }}
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Expense?',
                text: "This will permanently remove the expense record.",
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
