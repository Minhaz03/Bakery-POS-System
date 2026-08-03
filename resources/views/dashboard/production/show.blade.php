<x-layouts.admin title="Batch Details - {{ $batch->batch_code }}">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">
                Production Batch: <span style="font-family:monospace;color:#6366f1;">{{ $batch->batch_code }}</span>
            </h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Full details of this production run, including ingredients consumed and output.</p>
        </div>
        <a href="{{ route('dashboard.production') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Back to Production
        </a>
    </div>

    <!-- Batch Summary Card -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="background:#f8fafc;padding:16px 20px;">
            <span style="font-weight:700;font-size:16px;display:flex;align-items:center;">
                <i class="bi bi-info-circle" style="color:var(--primary);margin-right:8px;"></i> Batch Overview
            </span>
        </div>
        <div class="card-body" style="padding:24px;">
            <table style="width:100%;border-collapse:collapse;font-size:14.5px;">
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;width:200px;">Batch Code</td>
                    <td style="padding:14px 0;font-weight:700;color:#0f172a;font-family:monospace;">{{ $batch->batch_code }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Recipe</td>
                    <td style="padding:14px 0;font-weight:700;color:#1e293b;">
                        <i class="bi bi-egg-fried" style="color:var(--primary);margin-right:6px;"></i>
                        {{ $batch->recipe ? $batch->recipe->name : 'Unknown Recipe' }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Target Quantity</td>
                    <td style="padding:14px 0;font-weight:600;">{{ $batch->qty }} items</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Status</td>
                    <td style="padding:14px 0;">
                        @if($batch->status === 'Completed')
                            <span style="background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-check-circle-fill"></i> Completed
                            </span>
                        @elseif($batch->status === 'In Progress')
                            <span style="background:#eff6ff;color:#1d4ed8;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-arrow-repeat"></i> In Progress
                            </span>
                        @elseif($batch->status === 'Cancelled')
                            <span style="background:#fee2e2;color:#ef4444;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-x-circle-fill"></i> Cancelled
                            </span>
                        @else
                            <span style="background:#f1f5f9;color:#475569;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-calendar-event"></i> Scheduled
                            </span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Scheduled At</td>
                    <td style="padding:14px 0;color:#64748b;">
                        <i class="bi bi-clock"></i> {{ $batch->scheduled_at ? $batch->scheduled_at->format('Y-m-d h:i A') : '—' }}
                    </td>
                </tr>
                @if($batch->completed_at)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Completed At</td>
                    <td style="padding:14px 0;color:#15803d;font-weight:600;">
                        <i class="bi bi-check2-circle"></i> {{ $batch->completed_at->format('Y-m-d h:i A') }}
                    </td>
                </tr>
                @endif
                @if($batch->manufacturing_date)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Manufacturing Date</td>
                    <td style="padding:14px 0;">{{ $batch->manufacturing_date }}</td>
                </tr>
                @endif
                @if($batch->expiry_date)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Expiry Date</td>
                    <td style="padding:14px 0;color:#dc2626;font-weight:600;">{{ $batch->expiry_date }}</td>
                </tr>
                @endif
                @if($batch->wastage_qty > 0)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Wastage</td>
                    <td style="padding:14px 0;color:#d97706;font-weight:600;">
                        {{ $batch->wastage_qty }} items
                        @if($batch->wastage_notes)
                            <span style="color:#94a3b8;font-weight:400;"> — {{ $batch->wastage_notes }}</span>
                        @endif
                    </td>
                </tr>
                @endif
                @if($batch->recipe && $batch->recipe->product)
                <tr>
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Output Product</td>
                    <td style="padding:14px 0;font-weight:700;color:#6366f1;">
                        <i class="bi bi-box-seam"></i>
                        {{ $batch->recipe->product->name }}
                        @if($batch->status === 'Completed')
                            <span style="font-weight:400;color:#64748b;font-size:13px;margin-left:4px;">
                                (+{{ $batch->qty - ($batch->wastage_qty ?? 0) }} units added to stock)
                            </span>
                        @endif
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Ingredients Consumed -->
    @if($batch->consumptions->count() > 0)
    <div class="card">
        <div class="card-header" style="background:#f8fafc;padding:16px 20px;">
            <span style="font-weight:700;font-size:16px;display:flex;align-items:center;">
                <i class="bi bi-basket" style="color:#d97706;margin-right:8px;"></i> Raw Materials Consumed
            </span>
        </div>
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13.5px;text-align:left;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                        <th style="padding:14px 20px;">Ingredient</th>
                        <th style="padding:14px 20px;text-align:center;">Quantity Used</th>
                        <th style="padding:14px 20px;text-align:right;">Unit Cost</th>
                        <th style="padding:14px 20px;text-align:right;">Total Cost</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @php $grandTotal = 0; @endphp
                    @foreach($batch->consumptions as $consumption)
                    @php $grandTotal += $consumption->total_cost; @endphp
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 20px;font-weight:600;">
                            <i class="bi bi-box" style="color:#94a3b8;margin-right:6px;"></i>
                            {{ $consumption->product ? $consumption->product->name : 'Unknown' }}
                        </td>
                        <td style="padding:14px 20px;text-align:center;">{{ number_format($consumption->qty, 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;">৳ {{ number_format($consumption->unit_cost, 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;">৳ {{ number_format($consumption->total_cost, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
                        <td colspan="3" style="padding:14px 20px;font-weight:700;color:#0f172a;text-align:right;">Total Material Cost</td>
                        <td style="padding:14px 20px;font-weight:800;color:#6366f1;text-align:right;font-size:16px;">৳ {{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body" style="padding:40px;text-align:center;color:#94a3b8;">
            <i class="bi bi-basket" style="font-size:36px;display:block;margin-bottom:10px;"></i>
            <span style="font-size:14px;">No consumption records found for this batch.</span>
        </div>
    </div>
    @endif

</x-layouts.admin>
