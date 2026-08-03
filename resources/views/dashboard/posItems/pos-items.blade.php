<x-layouts.admin title="POS Items">
    <style>
        /* -- iOS toggle -- */
        .status-toggle-switch {
            position: relative;
            display: inline-block;
            width: 42px;
            height: 22px;
            cursor: pointer;
            margin-bottom: 0;
            vertical-align: middle;
        }

        .status-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .status-toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ef4444;
            border: 1px solid #dc2626;
            transition: .25s ease;
            border-radius: 22px;
        }

        .status-toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .25s ease;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .status-toggle-switch input:checked+.status-toggle-slider {
            background-color: #22c55e;
            border-color: #16a34a;
        }

        .status-toggle-switch input:checked+.status-toggle-slider:before {
            transform: translateX(20px);
        }

        /* -- View Details Slide-over -- */
        .pos-drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 500;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s ease, visibility 0.15s ease;
            backdrop-filter: blur(3px);
        }

        .pos-drawer-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .pos-drawer {
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            width: 480px;
            max-width: 95vw;
            background: #fff;
            z-index: 501;
            transform: translateX(100%);
            transition: transform 0.18s cubic-bezier(.25, 0, .15, 1);
            display: flex;
            flex-direction: column;
            box-shadow: -8px 0 40px rgba(0, 0, 0, 0.12);
        }

        .pos-drawer.open {
            transform: translateX(0);
        }

        /* -- View Details btn -- */
        .btn-view-details {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1.5px solid #bae6fd;
            background: #f0f9ff;
            color: #0284c7;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s, box-shadow 0.15s, transform 0.12s;
            white-space: nowrap;
        }

        .btn-view-details:hover {
            background: #0ea5e9;
            border-color: #0ea5e9;
            color: #fff;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.28);
            transform: translateY(-1px);
        }

        .btn-view-details:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .pos-drawer-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .pos-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #64748b;
            font-weight: 500;
        }

        .detail-value {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }

        .pos-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
    </style>

    {{-- -- Header -- --}}
    <div
        style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">POS Items</h2>
                <span
                    style="display:inline-flex;align-items:center;gap:5px;background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(139,92,246,0.12));color:#6366f1;border:1px solid rgba(99,102,241,0.25);padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">
                    <i class="bi bi-grid-3x3-gap"></i> POS Terminal Items
                </span>
            </div>
            {{-- <p style="font-size:13.5px;color:#64748b;margin:0;">Products enabled for the POS Terminal � only active &amp;
                POS-enabled items appear here.</p> --}}
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            {{-- <a href="{{ route('dashboard.products') }}" class="btn btn-outline" style="text-decoration:none;">
                <i class="bi bi-box-seam"></i> All Products
            </a> --}}
            <a href="{{ route('dashboard.pos-items.create') }}" class="btn btn-primary" style="text-decoration:none;">
                <i class="bi bi-plus-circle"></i> Add New Item
            </a>
        </div>
    </div>

    {{-- -- Info Banner -- --}}
    <div
        style="background:linear-gradient(135deg,rgba(99,102,241,0.07),rgba(139,92,246,0.07));border:1px solid rgba(99,102,241,0.2);border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:12px;margin-bottom:20px;">
        <i class="bi bi-info-circle-fill" style="color:#6366f1;font-size:18px;flex-shrink:0;"></i>
        <div style="font-size:13px;color:#374151;">
            <strong style="color:#4f46e5;">POS Items</strong> are products that have <em>"Show in POS Terminal"</em>
            enabled and are currently active. To add items, click <strong>Add New Item</strong> and enable the POS
            Terminal toggle on the create form.
        </div>
    </div>

    {{-- -- Filters -- --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.pos-items') }}"
                style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:0;">
                <div style="flex:1;min-width:240px;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search POS items by name, SKU or barcode..." style="padding-left:36px;">
                    <i class="bi bi-search"
                        style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <select name="category_id" class="form-control" style="width:180px;cursor:pointer;"
                    onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <select name="product_type" class="form-control" style="width:170px;cursor:pointer;"
                    onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="ready_made" {{ request('product_type') === 'ready_made' ? 'selected' : '' }}>Ready
                        Made</option>
                    <option value="finished_product"
                        {{ request('product_type') === 'finished_product' ? 'selected' : '' }}>Finished Product</option>
                </select>
                <select name="status" class="form-control" style="width:160px;cursor:pointer;"
                    onchange="this.form.submit()">
                    <option value="">Stock Status</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock
                    </option>
                    <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of
                        Stock</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                @if (request()->anyFilled(['search', 'category_id', 'product_type', 'status']))
                    <a href="{{ route('dashboard.pos-items') }}" class="btn btn-outline btn-sm"
                        style="color:#ef4444;border-color:#fecaca;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- -- Stats Row -- --}}
    <div style="display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap;">
        @php
            $total = $posItems->total();
            $inStock = $posItems->getCollection()->where('stock_qty', '>', 0)->count();
            $lowStock = $posItems
                ->getCollection()
                ->filter(fn($p) => $p->stock_qty > 0 && $p->stock_qty <= $p->alert_qty)
                ->count();
            $outStock = $posItems->getCollection()->where('stock_qty', '<=', 0)->count();
        @endphp
        @foreach ([['icon' => 'bi-grid-3x3-gap', 'bg' => 'rgba(99,102,241,0.1)', 'color' => '#6366f1', 'label' => 'Total POS Items', 'val' => $total], ['icon' => 'bi-check-circle-fill', 'bg' => 'rgba(16,185,129,0.1)', 'color' => '#10b981', 'label' => 'In Stock', 'val' => $inStock], ['icon' => 'bi-exclamation-triangle-fill', 'bg' => 'rgba(245,158,11,0.1)', 'color' => '#f59e0b', 'label' => 'Low Stock', 'val' => $lowStock], ['icon' => 'bi-x-circle-fill', 'bg' => 'rgba(239,68,68,0.1)', 'color' => '#ef4444', 'label' => 'Out of Stock', 'val' => $outStock]] as $stat)
            <div
                style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:12px;min-width:150px;flex:1;">
                <div
                    style="width:38px;height:38px;border-radius:9px;background:{{ $stat['bg'] }};display:flex;align-items:center;justify-content:center;color:{{ $stat['color'] }};font-size:18px;">
                    <i class="bi {{ $stat['icon'] }}"></i>
                </div>
                <div>
                    <div
                        style="font-size:11px;color:#64748b;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">
                        {{ $stat['label'] }}</div>
                    <div style="font-size:22px;font-weight:800;color:#0f172a;">{{ $stat['val'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- -- POS Items Table -- --}}
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr
                        style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">
                        <th style="padding:16px 20px; text-align: left;">Product</th>
                        <th style="padding:16px 20px; text-align: center;">QR Code</th>
                        <th style="padding:16px 20px; text-align: left;">Category & Brand</th>
                        <th style="padding:16px 20px; text-align: center;">Type</th>
                        <th style="padding:16px 20px; text-align: right;">Pricing</th>
                        <th style="padding:16px 20px; text-align: center;">Stock</th>
                        <th style="padding:16px 20px; text-align: center;">Status</th>
                        <th style="padding:16px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @forelse($posItems as $item)
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
                            onmouseover="this.style.background='#f8fafc'"
                            onmouseout="this.style.background='transparent'">

                            <!-- Product (Image, Name, SKU, Barcode) -->
                            <td style="padding:14px 20px; vertical-align: middle;">
                                <div style="display:flex;align-items:center;gap:16px;">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                            style="width:48px;height:48px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    @else
                                        <div
                                            style="width:48px;height:48px;border-radius:8px;background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(139,92,246,0.12));color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:20px;border:1px solid #e2e8f0;">
                                            <i class="bi bi-bag-fill"></i>
                                        </div>
                                    @endif
                                    <div style="display:flex;flex-direction:column;gap:4px;">
                                        <span
                                            style="font-weight:700;color:#0f172a;font-size:14.5px;">{{ $item->name }}</span>
                                        <div
                                            style="display:flex;align-items:center;gap:8px;font-size:12px;color:#64748b;font-family:monospace;">
                                            <span title="SKU"><i class="bi bi-upc"></i> {{ $item->sku }}</span>
                                            @if ($item->barcode)
                                                <span style="color:#cbd5e1;">|</span>
                                                <span title="Barcode"><i class="bi bi-upc-scan"></i>
                                                    {{ $item->barcode }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- QR Code -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                @if ($item->barcode)
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data={{ urlencode($item->barcode) }}"
                                        alt="QR Code"
                                        style="width:36px;height:36px;border:1px solid #e2e8f0;padding:2px;border-radius:4px;background:#fff;"
                                        title="Scan Barcode: {{ $item->barcode }}">
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">-</span>
                                @endif
                            </td>

                            <!-- Category & Brand -->
                            <td style="padding:14px 20px; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;gap:2px;">
                                    <span
                                        style="font-weight:600;color:#334155;font-size:13.5px;">{{ $item->category?->name ?? 'Uncategorized' }}</span>
                                    <span
                                        style="font-size:12px;color:#64748b;">{{ $item->brand?->name ?? 'No Brand' }}</span>
                                </div>
                            </td>

                            <!-- Type -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                @if ($item->product_type === 'raw_material')
                                    <span
                                        style="background:#fef3c7;color:#d97706;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.3px;">Raw
                                        Material</span>
                                @elseif($item->product_type === 'ready_made')
                                    <span
                                        style="background:#e0e7ff;color:#4f46e5;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.3px;">Ready
                                        Made</span>
                                @elseif($item->product_type === 'finished_product')
                                    <span
                                        style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.3px;">Finished
                                        Product</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">-</span>
                                @endif
                            </td>

                            <!-- Pricing -->
                            <td style="padding:14px 20px; text-align: right; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;gap:2px;">
                                    <span style="font-weight:700;color:#0f172a;font-size:14.5px;">&#2547;
                                        {{ number_format($item->sale_price, 2) }}</span>
                                    <span style="font-size:12px;color:#64748b;" title="Cost Price">Cost: &#2547;
                                        {{ number_format($item->cost_price, 2) }}</span>
                                </div>
                            </td>

                            <!-- Stock -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:2px;">
                                    <span
                                        style="font-weight:700;color:#0f172a;font-size:14.5px;">{{ floatval($item->stock_qty) }}</span>
                                    <span
                                        style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600;">{{ $item->unit?->short_name ?? 'pcs' }}</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                    <form action="{{ route('dashboard.products.toggle-stock', $item) }}"
                                        method="POST" style="margin:0;">
                                        @csrf
                                        @method('PATCH')
                                        <label class="status-toggle-switch">
                                            <input type="checkbox" onchange="this.form.submit()"
                                                {{ $item->stock_qty > 0 ? 'checked' : '' }}>
                                            <span class="status-toggle-slider"></span>
                                        </label>
                                    </form>
                                    @if ($item->stock_qty <= 0)
                                        <span
                                            style="color:#ef4444;font-size:10px;font-weight:700;background:#fee2e2;padding:2px 6px;border-radius:10px;text-transform:uppercase;">Out
                                            of Stock</span>
                                    @elseif($item->stock_qty <= $item->alert_qty)
                                        <span
                                            style="color:#d97706;font-size:10px;font-weight:700;background:#fef3c7;padding:2px 6px;border-radius:10px;text-transform:uppercase;">Low
                                            Stock</span>
                                    @else
                                        <span
                                            style="color:#10b981;font-size:10px;font-weight:700;background:#d1fae5;padding:2px 6px;border-radius:10px;text-transform:uppercase;">In
                                            Stock</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                    <!-- View Details btn -->
                                    <button type="button" class="btn-view-details"
                                        onclick="openDrawer({{ json_encode([
                                            'id' => $item->id,
                                            'name' => $item->name,
                                            'sku' => $item->sku,
                                            'barcode' => $item->barcode ?? 'N/A',
                                            'category' => $item->category?->name ?? 'N/A',
                                            'brand' => $item->brand?->name ?? 'N/A',
                                            'type' => ucwords(str_replace('_', ' ', $item->product_type)),
                                            'unit' => $item->unit?->short_name ?? 'pcs',
                                            'cost' => number_format($item->cost_price, 2),
                                            'sale' => number_format($item->sale_price, 2),
                                            'mrp' => number_format($item->mrp_price, 2),
                                            'stock' => floatval($item->stock_qty),
                                            'alert_qty' => floatval($item->alert_qty),
                                            'image' => $item->image ? asset('storage/' . $item->image) : null,
                                            'is_active' => $item->is_active,
                                            'edit_url' => route('dashboard.pos-items.edit', $item),
                                            'show_url' => route('dashboard.products.show', $item),
                                        ]) }})">
                                        <i class="bi bi-eye"></i> View
                                    </button>

                                    <!-- Edit -->
                                    <a href="{{ route('dashboard.pos-items.edit', $item) }}" title="Edit Item"
                                        style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;border:1.5px solid #e0e7ff;background:#f5f3ff;color:#6366f1;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.15s;"
                                        onmouseover="this.style.background='#6366f1';this.style.color='#fff';this.style.borderColor='#6366f1';this.style.boxShadow='0 4px 12px rgba(99,102,241,0.28)';"
                                        onmouseout="this.style.background='#f5f3ff';this.style.color='#6366f1';this.style.borderColor='#e0e7ff';this.style.boxShadow='none';">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <!-- Delete -->
                                    <form id="delete-positem-{{ $item->id }}" method="POST"
                                        action="{{ route('dashboard.products.destroy', $item) }}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" title="Delete Item"
                                        onclick="confirmDeletePosItem({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                        style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;border:1.5px solid #fee2e2;background:#fef2f2;color:#ef4444;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.15s;"
                                        onmouseover="this.style.background='#ef4444';this.style.color='#fff';this.style.borderColor='#ef4444';this.style.boxShadow='0 4px 12px rgba(239,68,68,0.28)';"
                                        onmouseout="this.style.background='#fef2f2';this.style.color='#ef4444';this.style.borderColor='#fee2e2';this.style.boxShadow='none';">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:60px;text-align:center;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                                    <div
                                        style="width:64px;height:64px;border-radius:16px;background:rgba(99,102,241,0.08);display:flex;align-items:center;justify-content:center;color:#6366f1;font-size:30px;">
                                        <i class="bi bi-grid-3x3-gap"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:15px;font-weight:700;color:#0f172a;margin-bottom:4px;">No
                                            POS Items Found</div>
                                        <div style="font-size:13px;color:#94a3b8;max-width:380px;line-height:1.6;">
                                            @if (request()->anyFilled(['search', 'category_id', 'product_type', 'status']))
                                                No items match your filters. <a
                                                    href="{{ route('dashboard.pos-items') }}"
                                                    style="color:#6366f1;text-decoration:none;font-weight:600;">Clear
                                                    filters</a>
                                            @else
                                                No products have POS Terminal enabled. Create or edit a product and
                                                toggle <strong>Show in POS Terminal</strong>.
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('dashboard.pos-items.create') }}" class="btn btn-primary"
                                        style="margin-top:4px;text-decoration:none;">
                                        <i class="bi bi-plus-circle"></i> Add New Item
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:20px;">{{ $posItems->links() }}</div>


    {{-- --------------------------------------------------
         View Details Slide-over Drawer
         -------------------------------------------------- --}}
    <div class="pos-drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

    <div class="pos-drawer" id="detailDrawer">
        <div class="pos-drawer-header">
            <div id="drawerProductThumb"
                style="width:46px;height:46px;border-radius:10px;background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(139,92,246,0.12));color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:22px;overflow:hidden;flex-shrink:0;">
                <i class="bi bi-bag-fill"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div id="drawerProductName"
                    style="font-size:16px;font-weight:800;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                </div>
                <div id="drawerProductSku" style="font-size:12px;color:#64748b;font-family:monospace;"></div>
            </div>
            <button onclick="closeDrawer()"
                style="background:none;border:none;color:#94a3b8;font-size:20px;cursor:pointer;padding:0;flex-shrink:0;"
                title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="pos-drawer-body">

            {{-- Image (shown when available) --}}
            <div id="drawerImageWrap" style="display:none;margin-bottom:20px;text-align:center;">
                <img id="drawerProductImage" src="" alt=""
                    style="max-height:180px;border-radius:12px;object-fit:cover;border:1px solid #e2e8f0;">
            </div>

            {{-- Status badges --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;" id="drawerBadges"></div>

            {{-- Price cards --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;">
                <div
                    style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;color:#64748b;font-weight:500;margin-bottom:3px;">Cost Price</div>
                    <div id="drawerCost" style="font-size:16px;font-weight:800;color:#0f172a;"></div>
                </div>
                <div
                    style="background:#e0f2fe;border:1px solid #bae6fd;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;color:#0369a1;font-weight:500;margin-bottom:3px;">Sale Price</div>
                    <div id="drawerSale" style="font-size:16px;font-weight:800;color:#0369a1;"></div>
                </div>
                <div
                    style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:11px;color:#64748b;font-weight:500;margin-bottom:3px;">MRP Price</div>
                    <div id="drawerMrp" style="font-size:16px;font-weight:800;color:#0f172a;"></div>
                </div>
            </div>

            {{-- Detail rows --}}
            <div
                style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:4px 16px;margin-bottom:20px;">
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-upc-scan"
                            style="margin-right:6px;color:#6366f1;"></i>Barcode</span>
                    <span class="detail-value" id="drawerBarcode" style="font-family:monospace;"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-tags"
                            style="margin-right:6px;color:#6366f1;"></i>Category</span>
                    <span class="detail-value" id="drawerCategory"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-award"
                            style="margin-right:6px;color:#6366f1;"></i>Brand</span>
                    <span class="detail-value" id="drawerBrand"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-box-seam"
                            style="margin-right:6px;color:#6366f1;"></i>Product Type</span>
                    <span class="detail-value" id="drawerType"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-stack"
                            style="margin-right:6px;color:#6366f1;"></i>Stock Qty</span>
                    <span class="detail-value" id="drawerStock"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-exclamation-triangle"
                            style="margin-right:6px;color:#f59e0b;"></i>Alert Qty</span>
                    <span class="detail-value" id="drawerAlertQty"></span>
                </div>
            </div>

            {{-- Action buttons --}}
            {{-- <div style="display:flex;gap:10px;">
                <a id="drawerEditBtn" href="#" class="btn btn-primary"
                    style="flex:1;text-decoration:none;justify-content:center;">
                    <i class="bi bi-pencil-square"></i> Edit Items
                </a>
                <a id="drawerShowBtn" href="#" class="btn btn-outline"
                    style="flex:1;text-decoration:none;justify-content:center;">
                    <i class="bi bi-eye"></i> View Details
                </a>
            </div> --}}
        </div>
    </div>

    <script>
        function openDrawer(data) {
            // Name / SKU header
            document.getElementById('drawerProductName').textContent = data.name;
            document.getElementById('drawerProductSku').textContent = 'SKU: ' + data.sku;

            // Thumbnail
            const thumb = document.getElementById('drawerProductThumb');
            const imgWrap = document.getElementById('drawerImageWrap');
            const img = document.getElementById('drawerProductImage');
            if (data.image) {
                thumb.innerHTML = '<img src="' + data.image + '" style="width:100%;height:100%;object-fit:cover;">';
                img.src = data.image;
                imgWrap.style.display = 'block';
            } else {
                thumb.innerHTML = '<i class="bi bi-bag-fill"></i>';
                imgWrap.style.display = 'none';
            }

            // Badges
            let badges = '';
            badges += data.is_active ?
                '<span class="pos-badge-pill" style="background:#dcfce7;color:#15803d;"><i class="bi bi-circle-fill" style="font-size:7px;"></i> Active</span>' :
                '<span class="pos-badge-pill" style="background:#f1f5f9;color:#64748b;"><i class="bi bi-circle-fill" style="font-size:7px;"></i> Inactive</span>';
            badges +=
                '<span class="pos-badge-pill" style="background:#e0f2fe;color:#0369a1;"><i class="bi bi-calculator"></i> POS Enabled</span>';
            if (data.stock <= 0) {
                badges +=
                    '<span class="pos-badge-pill" style="background:#fef2f2;color:#b91c1c;"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>';
            } else if (data.stock <= data.alert_qty) {
                badges +=
                    '<span class="pos-badge-pill" style="background:#fffbeb;color:#d97706;"><i class="bi bi-exclamation-triangle-fill"></i> Low Stock</span>';
            } else {
                badges +=
                    '<span class="pos-badge-pill" style="background:#f0fdf4;color:#15803d;"><i class="bi bi-check-circle-fill"></i> In Stock</span>';
            }
            document.getElementById('drawerBadges').innerHTML = badges;

            // Prices
            document.getElementById('drawerCost').innerHTML = '&#2547; ' + data.cost;
            document.getElementById('drawerSale').innerHTML = '&#2547; ' + data.sale;
            document.getElementById('drawerMrp').innerHTML = '&#2547; ' + data.mrp;

            // Details
            document.getElementById('drawerBarcode').textContent = data.barcode;
            document.getElementById('drawerCategory').textContent = data.category;
            document.getElementById('drawerBrand').textContent = data.brand;
            document.getElementById('drawerType').textContent = data.type;
            document.getElementById('drawerStock').textContent = data.stock + ' ' + data.unit;
            document.getElementById('drawerAlertQty').textContent = data.alert_qty + ' ' + data.unit;


            // Open
            document.getElementById('drawerOverlay').classList.add('open');
            document.getElementById('detailDrawer').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            document.getElementById('drawerOverlay').classList.remove('open');
            document.getElementById('detailDrawer').classList.remove('open');
            document.body.style.overflow = '';
        }

        // Close on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeDrawer();
        });

        function confirmDeletePosItem(id, name) {
            Swal.fire({
                title: 'Delete POS Item?',
                html: `Are you sure you want to delete <strong>"${name}"</strong>? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: '#ffffff',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-positem-' + id).submit();
                }
            });
        }
    </script>

</x-layouts.admin>
