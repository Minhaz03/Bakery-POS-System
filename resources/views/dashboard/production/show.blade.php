<x-layouts.admin title="Order Details - {{ $order->reference_no }}">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">
                Production Order: <span style="font-family:monospace;color:#6366f1;">{{ $order->reference_no }}</span>
            </h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Full details of this production run, including ingredients consumed and output.</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('dashboard.production') }}" class="btn btn-outline" style="color:#64748b;border-color:#e2e8f0;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @if($order->status !== 'completed')
            <a href="{{ route('dashboard.production.edit', $order) }}" class="btn btn-primary" style="text-decoration:none;">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            @endif
            <form id="del-order-form" method="POST" action="{{ route('dashboard.production.destroy', $order) }}" style="display:none;">@csrf @method('DELETE')</form>
            <button type="button" onclick="confirmDeleteOrder()" class="btn btn-outline" style="color:#ef4444;border-color:#fecaca;"><i class="bi bi-trash"></i> Delete</button>
        </div>
    </div>

    <script>
        function confirmDeleteOrder() {
            Swal.fire({
                title: 'Delete Order?',
                html: `Are you sure you want to delete order <strong>{{ $order->reference_no }}</strong>?<br><br>If this order is completed, its stock deductions/additions will be reversed!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('del-order-form').submit();
                }
            })
        }
    </script>

    <!-- Order Summary Card -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header" style="background:#f8fafc;padding:16px 20px;">
            <span style="font-weight:700;font-size:16px;display:flex;align-items:center;">
                <i class="bi bi-info-circle" style="color:var(--primary);margin-right:8px;"></i> Order Overview
            </span>
        </div>
        <div class="card-body" style="padding:24px;">
            <table style="width:100%;border-collapse:collapse;font-size:14.5px;">
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;width:200px;">Reference No</td>
                    <td style="padding:14px 0;font-weight:700;color:#0f172a;font-family:monospace;">{{ $order->reference_no }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Recipe</td>
                    <td style="padding:14px 0;font-weight:700;color:#1e293b;">
                        <i class="bi bi-egg-fried" style="color:var(--primary);margin-right:6px;"></i>
                        {{ $order->recipe ? $order->recipe->name : 'Unknown Recipe' }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Planned Quantity</td>
                    <td style="padding:14px 0;font-weight:600;">{{ $order->planned_quantity }} items</td>
                </tr>
                @if($order->status === 'completed')
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Actual Quantity</td>
                    <td style="padding:14px 0;font-weight:600;">{{ $order->actual_quantity }} items</td>
                </tr>
                @endif
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Status</td>
                    <td style="padding:14px 0;">
                        @if($order->status === 'completed')
                            <span style="background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-check-circle-fill"></i> Completed
                            </span>
                        @elseif($order->status === 'in_progress')
                            <span style="background:#eff6ff;color:#1d4ed8;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-arrow-repeat"></i> In Progress
                            </span>
                        @elseif($order->status === 'cancelled')
                            <span style="background:#fee2e2;color:#ef4444;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-x-circle-fill"></i> Cancelled
                            </span>
                        @else
                            <span style="background:#f1f5f9;color:#475569;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;">
                                <i class="bi bi-calendar-event"></i> Planned
                            </span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Planned Date</td>
                    <td style="padding:14px 0;color:#64748b;">
                        <i class="bi bi-clock"></i> {{ $order->planned_date ? \Carbon\Carbon::parse($order->planned_date)->format('Y-m-d') : '—' }}
                    </td>
                </tr>
                @if($order->produced_at)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Produced At</td>
                    <td style="padding:14px 0;color:#15803d;font-weight:600;">
                        <i class="bi bi-check2-circle"></i> {{ \Carbon\Carbon::parse($order->produced_at)->format('Y-m-d h:i A') }}
                    </td>
                </tr>
                @endif
                
                @php $firstBatch = $order->batches->first(); @endphp
                @if($firstBatch)
                    @if($firstBatch->manufacturing_date)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 0;color:#64748b;font-weight:500;">Manufacturing Date</td>
                        <td style="padding:14px 0;">{{ \Carbon\Carbon::parse($firstBatch->manufacturing_date)->format('Y-m-d') }}</td>
                    </tr>
                    @endif
                    @if($firstBatch->expiry_date)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 0;color:#64748b;font-weight:500;">Expiry Date</td>
                        <td style="padding:14px 0;color:#dc2626;font-weight:600;">{{ \Carbon\Carbon::parse($firstBatch->expiry_date)->format('Y-m-d') }}</td>
                    </tr>
                    @endif
                @endif

                @php $totalWaste = $order->ingredients->sum('waste_qty'); @endphp
                @if($totalWaste > 0)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Total Wastage</td>
                    <td style="padding:14px 0;color:#d97706;font-weight:600;">
                        {{ $totalWaste }} items
                        @if($order->notes)
                            <span style="color:#94a3b8;font-weight:400;"> — {{ $order->notes }}</span>
                        @endif
                    </td>
                </tr>
                @endif
                
                @if($order->recipe && $order->recipe->product)
                <tr>
                    <td style="padding:14px 0;color:#64748b;font-weight:500;">Output Product</td>
                    <td style="padding:14px 0;font-weight:700;color:#6366f1;">
                        <i class="bi bi-box-seam"></i>
                        {{ $order->recipe->product->name }}
                        @if($order->status === 'completed')
                            <span style="font-weight:400;color:#64748b;font-size:13px;margin-left:4px;">
                                (+{{ $order->actual_quantity }} units added to stock)
                            </span>
                        @endif
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Ingredients Consumed -->
    @if($order->ingredients->count() > 0)
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
                    @foreach($order->ingredients as $consumption)
                    @php 
                        $qty = $consumption->consumed_qty ?? $consumption->required_qty;
                        $unitCost = $consumption->ingredient->cost_price ?? 0;
                        $totalCost = $qty * $unitCost;
                        $grandTotal += $totalCost; 
                    @endphp
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:14px 20px;font-weight:600;">
                            <i class="bi bi-box" style="color:#94a3b8;margin-right:6px;"></i>
                            {{ $consumption->ingredient ? $consumption->ingredient->name : 'Unknown' }}
                        </td>
                        <td style="padding:14px 20px;text-align:center;">{{ number_format($qty, 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;">৳ {{ number_format($unitCost, 2) }}</td>
                        <td style="padding:14px 20px;text-align:right;font-weight:700;">৳ {{ number_format($totalCost, 2) }}</td>
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
            <span style="font-size:14px;">No consumption records found for this order.</span>
        </div>
    </div>
    @endif

</x-layouts.admin>
