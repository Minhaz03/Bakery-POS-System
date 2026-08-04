<x-layouts.admin title="Edit Expense">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('dashboard.expenses.index') }}" style="color:#64748b;text-decoration:none;font-size:18px;"><i class="bi bi-arrow-left"></i></a>
            <h2 class="topbar-title">Edit Expense</h2>
        </div>
    </div>

    <div class="page-content">
        @if ($errors->any())
            <div style="background:#fee2e2;color:#b91c1c;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;border:1px solid #fecaca;">
                <ul style="margin:0;padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card" style="max-width:800px;margin:0 auto;">
            <div class="card-body" style="padding:32px;">
                <form action="{{ route('dashboard.expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                        <div>
                            <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Expense Category *</label>
                            <select name="expense_category_id" class="form-control" style="width:100%;" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Amount (৳) *</label>
                            <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0.01" class="form-control" style="width:100%;" required>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                        <div>
                            <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Expense Date *</label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" class="form-control" style="width:100%;" required>
                        </div>
                        
                        <div>
                            <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Payment Method *</label>
                            <select name="payment_method" class="form-control" style="width:100%;" required>
                                <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method', $expense->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method', $expense->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="mobile_banking" {{ old('payment_method', $expense->payment_method) == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:24px;">
                        <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Reference No (Optional)</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no', $expense->reference_no) }}" class="form-control" style="width:100%;">
                    </div>

                    <div style="margin-bottom:24px;">
                        <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Description / Notes</label>
                        <textarea name="description" class="form-control" style="width:100%;" rows="4">{{ old('description', $expense->description) }}</textarea>
                    </div>

                    <div style="margin-bottom:32px;">
                        <label class="form-label" style="font-size:14px;font-weight:600;color:#334155;margin-bottom:8px;">Attachment (Receipt / Bill)</label>
                        @if($expense->attachment)
                            <div style="margin-bottom:12px;padding:12px;border:1px solid #e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:space-between;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <i class="bi bi-file-earmark-text" style="color:#6366f1;font-size:20px;"></i>
                                    <span style="font-size:13px;color:#475569;font-weight:500;">Current Attachment</span>
                                </div>
                                <a href="{{ Storage::url($expense->attachment) }}" target="_blank" class="btn btn-sm btn-outline">View File</a>
                            </div>
                        @endif
                        <input type="file" name="attachment" class="form-control" style="width:100%;padding:8px;" accept=".jpg,.jpeg,.png,.pdf">
                        <small style="color:#64748b;display:block;margin-top:6px;">Max size: 2MB. Allowed formats: JPG, PNG, PDF. Leave blank to keep current attachment.</small>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:12px;border-top:1px solid #e2e8f0;padding-top:24px;">
                        <a href="{{ route('dashboard.expenses.index') }}" class="btn btn-outline" style="padding:10px 24px;text-decoration:none;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="padding:10px 32px;font-size:14.5px;">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
