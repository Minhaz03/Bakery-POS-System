<x-layouts.admin title="Edit Recipe – {{ $recipe->name }}">

    <!-- Select2 Assets & Custom Styling -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 4px 8px;
            font-size: 13px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            line-height: 28px;
            padding-left: 2px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }
        .select2-dropdown {
            border-color: #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }
        .select2-results__option {
            font-size: 13px;
            padding: 8px 12px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6366f1;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap;">
        <a href="{{ route('dashboard.recipes') }}" class="btn-topbar" style="padding:8px 12px;text-decoration:none;"><i class="bi bi-arrow-left"></i> Back to Recipes</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;"><i class="bi bi-pencil-square"></i> Edit Recipe</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Editing: <strong>{{ $recipe->name }}</strong></p>
        </div>
        <div style="margin-left:auto;display:flex;gap:10px;">
            <a href="{{ route('dashboard.recipes.show', $recipe) }}" class="btn btn-outline btn-sm" style="text-decoration:none;"><i class="bi bi-eye"></i> View</a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="card" style="margin-bottom:20px;border-color:#fca5a5;background:#fef2f2;color:#991b1b;">
            <div class="card-body" style="padding:16px 20px;">
                <h5 style="margin:0 0 8px 0;font-weight:700;"><i class="bi bi-exclamation-triangle-fill"></i> Please correct the errors below:</h5>
                <ul style="margin:0;padding-left:20px;font-size:13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.recipes.update', $recipe) }}" id="recipe-form">
        @csrf
        @method('PUT')

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

            {{-- LEFT COLUMN --}}
            <div style="display:flex;flex-direction:column;gap:24px;">

                {{-- Basic Info --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-info-circle"></i> Basic Details</span>
                    </div>
                    <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group" style="grid-column:span 2;">
                            <label class="form-label" for="name">Recipe Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $recipe->name) }}" required placeholder="e.g. Classic Sourdough Bread">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="category">Category</label>
                            <input type="text" name="category" id="category" class="form-control" value="{{ old('category', $recipe->category) }}" list="category-suggestions" placeholder="e.g. Bread, Cake, Pastry">
                            <datalist id="category-suggestions">
                                <option value="Bread">
                                <option value="Pastry">
                                <option value="Cake">
                                <option value="Cookie">
                                <option value="Beverage">
                                <option value="Savory">
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="product_id">Linked Product (Optional)</label>
                            <select name="product_id" id="product_id" class="form-control select2" style="cursor:pointer; width:100%;">
                                <option value="">-- No linked product --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $recipe->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="grid-column:span 2;">
                            <label class="form-label" for="description">Short Description</label>
                            <textarea name="description" id="description" rows="2" class="form-control" placeholder="Brief description of this recipe...">{{ old('description', $recipe->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Timing & Expected Output --}}
                <div class="card" style="border:1px solid #e2e8f0;box-shadow:none;margin-bottom:24px;">
                    <div class="card-header" style="background:#f8fafc;padding:16px 20px;border-bottom:1px solid #e2e8f0;">
                        <span class="card-title"><i class="bi bi-hourglass-split"></i> Timing & Expected Output</span>
                    </div>
                    <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label" for="prep_time">Prep Time</label>
                            <input type="text" name="prep_time" id="prep_time" class="form-control" value="{{ old('prep_time', $recipe->prep_time) }}" placeholder="e.g. 2 hours">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bake_time">Bake Time</label>
                            <input type="text" name="bake_time" id="bake_time" class="form-control" value="{{ old('bake_time', $recipe->bake_time) }}" placeholder="e.g. 45 mins">
                        </div>
                        <div>
                            <label class="form-label" for="yield_qty">Expected Output Qty <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="yield_qty" id="yield_qty" class="form-control" value="{{ old('yield_qty', $recipe->yield_qty) }}" min="1" required>
                        </div>
                        <div>
                            <label class="form-label" for="yield_unit">Output Unit</label>
                            <input type="text" name="yield_unit" id="yield_unit" class="form-control" value="{{ old('yield_unit', $recipe->yield_unit) }}" placeholder="loaves, pcs, kg">
                        </div>
                    </div>
                </div>

                {{-- Ingredients Table --}}
                <div class="card">
                    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                        <span class="card-title"><i class="bi bi-list-check"></i> Ingredients</span>
                        <button type="button" id="btn-add-ingredient" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Ingredient</button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13px;" id="ingredients-table">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                                        <th style="padding:12px 16px;">Ingredient Name <span style="color:#ef4444;">*</span></th>
                                        <th style="padding:12px 16px;width:110px;">Quantity</th>
                                        <th style="padding:12px 16px;width:90px;">Unit</th>
                                        <th style="padding:12px 16px;width:120px;">Unit Cost (৳)</th>
                                        <th style="padding:12px 16px;width:110px;text-align:right;">Subtotal</th>
                                        <th style="padding:12px 16px;width:50px;text-align:center;">Del</th>
                                    </tr>
                                </thead>
                                <tbody id="ingredients-tbody">
                                    {{-- Existing ingredients --}}
                                    @foreach($recipe->ingredients as $ing)
                                    <tr data-row="{{ $loop->index }}" style="border-bottom:1px solid #f1f5f9;">
                                        <td style="padding:10px 14px;">
                                            <div style="display:flex;flex-direction:column;gap:6px;">
                                                <select name="ingredients[product_id][]" class="form-control ing-product-select" style="font-size:13px;width:100%;">
                                                    <option value="">-- Custom (No link) --</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}" data-name="{{ $prod->name }}" data-unit-id="{{ $prod->unit_id }}" data-base-unit-id="{{ $prod->unit?->base_unit_id }}" data-cost="{{ $prod->cost_price }}" {{ $ing->product_id == $prod->id ? 'selected' : '' }}>
                                                            {{ $prod->name }} (SKU: {{ $prod->sku }} | Cost: ৳{{ $prod->cost_price }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="ingredients[ingredient_name][]" value="{{ $ing->ingredient_name }}" class="form-control ing-name" placeholder="e.g. Bread Flour" required style="font-size:13px;">
                                            </div>
                                        </td>
                                        <td style="padding:10px 14px;">
                                            <input type="number" step="0.001" min="0" name="ingredients[quantity][]" value="{{ $ing->quantity }}" class="form-control ing-qty" style="font-size:13px;text-align:center;" required>
                                        </td>
                                        <td style="padding:10px 14px;">
                                            <select name="ingredients[unit_id][]" class="form-control ing-unit" style="font-size:13px;" required>
                                                <option value="">-- Unit --</option>
                                                @foreach($allUnits as $u)
                                                    @php
                                                        $isVisible = true;
                                                        if ($ing->product_id && $ing->product) {
                                                            $pUnitId = $ing->product->unit_id;
                                                            $pBaseId = $ing->product->unit?->base_unit_id;
                                                            $isVisible = ($u->id == $pUnitId || $u->base_unit_id == $pUnitId || ($pBaseId && ($u->id == $pBaseId || $u->base_unit_id == $pBaseId)));
                                                        }
                                                    @endphp
                                                    <option value="{{ $u->id }}" data-base-unit-id="{{ $u->base_unit_id }}" {{ $ing->unit_id == $u->id ? 'selected' : '' }} style="display:{{ $isVisible ? '' : 'none' }}" {{ $isVisible ? '' : 'disabled' }}>
                                                        {{ $u->short_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="padding:10px 14px;">
                                            <input type="number" step="0.01" min="0" name="ingredients[unit_cost][]" value="{{ $ing->unit_cost }}" class="form-control ing-cost" style="font-size:13px;text-align:right;" required>
                                        </td>
                                        <td style="padding:10px 14px;font-weight:700;color:#0f172a;text-align:right;vertical-align:middle;">
                                            ৳ <span class="ing-subtotal">{{ number_format($ing->subtotal, 2) }}</span>
                                        </td>
                                        <td style="padding:10px 14px;text-align:center;vertical-align:middle;">
                                            <button type="button" class="btn-del-row" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;" title="Remove"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @if($recipe->ingredients->count() === 0)
                                    <tr id="empty-ing-row">
                                        <td colspan="6" style="padding:32px;text-align:center;color:#94a3b8;">
                                            <i class="bi bi-basket" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                                            No ingredients added. Click "+ Add Ingredient" to start.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot id="ingredients-tfoot" style="{{ $recipe->ingredients->count() > 0 ? '' : 'display:none;' }}">
                                    <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
                                        <td colspan="4" style="padding:12px 16px;font-weight:700;color:#0f172a;text-align:right;">Estimated Ingredient Cost:</td>
                                        <td style="padding:12px 16px;font-weight:800;color:#16a34a;text-align:right;">৳ <span id="total-cost-display">{{ number_format($recipe->estimated_cost, 2) }}</span></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-card-text"></i> Preparation Instructions</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group" style="margin:0;">
                            <textarea name="instructions" id="instructions" rows="8" class="form-control" placeholder="Step 1: Mix flour and water...">{{ old('instructions', $recipe->instructions) }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div style="display:flex;flex-direction:column;gap:24px;">

                {{-- Cost Summary --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-cash-stack"></i> Cost Summary</span>
                    </div>
                    <div class="card-body" style="display:flex;flex-direction:column;gap:14px;">
                        <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:10px;padding:16px;text-align:center;">
                            <div style="font-size:11px;text-transform:uppercase;color:#16a34a;font-weight:700;letter-spacing:0.05em;margin-bottom:4px;">Estimated Total Cost</div>
                            <div style="font-size:26px;font-weight:800;color:#15803d;">৳ <span id="summary-cost">{{ number_format($recipe->estimated_cost, 2) }}</span></div>
                            <div style="font-size:11.5px;color:#4ade80;margin-top:4px;">Auto-calculated from ingredients</div>
                        </div>
                    </div>
                </div>

                {{-- Baker's Notes --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-sticky"></i> Baker's Notes</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group" style="margin:0;">
                            <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Tips, allergens, storage advice, variations...">{{ old('notes', $recipe->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="bi bi-toggle-on"></i> Status</span>
                    </div>
                    <div class="card-body">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $recipe->is_active) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#6366f1;cursor:pointer;">
                            <span style="font-weight:600;font-size:14px;color:#0f172a;">Recipe is Active</span>
                        </label>
                        <p style="font-size:12px;color:#64748b;margin:8px 0 0 28px;">Inactive recipes are hidden from production and ordering.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="bi bi-save"></i> Update Recipe</button>
                    <a href="{{ route('dashboard.recipes.show', $recipe) }}" class="btn btn-outline" style="text-decoration:none;justify-content:center;text-align:center;"><i class="bi bi-eye"></i> View Recipe</a>
                    <a href="{{ route('dashboard.recipes') }}" class="btn btn-outline" style="text-decoration:none;justify-content:center;text-align:center;">Cancel</a>
                </div>

            </div>
        </div>
    </form>

    {{-- Datalist for units removed as we now use select --}}

    <script>
    $(document).ready(function () {
        // Initialize Select2 on Linked Product
        $('#product_id').select2({
            placeholder: '-- No linked product --',
            allowClear: true,
            width: '100%'
        });

        const tbody = document.getElementById('ingredients-tbody');
        const tfoot = document.getElementById('ingredients-tfoot');
        const totalDisplay = document.getElementById('total-cost-display');
        const summaryDisplay = document.getElementById('summary-cost');

        const allUnits = @json($allUnits);
        const unitMap = {};
        allUnits.forEach(u => unitMap[u.id] = u);

        const productOptionsHTML = `
            <option value="">-- Custom (No link) --</option>
            @foreach($products as $prod)
                <option value="{{ $prod->id }}" data-name="{{ $prod->name }}" data-unit-id="{{ $prod->unit_id }}" data-base-unit-id="{{ $prod->unit?->base_unit_id }}" data-cost="{{ $prod->cost_price }}">
                    {{ $prod->name }} (SKU: {{ $prod->sku }} | Cost: ৳{{ $prod->cost_price }})
                </option>
            @endforeach
        `;

        // Bind existing rows
        tbody.querySelectorAll('tr[data-row]').forEach(function (tr) {
            bindRow(tr);
        });

        // Initial calculation
        recalc();

        document.getElementById('btn-add-ingredient').addEventListener('click', function () {
            addRow();
        });

        function addRow() {
            const existingRows = tbody.querySelectorAll('tr[data-row]');
            const newIdx = existingRows.length;

            const emptyRow = document.getElementById('empty-ing-row');
            if (emptyRow) {
                emptyRow.style.display = 'none';
            }
            tfoot.style.display = '';

            let unitOptionsHTML = '<option value="">-- Unit --</option>';
            allUnits.forEach(u => {
                unitOptionsHTML += `<option value="${u.id}" data-base-unit-id="${u.base_unit_id}">${u.short_name}</option>`;
            });

            const tr = document.createElement('tr');
            tr.setAttribute('data-row', newIdx);
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = `
                <td style="padding:10px 14px;">
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <select name="ingredients[product_id][]" class="form-control ing-product-select" style="font-size:13px;width:100%;">
                            ${productOptionsHTML}
                        </select>
                        <input type="text" name="ingredients[ingredient_name][]" value="" class="form-control ing-name" placeholder="e.g. Bread Flour" required style="font-size:13px;">
                    </div>
                </td>
                <td style="padding:10px 14px;">
                    <input type="number" step="0.001" min="0" name="ingredients[quantity][]" value="1.000" class="form-control ing-qty" style="font-size:13px;text-align:center;" required>
                </td>
                <td style="padding:10px 14px;">
                    <select name="ingredients[unit_id][]" class="form-control ing-unit" style="font-size:13px;" required>
                        ${unitOptionsHTML}
                    </select>
                </td>
                <td style="padding:10px 14px;">
                    <input type="number" step="0.01" min="0" name="ingredients[unit_cost][]" value="0.00" class="form-control ing-cost" style="font-size:13px;text-align:right;" required>
                </td>
                <td style="padding:10px 14px;font-weight:700;color:#0f172a;text-align:right;vertical-align:middle;">
                    ৳ <span class="ing-subtotal">0.00</span>
                </td>
                <td style="padding:10px 14px;text-align:center;vertical-align:middle;">
                    <button type="button" class="btn-del-row" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;" title="Remove"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
            bindRow(tr);
            recalc();
        }

        function bindRow(tr) {
            const $productSelect = $(tr).find('.ing-product-select');
            const nameInput     = tr.querySelector('.ing-name');
            const qtyInput      = tr.querySelector('.ing-qty');
            const unitInput     = tr.querySelector('.ing-unit');
            const costInput     = tr.querySelector('.ing-cost');
            const delBtn        = tr.querySelector('.btn-del-row');

            // Initialize Select2 on the new row
            $productSelect.select2({
                placeholder: '-- Custom (No link) --',
                allowClear: true,
                width: '100%'
            });

            // Keep track of the current base cost to scale on unit change
            let baseCost = parseFloat(costInput.value) || 0;
            // Note: If this is prefilled with a unit that is NOT the base unit, we would need to reverse-calculate.
            // For simplicity in Recipe create (since they select Product -> we set cost & default unit), we can just set baseCost = product base cost.
            let productBaseCost = 0;

            $productSelect.on('change', function () {
                const selectedOpt = $(this).find(':selected');
                const val = $(this).val();
                if (val && selectedOpt.length) {
                    nameInput.value = selectedOpt.attr('data-name') || '';
                    costInput.value = parseFloat(selectedOpt.attr('data-cost') || 0).toFixed(2);
                    
                    const pUnitId = selectedOpt.attr('data-unit-id');
                    const pBaseUnitId = selectedOpt.attr('data-base-unit-id');
                    
                    // The cost provided by the product is based on pUnitId.
                    const productCost = parseFloat(selectedOpt.attr('data-cost') || 0);
                    const pUnitObj = unitMap[pUnitId];
                    productBaseCost = productCost;
                    if (pUnitObj && pUnitObj.base_unit_id) {
                        if (pUnitObj.operator === '*') {
                            productBaseCost = productCost / parseFloat(pUnitObj.conversion_rate);
                        } else {
                            productBaseCost = productCost * parseFloat(pUnitObj.conversion_rate);
                        }
                    }
                    baseCost = productBaseCost;
                    
                    // Filter unit dropdown
                    Array.from(unitInput.options).forEach(opt => {
                        if (opt.value === "") return;
                        const uId = opt.value;
                        const uBaseId = opt.getAttribute('data-base-unit-id');
                        
                        if (uId == pUnitId || uBaseId == pUnitId || (pBaseUnitId && (uId == pBaseUnitId || uBaseId == pBaseUnitId))) {
                            opt.style.display = '';
                            opt.disabled = false;
                        } else {
                            opt.style.display = 'none';
                            opt.disabled = true;
                        }
                    });

                    if (pUnitId) {
                        unitInput.value = pUnitId;
                    }
                } else {
                    Array.from(unitInput.options).forEach(opt => {
                        opt.style.display = '';
                        opt.disabled = false;
                    });
                }
                recalc();
            });

            unitInput.addEventListener('change', function() {
                const selectedUnitObj = unitMap[this.value];
                if (!selectedUnitObj || baseCost === 0) return;

                let newCost = baseCost;
                if (selectedUnitObj.base_unit_id) {
                    if (selectedUnitObj.operator === '*') {
                        newCost = baseCost * parseFloat(selectedUnitObj.conversion_rate);
                    } else {
                        newCost = baseCost / parseFloat(selectedUnitObj.conversion_rate);
                    }
                }
                
                costInput.value = newCost.toFixed(4); // precision
                recalc();
            });

            qtyInput.addEventListener('input', recalc);
            costInput.addEventListener('input', recalc);
            delBtn.addEventListener('click', function () {
                $productSelect.select2('destroy');
                tr.remove();
                if (tbody.querySelectorAll('tr[data-row]').length === 0) {
                    const emptyRow = document.getElementById('empty-ing-row');
                    if (emptyRow) emptyRow.style.display = '';
                    tfoot.style.display = 'none';
                }
                recalc();
            });
        }

        function recalcRow(tr) {
            const qty  = parseFloat(tr.querySelector('.ing-qty').value) || 0;
            const cost = parseFloat(tr.querySelector('.ing-cost').value) || 0;
            tr.querySelector('.ing-subtotal').innerText = (qty * cost).toFixed(2);
        }

        function recalc() {
            let total = 0;
            tbody.querySelectorAll('tr[data-row]').forEach(function (tr) {
                recalcRow(tr);
                total += parseFloat(tr.querySelector('.ing-subtotal').innerText) || 0;
            });
            const f = total.toFixed(2);
            totalDisplay.innerText = f;
            summaryDisplay.innerText = f;
        }

        // Prevent form submit if no ingredients
        document.getElementById('recipe-form').addEventListener('submit', function (e) {
            const rows = tbody.querySelectorAll('tr[data-row]');
            if (rows.length === 0) {
                e.preventDefault();
                Swal.fire('Missing Ingredients', 'Please add at least one ingredient before saving.', 'warning');
            }
        });
    });
    </script>

</x-layouts.admin>
