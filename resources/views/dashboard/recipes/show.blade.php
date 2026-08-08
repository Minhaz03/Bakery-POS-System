<x-layouts.admin title="View Recipe – {{ $recipe->name }}">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
        <a href="{{ route('dashboard.recipes') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back</a>
        <div style="flex:1;">
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;"><i class="bi bi-journal-richtext"></i> {{ $recipe->name }}</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">
                @if($recipe->category)<span style="background:#ede9fe;color:#7c3aed;padding:1px 8px;border-radius:99px;font-size:12px;font-weight:600;margin-right:8px;">{{ $recipe->category }}</span>@endif
                Recipe Details &amp; Ingredient Breakdown
            </p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('dashboard.recipes.edit', $recipe) }}" class="btn btn-primary" style="text-decoration:none;"><i class="bi bi-pencil-square"></i> Edit Recipe</a>
            <form id="del-recipe-form" method="POST" action="{{ route('dashboard.recipes.destroy', $recipe) }}" style="display:none;">@csrf @method('DELETE')</form>
            <button type="button" onclick="confirmDeleteRecipe()" class="btn btn-outline" style="color:#ef4444;border-color:#fecaca;"><i class="bi bi-trash"></i> Delete</button>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

        {{-- LEFT --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Overview Card --}}
            <div class="card">
                <div class="card-header" style="background:linear-gradient(135deg,rgba(99,102,241,0.08),rgba(139,92,246,0.05));">
                    <span class="card-title"><i class="bi bi-grid-3x3-gap"></i> Recipe Overview</span>
                    @if($recipe->is_active)
                        <span style="margin-left:auto;background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;"><i class="bi bi-check-circle-fill"></i> Active</span>
                    @else
                        <span style="margin-left:auto;background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:700;"><i class="bi bi-pause-circle"></i> Inactive</span>
                    @endif
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px;">
                        <div style="text-align:center;background:#f8fafc;border-radius:10px;padding:14px 8px;border:1px solid #f1f5f9;">
                            <i class="bi bi-hourglass-split" style="font-size:22px;color:#6366f1;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Prep Time</div>
                            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-top:2px;">{{ $recipe->prep_time ?: '—' }}</div>
                        </div>
                        <div style="text-align:center;background:#f8fafc;border-radius:10px;padding:14px 8px;border:1px solid #f1f5f9;">
                            <i class="bi bi-fire" style="font-size:22px;color:#ef4444;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Bake Time</div>
                            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-top:2px;">{{ $recipe->bake_time ?: '—' }}</div>
                        </div>
                        <div style="text-align:center;background:#f8fafc;border-radius:10px;padding:14px 8px;border:1px solid #f1f5f9;">
                            <i class="bi bi-boxes" style="font-size:22px;color:#0ea5e9;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;">Expected Output</div>
                            <div style="font-size:14px;font-weight:700;color:#0f172a;margin-top:2px;">{{ $recipe->yield_qty }} {{ $recipe->yield_unit }}</div>
                        </div>
                        <div style="text-align:center;background:#f0fdf4;border-radius:10px;padding:14px 8px;border:1px solid #86efac;">
                            <i class="bi bi-cash-stack" style="font-size:22px;color:#16a34a;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:11px;color:#16a34a;font-weight:600;text-transform:uppercase;">Est. Cost</div>
                            <div style="font-size:14px;font-weight:800;color:#15803d;margin-top:2px;">৳{{ number_format($recipe->estimated_cost, 2) }}</div>
                        </div>
                    </div>

                    @if($recipe->description)
                        <div style="background:#f8fafc;border-radius:8px;padding:12px 16px;border:1px solid #f1f5f9;">
                            <p style="margin:0;font-size:13.5px;color:#334155;line-height:1.7;">{{ $recipe->description }}</p>
                        </div>
                    @endif

                    @if($recipe->product)
                        <div style="margin-top:14px;display:flex;align-items:center;gap:8px;font-size:13px;color:#475569;">
                            <i class="bi bi-link-45deg" style="color:#6366f1;"></i>
                            <span>Linked Product: <strong style="color:#0f172a;">{{ $recipe->product->name }}</strong> ({{ $recipe->product->sku }})</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ingredients Table --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-list-check"></i> Ingredients List</span>
                    <span style="margin-left:auto;background:#ede9fe;color:#7c3aed;padding:2px 10px;border-radius:99px;font-size:12px;font-weight:700;">{{ $recipe->ingredients->count() }} items</span>
                </div>
                <div class="card-body" style="padding:0;overflow-x:auto;">
                    @if($recipe->ingredients->count() > 0)
                    <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                                <th style="padding:12px 20px;">#</th>
                                <th style="padding:12px 20px;">Ingredient</th>
                                <th style="padding:12px 20px;text-align:center;">Quantity</th>
                                <th style="padding:12px 20px;text-align:right;">Unit Cost (৳)</th>
                                <th style="padding:12px 20px;text-align:right;">Subtotal (৳)</th>
                            </tr>
                        </thead>
                        <tbody style="color:#334155;">
                            @foreach($recipe->ingredients as $i => $ingredient)
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.1s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <td style="padding:12px 20px;color:#94a3b8;font-weight:600;">{{ $i + 1 }}</td>
                                <td style="padding:12px 20px;">
                                    <span style="font-weight:600;color:#0f172a;display:block;">{{ $ingredient->ingredient_name }}</span>
                                    @if($ingredient->product)
                                        <span style="font-size:11px;color:#6366f1;font-family:monospace;">{{ $ingredient->product->sku }}</span>
                                    @endif
                                    @if($ingredient->notes)
                                        <span style="font-size:11px;color:#94a3b8;display:block;font-style:italic;">{{ $ingredient->notes }}</span>
                                    @endif
                                </td>
                                <td style="padding:12px 20px;text-align:center;font-weight:600;">{{ number_format($ingredient->quantity, 3) }} <span style="color:#64748b;font-size:12px;">{{ $ingredient->unit->short_name ?? $ingredient->unit->name ?? '' }}</span></td>
                                <td style="padding:12px 20px;text-align:right;">৳{{ number_format($ingredient->unit_cost, 2) }}</td>
                                <td style="padding:12px 20px;text-align:right;font-weight:700;color:#0f172a;">৳{{ number_format($ingredient->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f0fdf4;border-top:2px solid #86efac;">
                                <td colspan="4" style="padding:12px 20px;font-weight:700;color:#15803d;text-align:right;">Total Estimated Cost:</td>
                                <td style="padding:12px 20px;font-weight:800;color:#15803d;text-align:right;font-size:15px;">৳{{ number_format($recipe->estimated_cost, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    @else
                        <div style="padding:32px;text-align:center;color:#94a3b8;">No ingredients recorded.</div>
                    @endif
                </div>
            </div>

            {{-- Instructions --}}
            @if($recipe->instructions)
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-card-text"></i> Preparation Instructions</span>
                </div>
                <div class="card-body">
                    <div style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:16px 20px;white-space:pre-line;font-size:13.5px;color:#334155;line-height:1.8;">{{ $recipe->instructions }}</div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Cost per unit --}}
            @if($recipe->yield_qty > 0 && $recipe->estimated_cost > 0)
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-calculator"></i> Cost Analysis</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Total Ingredient Cost</span>
                        <span style="font-weight:700;color:#0f172a;">৳{{ number_format($recipe->estimated_cost, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Expected Output</span>
                        <span style="font-weight:700;color:#0f172a;">{{ $recipe->yield_qty }} {{ $recipe->yield_unit }}</span>
                    </div>
                    <hr style="border:0;border-top:1px solid #e2e8f0;margin:4px 0;">
                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="font-weight:700;color:#0f172a;">Cost per Unit</span>
                        <span style="font-weight:800;color:#6366f1;font-size:16px;">৳{{ number_format($recipe->estimated_cost / max($recipe->yield_qty, 1), 2) }}</span>
                    </div>
                    @if($recipe->product && $recipe->product->sale_price > 0)
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;color:#475569;">
                        <span>Selling Price</span>
                        <span style="font-weight:700;color:#0f172a;">৳{{ number_format($recipe->product->sale_price, 2) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:14px;">
                        <span style="font-weight:700;color:#16a34a;">Gross Margin</span>
                        @php
                            $margin = $recipe->product->sale_price - ($recipe->estimated_cost / max($recipe->yield_qty, 1));
                            $pct = $recipe->product->sale_price > 0 ? ($margin / $recipe->product->sale_price) * 100 : 0;
                        @endphp
                        <span style="font-weight:800;color:#15803d;">৳{{ number_format($margin, 2) }} ({{ number_format($pct, 1) }}%)</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Baker's Notes --}}
            @if($recipe->notes)
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-sticky"></i> Baker's Notes</span>
                </div>
                <div class="card-body">
                    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:14px 16px;font-size:13.5px;color:#78350f;line-height:1.7;white-space:pre-line;">{{ $recipe->notes }}</div>
                </div>
            </div>
            @endif

            {{-- Meta Info --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-clock-history"></i> Record Info</span>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:10px;font-size:13px;color:#475569;">
                    <div style="display:flex;justify-content:space-between;">
                        <span>Created</span>
                        <span style="font-weight:600;color:#0f172a;">{{ $recipe->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span>Last Updated</span>
                        <span style="font-weight:600;color:#0f172a;">{{ $recipe->updated_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div style="display:flex;flex-direction:column;gap:10px;">
                <a href="{{ route('dashboard.recipes.edit', $recipe) }}" class="btn btn-primary" style="text-decoration:none;justify-content:center;"><i class="bi bi-pencil-square"></i> Edit This Recipe</a>
                <a href="{{ route('dashboard.recipes.create') }}" class="btn btn-outline" style="text-decoration:none;justify-content:center;text-align:center;"><i class="bi bi-plus-circle"></i> Add Another Recipe</a>
                <button type="button" onclick="confirmDeleteRecipe()" class="btn btn-outline" style="color:#ef4444;border-color:#fecaca;justify-content:center;"><i class="bi bi-trash"></i> Delete Recipe</button>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteRecipe() {
            Swal.fire({
                title: 'Delete Recipe?',
                html: 'Are you sure you want to delete <strong>"{{ addslashes($recipe->name) }}"</strong>? This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: '#ffffff',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('del-recipe-form').submit();
                }
            });
        }
    </script>

</x-layouts.admin>
