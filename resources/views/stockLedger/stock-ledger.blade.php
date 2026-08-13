<x-layouts.admin title="Stock Ledger">
    <!-- Select2 & Flatpickr Assets -->
    <link href="{{ asset('vendor/select2/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes modalScale {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .select2-container--default .select2-selection--single {
            height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 6px 12px;
            font-size: 14px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            line-height: 30px;
            padding-left: 2px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
            right: 10px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }
        .select2-dropdown {
            border-color: #cbd5e1;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            z-index: 10000;
        }
        .select2-results__option {
            font-size: 13.5px;
            padding: 10px 14px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6366f1;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>

    <div x-data="{ showAdjustModal: false, selectedType: 'Adjustment (+)', auditDirection: 'add' }" 
         x-init="$watch('showAdjustModal', value => { if(value) { setTimeout(() => { $('#product_id').select2({ placeholder: '-- Choose a Product --', allowClear: true, width: '100%', dropdownParent: $('#product_id').parent() }); }, 50); } })" 
         style="display:contents;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <div>
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Stock Ledger</h2>
                <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Complete audit trail of inventory updates,
                    production increments, sales deductions, and adjustments.</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <a href="{{ route('dashboard.stock-ledger.export') }}" class="btn btn-outline"
                    style="text-decoration:none;display:inline-flex;align-items:center;gap:7px;">
                    <i class="bi bi-download"></i> Export Excel
                </a>
                <button @click="showAdjustModal = true" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Stock Adjustment
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-body">
                <form action="{{ route('dashboard.stock-ledger') }}" method="GET" style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;">
                    <div class="form-group" style="margin:0;flex:2;min-width:200px;">
                        <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Product</label>
                        <select name="product_id" class="form-control select2-filter" style="height:44px;border-radius:10px;width:100%;">
                            <option value=""></option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;flex:1.5;min-width:150px;">
                        <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">Movement Type</label>
                        <select name="type" class="form-control" style="height:44px;border-radius:10px;width:100%;">
                            <option value="">All Types</option>
                            <option value="Initial Stock" {{ request('type') == 'Initial Stock' ? 'selected' : '' }}>Initial Stock</option>
                            <option value="Manual Edit" {{ request('type') == 'Manual Edit' ? 'selected' : '' }}>Manual Edit</option>
                            <option value="Adjustment" {{ request('type') == 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="Production" {{ request('type') == 'Production' ? 'selected' : '' }}>Production</option>
                            <option value="Wastage" {{ request('type') == 'Wastage' ? 'selected' : '' }}>Wastage</option>
                            <option value="Stock Audit" {{ request('type') == 'Stock Audit' ? 'selected' : '' }}>Stock Audit</option>
                            <option value="Sale" {{ request('type') == 'Sale' ? 'selected' : '' }}>Sale</option>
                            <option value="Purchase" {{ request('type') == 'Purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="Stock Toggled" {{ request('type') == 'Stock Toggled' ? 'selected' : '' }}>Stock Toggled</option>
                            <option value="Product Deleted" {{ request('type') == 'Product Deleted' ? 'selected' : '' }}>Product Deleted</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;flex:1;min-width:130px;">
                        <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">From Date</label>
                        <input type="text" name="start_date" value="{{ request('start_date') }}" class="form-control flatpickr-date" placeholder="Select Date" style="height:44px;border-radius:10px;width:100%;background-color:#fff;">
                    </div>
                    <div class="form-group" style="margin:0;flex:1;min-width:130px;">
                        <label class="form-label" style="font-size:13px;font-weight:600;margin-bottom:6px;display:block;">To Date</label>
                        <input type="text" name="end_date" value="{{ request('end_date') }}" class="form-control flatpickr-date" placeholder="Select Date" style="height:44px;border-radius:10px;width:100%;background-color:#fff;">
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" class="btn btn-primary" style="height:44px;border-radius:10px;padding:0 20px;"><i class="bi bi-funnel"></i> Filter</button>
                        <a href="{{ route('dashboard.stock-ledger') }}" class="btn btn-outline" style="height:44px;border-radius:10px;padding:0 20px;display:inline-flex;align-items:center;">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="card">
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                            <th style="padding:16px 20px;">Ref ID</th>
                            <th style="padding:16px 20px;">Product</th>
                            <th style="padding:16px 20px;">Movement Type</th>
                            <th style="padding:16px 20px;text-align:center;">Quantity</th>
                            <th style="padding:16px 20px;">Updated By</th>
                            <th style="padding:16px 20px;">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody style="color:#334155;">
                        @forelse($ledger as $row)
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
                                onmouseover="this.style.background='#f8fafc'"
                                onmouseout="this.style.background='transparent'">
                                <td style="padding:14px 20px;font-family:monospace;color:#64748b;font-weight:600;">
                                    #TXN-{{ $row->id }}</td>
                                <td style="padding:14px 20px;font-weight:700;color:#0f172a;">
                                    {{ $row->product?->name ?? 'Deleted Product' }}
                                    @if ($row->product?->sku)
                                        <span
                                            style="font-size:11px;color:#94a3b8;font-weight:400;margin-left:4px;">({{ $row->product->sku }})</span>
                                    @endif
                                    @if ($row->notes)
                                        <div style="font-size:11.5px;color:#64748b;font-weight:400;margin-top:3px;">
                                            <i class="bi bi-info-circle" style="font-size:11px;"></i>
                                            {{ $row->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:14px 20px;">
                                    @if (strpos($row->type, '(+)') !== false)
                                        <span style="color:#16a34a;font-weight:600;"><i
                                                class="bi bi-arrow-up-right-circle"></i> {{ $row->type }}</span>
                                    @elseif(strpos($row->type, '(-)') !== false)
                                        <span style="color:#dc2626;font-weight:600;"><i
                                                class="bi bi-arrow-down-left-circle"></i> {{ $row->type }}</span>
                                    @else
                                        <span style="color:#ea580c;font-weight:600;"><i
                                                class="bi bi-arrow-left-right"></i> {{ $row->type }}</span>
                                    @endif
                                </td>
                                <td
                                    style="padding:14px 20px;text-align:center;font-weight:700;color:{{ $row->quantity > 0 ? '#16a34a' : '#dc2626' }}">
                                    {{ $row->quantity > 0 ? '+' : '' }}{{ number_format($row->quantity, 2) }}
                                </td>
                                <td style="padding:14px 20px;"><i class="bi bi-person-fill" style="color:#64748b;"></i>
                                    {{ $row->user?->name ?? 'System' }}</td>
                                <td style="padding:14px 20px;color:#64748b;"><i class="bi bi-clock"></i>
                                    {{ $row->created_at->format('Y-m-d h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:48px 20px;text-align:center;color:#94a3b8;">
                                    <i class="bi bi-layers-half"
                                        style="font-size:36px;display:block;margin-bottom:10px;"></i>
                                    <span style="font-size:14px;font-weight:500;">No ledger entries found. Perform a
                                        stock adjustment to add logs.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ledger->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;background:#fff;border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
                    {{ $ledger->links() }}
                </div>
            @endif
        </div>

        <!-- Stock Adjustment Modal -->
        <div x-show="showAdjustModal" class="modal-overlay" x-cloak>

            <div @click.away="showAdjustModal = false"
                style="background:#ffffff;width:100%;max-width:550px;border-radius:20px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);border:1px solid rgba(226,232,240,0.8);overflow:hidden;animation: modalScale 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);">

                <div
                    style="padding:22px 28px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg, rgba(99,102,241,0.05) 0%, rgba(139,92,246,0.02) 100%);">
                    <h3
                        style="margin:0;font-size:17.5px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:10px;">
                        <span
                            style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,0.1);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;">
                            <i class="bi bi-sliders"></i>
                        </span>
                        Stock Adjustment
                    </h3>
                    <button type="button" @click="showAdjustModal = false"
                        style="background:none;border:none;color:#94a3b8;font-size:24px;cursor:pointer;line-height:1;padding:4px;display:flex;align-items:center;justify-content:center;transition:color 0.15s;"
                        onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94a3b8'">
                        <i class="bi bi-x-lg" style="font-size:16px;"></i>
                    </button>
                </div>

                <form action="{{ route('dashboard.stock-ledger.adjust') }}" method="POST" style="margin:0;">
                    @csrf
                    <div style="padding:28px;">
                        <!-- Product Dropdown -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label" for="product_id" style="font-weight:600;margin-bottom:8px;">Select
                                Product <span style="color:var(--danger)">*</span></label>
                            <select name="product_id" id="product_id" class="form-control select2" required
                                style="height:44px;border-radius:10px;cursor:pointer;width:100%;">
                                <option value="">-- Choose a Product --</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (SKU:
                                        {{ $p->sku }}) — Stock: {{ number_format($p->stock_qty, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                            <!-- Movement Type -->
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="type"
                                    style="font-weight:600;margin-bottom:8px;">Adjustment Type <span
                                        style="color:var(--danger)">*</span></label>
                                <select name="type" id="type" x-model="selectedType" class="form-control"
                                    required style="height:44px;border-radius:10px;cursor:pointer;">
                                    <option value="Adjustment (+)">Adjustment (+)</option>
                                    <option value="Adjustment (-)">Adjustment (-)</option>
                                    <option value="Wastage (-)">Wastage (-)</option>
                                    <option value="Production (+)">Production (+)</option>
                                    <option value="Stock Audit (Adj)">Stock Audit (Adj)</option>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="qty"
                                    style="font-weight:600;margin-bottom:8px;">Quantity <span
                                        style="color:var(--danger)">*</span></label>
                                <input type="number" name="qty" id="qty" class="form-control"
                                    step="0.001" min="0.001" placeholder="e.g. 10.00" required
                                    style="height:44px;border-radius:10px;">
                            </div>
                        </div>

                        <!-- Conditional Audit Direction -->
                        <div x-show="selectedType === 'Stock Audit (Adj)'"
                            style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px;border-radius:12px;margin-bottom:20px;"
                            x-transition>
                            <label class="form-label" style="font-weight:600;margin-bottom:8px;">Audit Direction <span
                                    style="color:var(--danger)">*</span></label>
                            <div style="display:flex;gap:12px;align-items:center;">
                                <label
                                    :style="auditDirection === 'add' ?
                                        'border-color: #10b981; background: rgba(16,185,129,0.05); color: #065f46;' :
                                        'border-color: #e2e8f0; background: #ffffff; color: #475569;'"
                                    style="flex:1; display:inline-flex;align-items:center;gap:8px;font-size:13.5px;font-weight:600;padding:12px;border:1px solid;border-radius:10px;cursor:pointer;transition:all 0.15s;margin:0;">
                                    <input type="radio" name="direction" value="add" x-model="auditDirection"
                                        style="accent-color:#10b981;width:16px;height:16px;margin:0;">
                                    <span>Add to Stock (+)</span>
                                </label>
                                <label
                                    :style="auditDirection === 'subtract' ?
                                        'border-color: #ef4444; background: rgba(239,68,68,0.05); color: #991b1b;' :
                                        'border-color: #e2e8f0; background: #ffffff; color: #475569;'"
                                    style="flex:1; display:inline-flex;align-items:center;gap:8px;font-size:13.5px;font-weight:600;padding:12px;border:1px solid;border-radius:10px;cursor:pointer;transition:all 0.15s;margin:0;">
                                    <input type="radio" name="direction" value="subtract" x-model="auditDirection"
                                        style="accent-color:#ef4444;width:16px;height:16px;margin:0;">
                                    <span>Deduct Stock (-)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="notes" style="font-weight:600;margin-bottom:8px;">Notes
                                / Reason for Adjustment</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="Describe the reason for adjustment..." style="resize:none;padding-top:10px;border-radius:10px;"></textarea>
                        </div>
                    </div>

                    <div
                        style="padding:18px 28px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:12px;background:#f8fafc;">
                        <button type="button" @click="showAdjustModal = false" class="btn btn-outline"
                            style="height:42px;border-radius:10px;padding:0 20px;font-weight:600;">Cancel</button>
                        <button type="submit" class="btn btn-primary"
                            style="height:42px;border-radius:10px;padding:0 20px;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
                            <i class="bi bi-check-circle"></i> Save Adjustment
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-layouts.admin>
<script>
    $(document).ready(function() {
        $('.select2-filter').select2({
            placeholder: '-- All Products --',
            allowClear: true
        });

        flatpickr('.flatpickr-date', {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
</script>
