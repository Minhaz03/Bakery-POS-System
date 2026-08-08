<x-layouts.admin title="Edit Production Order - {{ $order->reference_no }}">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">
                Edit Order: <span style="font-family:monospace;color:#6366f1;">{{ $order->reference_no }}</span>
            </h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Update planned production details.</p>
        </div>
        <a href="{{ route('dashboard.production') }}" class="btn btn-outline" style="color:#64748b;border-color:#e2e8f0;">
            <i class="bi bi-arrow-left"></i> Cancel & Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:20px;">
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('dashboard.production.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                    <!-- Recipe -->
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Recipe <span style="color:#ef4444">*</span></label>
                        <select name="recipe_id" class="form-select" required style="width:100%;padding:10px;border-radius:6px;border:1px solid #cbd5e1;background:#fff;">
                            <option value="">Select Recipe...</option>
                            @foreach($recipes as $recipe)
                                <option value="{{ $recipe->id }}" {{ (old('recipe_id', $order->recipe_id) == $recipe->id) ? 'selected' : '' }}>
                                    {{ $recipe->name }} (Output: {{ $recipe->yield_qty }} {{ $recipe->yield_unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Quantity -->
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Planned Quantity <span style="color:#ef4444">*</span></label>
                        <input type="number" step="0.01" name="qty" class="form-control" value="{{ old('qty', $order->planned_quantity) }}" required style="width:100%;padding:10px;border-radius:6px;border:1px solid #cbd5e1;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
                    <!-- Scheduled At -->
                    <div>
                        <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Planned Date <span style="color:#ef4444">*</span></label>
                        <input type="date" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $order->planned_date ? \Carbon\Carbon::parse($order->planned_date)->format('Y-m-d') : '') }}" required style="width:100%;padding:10px;border-radius:6px;border:1px solid #cbd5e1;">
                    </div>
                </div>

                <div style="text-align:right;">
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-weight:600;">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>
