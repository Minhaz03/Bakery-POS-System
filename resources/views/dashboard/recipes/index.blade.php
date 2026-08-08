<x-layouts.admin title="Recipes Book">

    {{-- Page Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;"><i class="bi bi-journal-richtext"></i> Recipes Book</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage baking formulations, ingredients, preparation durations, and raw product cost estimations.</p>
        </div>
        <a href="{{ route('dashboard.recipes.create') }}" class="btn btn-primary" style="text-decoration:none;">
            <i class="bi bi-plus-circle"></i> Add New Recipe
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="card" style="margin-bottom:20px;border-color:#86efac;background:#f0fdf4;color:#15803d;">
            <div class="card-body" style="padding:14px 20px;display:flex;align-items:center;gap:10px;">
                <i class="bi bi-check-circle-fill" style="font-size:18px;"></i>
                <span style="font-weight:600;font-size:14px;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.recipes') }}" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:0;">
                <div style="flex:1;min-width:220px;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search recipe name or category..." style="padding-left:36px;">
                    <i class="bi bi-search" style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <select name="category" class="form-control" style="width:180px;cursor:pointer;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control" style="width:150px;cursor:pointer;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                @if(request()->anyFilled(['search', 'category', 'status']))
                    <a href="{{ route('dashboard.recipes') }}" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fecaca;text-decoration:none;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Recipe Cards Grid --}}
    @if($recipes->isEmpty())
        <div class="card">
            <div class="card-body" style="padding:64px;text-align:center;color:#94a3b8;">
                <i class="bi bi-journal-x" style="font-size:56px;display:block;margin-bottom:12px;color:#cbd5e1;"></i>
                <h4 style="margin:0 0 6px 0;font-weight:700;color:#64748b;">No Recipes Found</h4>
                <p style="margin:0 0 20px 0;font-size:13px;">No recipes match your filters, or no recipes have been added yet.</p>
                <a href="{{ route('dashboard.recipes.create') }}" class="btn btn-primary" style="text-decoration:none;">
                    <i class="bi bi-plus-circle"></i> Create First Recipe
                </a>
            </div>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(440px,1fr));gap:20px;margin-bottom:24px;">
            @foreach($recipes as $recipe)
            <div class="card" style="transition:box-shadow 0.2s;border:1px solid #e2e8f0;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.10)'" onmouseout="this.style.boxShadow=''">
                <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:linear-gradient(135deg,rgba(99,102,241,0.07),rgba(139,92,246,0.05));">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <div>
                            <span class="card-title" style="font-size:15px;font-weight:700;color:#0f172a;display:block;">{{ $recipe->name }}</span>
                            @if($recipe->category)
                                <span style="font-size:11px;background:#ede9fe;color:#7c3aed;padding:1px 8px;border-radius:99px;font-weight:600;">{{ $recipe->category }}</span>
                            @endif
                        </div>
                    </div>
                    @if($recipe->is_active)
                        <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;flex-shrink:0;"><i class="bi bi-check-circle-fill"></i> Active</span>
                    @else
                        <span style="background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;flex-shrink:0;"><i class="bi bi-pause-circle"></i> Inactive</span>
                    @endif
                </div>

                <div class="card-body" style="background:#fff;padding:16px 20px;">
                    {{-- Stats Row --}}
                    <div style="display:flex;gap:20px;margin-bottom:14px;flex-wrap:wrap;">
                        @if($recipe->prep_time)
                        <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#475569;">
                            <i class="bi bi-hourglass-split" style="color:#6366f1;"></i>
                            <span><strong>Prep:</strong> {{ $recipe->prep_time }}</span>
                        </div>
                        @endif
                        @if($recipe->bake_time)
                        <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#475569;">
                            <i class="bi bi-fire" style="color:#ef4444;"></i>
                            <span><strong>Bake:</strong> {{ $recipe->bake_time }}</span>
                        </div>
                        @endif
                        <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#475569;">
                            <i class="bi bi-boxes" style="color:#0ea5e9;"></i>
                            <span><strong>Expected Output:</strong> {{ $recipe->yield_qty }} {{ $recipe->yield_unit }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;">
                            <i class="bi bi-cash-stack" style="color:#16a34a;"></i>
                            <span style="color:#16a34a;font-weight:700;">Cost: ৳{{ number_format($recipe->estimated_cost, 2) }}</span>
                        </div>
                    </div>

                    {{-- Ingredients Preview --}}
                    @if($recipe->ingredients->count() > 0)
                    <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:10px 14px;margin-bottom:14px;">
                        <h5 style="font-size:11px;text-transform:uppercase;color:#475569;margin:0 0 6px 0;letter-spacing:0.05em;font-weight:700;"><i class="bi bi-list-check"></i> Ingredients ({{ $recipe->ingredients->count() }})</h5>
                        <p style="font-size:12.5px;color:#334155;margin:0;line-height:1.7;">
                            {{ $recipe->ingredients->take(4)->pluck('ingredient_name')->implode(', ') }}{{ $recipe->ingredients->count() > 4 ? ', ...' : '' }}
                        </p>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;font-size:13px;">
                        <a href="{{ route('dashboard.recipes.show', $recipe) }}" class="btn btn-outline btn-sm" style="text-decoration:none;"><i class="bi bi-eye"></i> View</a>
                        <a href="{{ route('dashboard.recipes.edit', $recipe) }}" class="btn btn-outline btn-sm" style="text-decoration:none;color:#6366f1;border-color:#e0e7ff;"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form id="delete-recipe-{{ $recipe->id }}" method="POST" action="{{ route('dashboard.recipes.destroy', $recipe) }}" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" onclick="confirmDeleteRecipe({{ $recipe->id }}, '{{ addslashes($recipe->name) }}')" class="btn btn-outline btn-sm" style="color:#ef4444;border-color:#fecaca;">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="margin-top:20px;">
            {{ $recipes->links() }}
        </div>
    @endif

    <script>
        function confirmDeleteRecipe(id, name) {
            Swal.fire({
                title: 'Delete Recipe?',
                html: `Are you sure you want to delete <strong>"${name}"</strong>? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: '#ffffff',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-recipe-' + id).submit();
                }
            });
        }
    </script>

</x-layouts.admin>
