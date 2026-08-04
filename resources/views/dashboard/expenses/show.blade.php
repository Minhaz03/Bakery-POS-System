<x-layouts.admin title="Expense Details">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('dashboard.expenses.index') }}" style="color:#64748b;text-decoration:none;font-size:18px;"><i class="bi bi-arrow-left"></i></a>
            <h2 class="topbar-title">Expense Details</h2>
        </div>
        <div style="display:flex;gap:12px;">
            <a href="{{ route('dashboard.expenses.edit', $expense) }}" class="btn btn-outline" style="padding:8px 16px;text-decoration:none;">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <form method="POST" action="{{ route('dashboard.expenses.destroy', $expense) }}" id="delete-form-{{ $expense->id }}" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete({{ $expense->id }})" class="btn" style="background:#fee2e2;color:#ef4444;border:none;padding:8px 16px;">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="page-content">
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
            <!-- Left Column: Details -->
            <div class="card">
                <div class="card-body" style="padding:32px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;border-bottom:1px solid #f1f5f9;padding-bottom:24px;">
                        <div>
                            <h3 style="margin:0 0 8px 0;font-size:24px;font-weight:700;color:#0f172a;">{{ $expense->category->name }}</h3>
                            <div style="font-size:14px;color:#64748b;">Reference: <span style="color:#334155;font-weight:600;">{{ $expense->reference_no ?: 'N/A' }}</span></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:32px;font-weight:700;color:#ef4444;line-height:1;">৳ {{ number_format($expense->amount, 2) }}</div>
                            <div style="font-size:13px;color:#64748b;margin-top:8px;">Total Amount</div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;">
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Date</div>
                            <div style="font-size:15px;color:#1e293b;font-weight:500;">
                                <i class="bi bi-calendar3" style="color:#64748b;margin-right:6px;"></i> 
                                {{ $expense->expense_date->format('F d, Y') }}
                            </div>
                        </div>
                        
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Payment Method</div>
                            <div style="font-size:15px;color:#1e293b;font-weight:500;text-transform:capitalize;">
                                <i class="bi bi-wallet2" style="color:#64748b;margin-right:6px;"></i> 
                                {{ str_replace('_', ' ', $expense->payment_method) }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Description / Notes</div>
                        <div style="background:#f8fafc;padding:16px;border-radius:8px;font-size:14px;color:#334155;line-height:1.6;border:1px solid #e2e8f0;min-height:80px;">
                            {!! nl2br(e($expense->description ?: 'No description provided.')) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Meta & Attachment -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <div class="card">
                    <div class="card-body" style="padding:24px;">
                        <h4 style="margin:0 0 16px 0;font-size:16px;font-weight:600;color:#0f172a;">Attachment</h4>
                        
                        @if($expense->attachment)
                            <div style="border:1px dashed #cbd5e1;border-radius:8px;padding:24px;text-align:center;background:#f8fafc;">
                                <i class="bi bi-file-earmark-check" style="font-size:40px;color:#10b981;display:block;margin-bottom:12px;"></i>
                                <div style="font-size:14px;font-weight:500;color:#334155;margin-bottom:16px;">Document Available</div>
                                <a href="{{ Storage::url($expense->attachment) }}" target="_blank" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                                    <i class="bi bi-download"></i> View / Download
                                </a>
                            </div>
                        @else
                            <div style="border:1px dashed #cbd5e1;border-radius:8px;padding:32px 24px;text-align:center;color:#94a3b8;background:#f8fafc;">
                                <i class="bi bi-file-earmark-x" style="font-size:32px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                <div style="font-size:13px;">No attachment provided</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" style="padding:24px;">
                        <h4 style="margin:0 0 16px 0;font-size:16px;font-weight:600;color:#0f172a;">Record Meta</h4>
                        
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#e0e7ff;color:#4f46e5;display:flex;align-items:center;justify-content:center;font-weight:600;">
                                {{ substr($expense->creator->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:#64748b;">Recorded by</div>
                                <div style="font-size:14px;font-weight:600;color:#1e293b;">{{ $expense->creator->name }}</div>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div style="font-size:13px;color:#64748b;">Created At</div>
                            <div style="font-size:13px;font-weight:500;color:#334155;">{{ $expense->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
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
