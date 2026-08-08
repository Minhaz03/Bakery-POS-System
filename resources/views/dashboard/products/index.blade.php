<x-layouts.admin title="Products">
    <style>
        /* iOS-style toggle switch */
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
            background-color: #cbd5e1;
            border: 1px solid #cbd5e1;
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
    </style>

    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Products Directory</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage bakery inventory items, pricing, and stock
                status.</p>
        </div>
        <div style="margin-left:auto;display:flex;gap:10px;">
            <a href="{{ route('dashboard.units') }}" class="btn btn-outline" style="text-decoration:none;"><i
                    class="bi bi-rulers"></i> Units</a>
            <a href="{{ route('dashboard.brands') }}" class="btn btn-outline" style="text-decoration:none;"><i
                    class="bi bi-award"></i> Brands</a>
            <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary" style="text-decoration:none;">
                <i class="bi bi-plus-circle"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Filters and Search Bar -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.products') }}"
                style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:0;">
                <div style="flex:1;min-width:240px;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search products by name, SKU or barcode..." style="padding-left:36px;">
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
                <select name="product_type" class="form-control" style="width:160px;cursor:pointer;"
                    onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="raw_material" {{ request('product_type') === 'raw_material' ? 'selected' : '' }}>Raw
                        Material</option>
                    <option value="ready_made" {{ request('product_type') === 'ready_made' ? 'selected' : '' }}>Ready
                        Made</option>
                    <option value="finished_product"
                        {{ request('product_type') === 'finished_product' ? 'selected' : '' }}>Finished Product
                    </option>
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
                    <a href="{{ route('dashboard.products') }}" class="btn btn-outline btn-sm"
                        style="color:#ef4444;border-color:#fecaca;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    {{-- -- Stats Row -- --}}
    <div style="display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap;">
        @php
            $total = $products->total();
            $inStock = $products->getCollection()->where('stock_qty', '>', 0)->count();
            $lowStock = $products
                ->getCollection()
                ->filter(fn($p) => $p->stock_qty > 0 && $p->stock_qty <= $p->alert_qty)
                ->count();
            $outStock = $products->getCollection()->where('stock_qty', '<=', 0)->count();
        @endphp
        @foreach ([['icon' => 'bi-box-seam', 'bg' => 'rgba(99,102,241,0.1)', 'color' => '#6366f1', 'label' => 'Total Products', 'val' => $total], ['icon' => 'bi-check-circle-fill', 'bg' => 'rgba(16,185,129,0.1)', 'color' => '#10b981', 'label' => 'In Stock', 'val' => $inStock], ['icon' => 'bi-exclamation-triangle-fill', 'bg' => 'rgba(245,158,11,0.1)', 'color' => '#f59e0b', 'label' => 'Low Stock', 'val' => $lowStock], ['icon' => 'bi-x-circle-fill', 'bg' => 'rgba(239,68,68,0.1)', 'color' => '#ef4444', 'label' => 'Out of Stock', 'val' => $outStock]] as $stat)
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

    <!-- Products Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">
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
                    @forelse($products as $product)
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
                            onmouseover="this.style.background='#f8fafc'"
                            onmouseout="this.style.background='transparent'">
                            
                            <!-- Product (Image, Name, SKU, Barcode) -->
                            <td style="padding:14px 20px; vertical-align: middle;">
                                <div style="display:flex;align-items:center;gap:16px;">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            style="width:48px;height:48px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                                    @else
                                        <div style="width:48px;height:48px;border-radius:8px;background:#f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:20px;border:1px solid #e2e8f0;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                    <div style="display:flex;flex-direction:column;gap:4px;">
                                        <span style="font-weight:700;color:#0f172a;font-size:14.5px;">{{ $product->name }}</span>
                                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#64748b;font-family:monospace;">
                                            <span title="SKU"><i class="bi bi-upc"></i> {{ $product->sku }}</span>
                                            @if($product->barcode)
                                                <span style="color:#cbd5e1;">|</span>
                                                <span title="Barcode"><i class="bi bi-upc-scan"></i> {{ $product->barcode }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- QR Code -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                @if ($product->barcode)
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data={{ urlencode($product->barcode) }}"
                                        alt="QR Code"
                                        style="width:36px;height:36px;border:1px solid #e2e8f0;padding:2px;border-radius:4px;background:#fff;"
                                        title="Scan Barcode: {{ $product->barcode }}">
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">-</span>
                                @endif
                            </td>

                            <!-- Category & Brand -->
                            <td style="padding:14px 20px; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;gap:2px;">
                                    <span style="font-weight:600;color:#334155;font-size:13.5px;">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                                    <span style="font-size:12px;color:#64748b;">{{ $product->brand?->name ?? 'No Brand' }}</span>
                                </div>
                            </td>

                            <!-- Type -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                @if ($product->product_type === 'raw_material')
                                    <span style="background:#fef3c7;color:#d97706;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.3px;">Raw Material</span>
                                @elseif($product->product_type === 'ready_made')
                                    <span style="background:#e0e7ff;color:#4f46e5;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.3px;">Ready Made</span>
                                @elseif($product->product_type === 'finished_product')
                                    <span style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:0.3px;">Finished Product</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">-</span>
                                @endif
                            </td>

                            <!-- Pricing -->
                            <td style="padding:14px 20px; text-align: right; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;gap:2px;">
                                    <span style="font-weight:700;color:#0f172a;font-size:14.5px;">৳ {{ number_format($product->sale_price, 2) }}</span>
                                    <span style="font-size:12px;color:#64748b;" title="Cost Price">Cost: ৳ {{ number_format($product->cost_price, 2) }}</span>
                                </div>
                            </td>

                            <!-- Stock -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:2px;">
                                    <span style="font-weight:700;color:#0f172a;font-size:14.5px;">{{ floatval($product->stock_qty) }}</span>
                                    <span style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:600;">{{ $product->unit?->short_name ?? 'pcs' }}</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                                    <form action="{{ route('dashboard.products.toggle-stock', $product) }}"
                                        method="POST" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <label class="status-toggle-switch">
                                            <input type="checkbox" onchange="this.form.submit()"
                                                {{ $product->stock_qty > 0 ? 'checked' : '' }}>
                                            <span class="status-toggle-slider"></span>
                                        </label>
                                    </form>
                                    @if ($product->stock_qty <= 0)
                                        <span style="color:#ef4444;font-size:10px;font-weight:700;background:#fee2e2;padding:2px 6px;border-radius:10px;text-transform:uppercase;">Out of Stock</span>
                                    @elseif($product->stock_qty <= $product->alert_qty)
                                        <span style="color:#d97706;font-size:10px;font-weight:700;background:#fef3c7;padding:2px 6px;border-radius:10px;text-transform:uppercase;">Low Stock</span>
                                    @else
                                        <span style="color:#10b981;font-size:10px;font-weight:700;background:#d1fae5;padding:2px 6px;border-radius:10px;text-transform:uppercase;">In Stock</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:12px;font-size:16px;">
                                    <!-- view btn  -->
                                    <a href="{{ route('dashboard.products.show', $product) }}" style="color:#0ea5e9;transition:color 0.2s;"
                                        onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='#0ea5e9'" title="View Product"><i class="bi bi-eye"></i></a>
                                    <!-- edit btn  -->
                                    <a href="{{ route('dashboard.products.edit', $product) }}" style="color:#6366f1;transition:color 0.2s;"
                                        onmouseover="this.style.color='#4f46e5'" onmouseout="this.style.color='#6366f1'" title="Edit Product"><i class="bi bi-pencil-square"></i></a>
                                    <!-- delete btn  -->
                                    <form id="delete-product-{{ $product->id }}" method="POST"
                                        action="{{ route('dashboard.products.destroy', $product) }}"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                        onclick="confirmDeleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                        style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;transition:color 0.2s;"
                                        onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ef4444'" title="Delete Product">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:48px;text-align:center;color:#64748b;">
                                <i class="bi bi-box-seam" style="font-size:36px;display:block;margin-bottom:8px;"></i>
                                No products found in catalogue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
        {{ $products->links() }}
    </div>

    <script>
        function confirmDeleteProduct(id, name) {
            Swal.fire({
                title: 'Delete Product?',
                html: `Are you sure you want to delete product <strong>"${name}"</strong>? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: '#ffffff',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-product-' + id).submit();
                }
            });
        }
    </script>
</x-layouts.admin>
