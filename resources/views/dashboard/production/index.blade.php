<x-layouts.admin title="Baking Production">

    <div x-data="{ showModal: false }" style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Production Batches</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Monitor active baking kitchen batches,
                quantities, scheduled timers, and statuses.</p>
        </div>
        <button class="btn btn-primary" @click="showModal = true" style="margin-left:auto;">
            <i class="bi bi-plus-circle"></i> Create Production Batch
        </button>

        <!-- Create Batch Modal -->
        <div x-show="showModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;">
            <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;padding:20px;">
                <div class="card" @click.outside="showModal = false"
                    style="width:100%;max-width:500px;box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                    <div class="card-header" style="justify-content:space-between;padding:16px 20px;">
                        <span style="font-weight:700;font-size:16px;">New Production Batch</span>
                        <button @click="showModal = false"
                            style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;"><i
                                class="bi bi-x"></i></button>
                    </div>
                    <form action="{{ route('dashboard.production.store') }}" method="POST">
                        @csrf
                        <div class="card-body" style="padding:20px;">
                            <div class="form-group">
                                <label class="form-label">Recipe <span style="color:var(--danger)">*</span></label>
                                <select name="recipe_id" class="form-control" required>
                                    <option value="">Select Recipe...</option>
                                    @foreach ($recipes as $recipe)
                                        <option value="{{ $recipe->id }}">{{ $recipe->name }} (Output:
                                            {{ $recipe->yield_qty }} {{ $recipe->yield_unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Target Quantity (Units/Batches) <span
                                        style="color:var(--danger)">*</span></label>
                                <input type="number" step="0.01" name="qty" class="form-control"
                                    placeholder="e.g. 50" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Production Schedule Date <span
                                        style="color:var(--danger)">*</span></label>
                                <input type="datetime-local" name="scheduled_at" class="form-control"
                                    value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                        <div
                            style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
                            <button type="button" class="btn btn-outline" @click="showModal = false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Schedule Batch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Production Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:16px 20px;">Batch ID</th>
                        <th style="padding:16px 20px;">Recipe Name</th>
                        <th style="padding:16px 20px;text-align:center;">Target Qty</th>
                        <th style="padding:16px 20px;text-align:center;">Status</th>
                        <th style="padding:16px 20px;">Date & Time</th>
                        <th style="padding:16px 20px;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @foreach ($batches as $batch)
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
                            onmouseover="this.style.background='#f8fafc'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 20px;font-weight:700;color:#0f172a;font-family:monospace;">
                                {{ $batch['id'] }}</td>
                            <td style="padding:14px 20px;font-weight:700;color:#1e293b;">
                                <i class="bi bi-egg-fried"
                                    style="color:var(--primary);margin-right:6px;"></i>{{ $batch['recipe'] }}
                            </td>
                            <td style="padding:14px 20px;text-align:center;font-weight:600;">{{ $batch['qty'] }} items
                            </td>
                            <td style="padding:14px 20px;text-align:center;">
                                @if ($batch['status'] === 'Completed')
                                    <span
                                        style="background:#dcfce7;color:#15803d;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i
                                            class="bi bi-check-circle-fill"></i> Completed</span>
                                @elseif($batch['status'] === 'In Progress')
                                    <span
                                        style="background:#eff6ff;color:#1d4ed8;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i
                                            class="bi bi-arrow-repeat"
                                            style="display:inline-block;animation:spin 2s linear infinite;"></i> In
                                        Progress</span>
                                @elseif($batch['status'] === 'Cancelled')
                                    <span
                                        style="background:#fee2e2;color:#ef4444;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i
                                            class="bi bi-x-circle-fill"></i> Cancelled</span>
                                @else
                                    <span
                                        style="background:#f1f5f9;color:#475569;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;display:inline-block;"><i
                                            class="bi bi-calendar-event"></i> Scheduled</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px;color:#64748b;"><i class="bi bi-clock"></i>
                                {{ $batch['date'] }}</td>
                            <td style="padding:14px 20px;text-align:center;font-size:16px;">
                                <a href="{{ route('dashboard.production.show', $batch['real_id']) }}"
                                    style="color:#6366f1;margin-right:12px;" title="View Details"><i
                                        class="bi bi-eye"></i></a>
                                @if ($batch['status'] !== 'Completed' && $batch['status'] !== 'Cancelled')
                                    <form id="form-complete-{{ $batch['real_id'] }}"
                                        action="{{ route('dashboard.production.complete', $batch['real_id']) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            onclick="confirmComplete({{ $batch['real_id'] }}, '{{ $batch['id'] }}')"
                                            style="background:none;border:none;color:#10b981;margin-right:12px;cursor:pointer;"
                                            title="Mark Completed"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                @endif
                                @if ($batch['status'] !== 'Completed' && $batch['status'] !== 'Cancelled')
                                    <form id="form-cancel-{{ $batch['real_id'] }}"
                                        action="{{ route('dashboard.production.cancel', $batch['real_id']) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            onclick="confirmCancel({{ $batch['real_id'] }}, '{{ $batch['id'] }}')"
                                            style="background:none;border:none;color:#d97706;margin-right:12px;cursor:pointer;"
                                            title="Cancel Order"><i class="bi bi-x-octagon"></i></button>
                                    </form>
                                @endif
                                @if ($batch['status'] !== 'Completed')
                                    <a href="{{ route('dashboard.production.edit', $batch['real_id']) }}"
                                        style="color:#0ea5e9;margin-right:12px;" title="Edit Order"><i
                                            class="bi bi-pencil-square"></i></a>
                                @endif
                                <form id="form-delete-{{ $batch['real_id'] }}"
                                    action="{{ route('dashboard.production.destroy', $batch['real_id']) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDeleteProduction({{ $batch['real_id'] }}, '{{ $batch['id'] }}')"
                                        style="background:none;border:none;color:#ef4444;cursor:pointer;"
                                        title="Delete Order"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmComplete(realId, batchId) {
            Swal.fire({
                title: 'Complete Batch',
                html: `
                    <div style="text-align:left;font-size:14px;color:#475569;">
                        <p style="margin-bottom:16px;background:#eff6ff;padding:10px;border-radius:6px;color:#1d4ed8;">Completing this batch will deduct raw materials and add finished products to inventory.</p>
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                            <div>
                                <label style="display:block;font-weight:600;margin-bottom:4px;">Manufacturing Date</label>
                                <input type="date" id="swal-mfg" class="form-control" value="{{ now()->format('Y-m-d') }}" style="width:100%;height:38px;">
                            </div>
                            <div>
                                <label style="display:block;font-weight:600;margin-bottom:4px;">Expiry Date</label>
                                <input type="date" id="swal-exp" class="form-control" style="width:100%;height:38px;">
                            </div>
                        </div>
                        
                        <div style="margin-bottom:12px;">
                            <label style="display:block;font-weight:600;margin-bottom:4px;">Wasted Qty (if any)</label>
                            <input type="number" step="0.01" id="swal-wastage" class="form-control" placeholder="0.00" style="width:100%;height:38px;">
                        </div>
                        
                        <div>
                            <label style="display:block;font-weight:600;margin-bottom:4px;">Wastage Notes</label>
                            <input type="text" id="swal-notes" class="form-control" placeholder="Reason for wastage..." style="width:100%;height:38px;">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-check2-circle"></i> Complete',
                preConfirm: () => {
                    return {
                        mfg: document.getElementById('swal-mfg').value,
                        exp: document.getElementById('swal-exp').value,
                        wastage: document.getElementById('swal-wastage').value,
                        notes: document.getElementById('swal-notes').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('form-complete-' + realId);

                    const addHiddenInput = (name, value) => {
                        if (value) {
                            let input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = name;
                            input.value = value;
                            form.appendChild(input);
                        }
                    };

                    addHiddenInput('manufacturing_date', result.value.mfg);
                    addHiddenInput('expiry_date', result.value.exp);
                    addHiddenInput('wastage_qty', result.value.wastage);
                    addHiddenInput('wastage_notes', result.value.notes);

                    form.submit();
                }
            });
        }

        function confirmCancel(realId, refId) {
            Swal.fire({
                title: 'Cancel Order?',
                text: "Are you sure you want to cancel order " + refId + "?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-cancel-' + realId).submit();
                }
            })
        }

        function confirmDeleteProduction(realId, refId) {
            Swal.fire({
                title: 'Delete Order?',
                html: `Are you sure you want to delete order <strong>${refId}</strong>?<br><br>If this order is completed, its stock deductions/additions will be reversed!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-' + realId).submit();
                }
            })
        }
    </script>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>

</x-layouts.admin>
