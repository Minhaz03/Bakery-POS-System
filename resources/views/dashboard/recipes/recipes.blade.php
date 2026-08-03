<x-layouts.admin title="Bakery Recipes">

    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Recipes Book</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage baking formulations, ingredients, preparation durations, and raw product cost estimations.</p>
        </div>
        <button class="btn btn-primary" style="margin-left:auto;">
            <i class="bi bi-plus-circle"></i> Add New Recipe
        </button>
    </div>

    <!-- Recipes Cards -->
    <div style="display:grid;grid-template-columns:1fr;gap:18px;margin-bottom:24px;">
        @foreach($recipes as $recipe)
        <div class="card">
            <div class="card-header" style="display:flex;justify-content:between;align-items:center;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(99,102,241,0.1);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;">
                        <i class="bi bi-egg-fried"></i>
                    </div>
                    <span class="card-title" style="font-size:16px;font-weight:700;">{{ $recipe['name'] }} Recipe</span>
                </div>
                <div style="margin-left:auto;display:flex;gap:12px;font-size:13px;font-weight:600;color:#475569;">
                    <span><i class="bi bi-hourglass-split"></i> {{ $recipe['prep_time'] }}</span>
                    <span style="color:#e2e8f0;">|</span>
                    <span style="color:#16a34a;"><i class="bi bi-cash-stack"></i> Cost: ৳{{ $recipe['cost'] }}</span>
                </div>
            </div>
            <div class="card-body" style="background:#fff;">
                <h4 style="font-size:13px;text-transform:uppercase;color:#475569;margin:0 0 8px 0;letter-spacing:0.04em;font-weight:700;">Ingredients List</h4>
                <p style="font-size:13.5px;color:#0f172a;line-height:1.6;margin:0 0 16px 0;background:#f8fafc;padding:12px 16px;border-radius:8px;border:1px solid #f1f5f9;font-family:system-ui;">
                    {{ $recipe['ingredients'] }}
                </p>
                <div style="display:flex;justify-content:flex-end;gap:12px;font-size:14px;font-weight:600;">
                    <a href="#" class="btn btn-outline btn-sm"><i class="bi bi-eye"></i> View Steps</a>
                    <a href="#" class="btn btn-primary btn-sm" style="background:#0f172a;"><i class="bi bi-pencil-square"></i> Edit Recipe</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</x-layouts.admin>
