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

    <!-- Products Table -->
    <div class="card">
        <div class="card-body" style="padding:0;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600">
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: left; overflow-wrap: break-word;">
                            Product Details</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            SKU</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Barcode Scan QR</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Category</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Type</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Brand</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: right; overflow-wrap: break-word;">
                            Cost Price</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: right; overflow-wrap: break-word;">
                            Sale Price</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Stock Qty</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Status</th>
                        <th
                            style="padding:16px 20px; white-space: normal; vertical-align: middle; text-align: center; overflow-wrap: break-word;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody style="color:#334155;">
                    @forelse($products as $product)
                        <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
                            onmouseover="this.style.background='#f8fafc'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 20px; vertical-align: middle;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            style="width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;">
                                    @else
                                        <div
                                            style="width:40px;height:40px;border-radius:8px;background:#f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:18px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <span
                                            style="font-weight:700;color:#0f172a;display:block;">{{ $product->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <div style="font-family:monospace;color:#475569;font-weight:600;">{{ $product->sku }}
                                </div>
                            </td>
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                @if ($product->barcode)
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($product->barcode) }}"
                                            alt="QR Code"
                                            style="width:40px;height:40px;border:1px solid #e2e8f0;padding:2px;border-radius:4px;background:#fff;"
                                            title="Scan Barcode: {{ $product->barcode }}">
                                        <span style="font-size:11px;color:#64748b;font-family:monospace;"><i
                                                class="bi bi-upc-scan"></i> {{ $product->barcode }}</span>
                                    </div>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">N/A</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                {{ $product->category?->name ?? 'N/A' }}</td>
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                @if ($product->product_type === 'raw_material')
                                    <span
                                        style="background:#fef3c7;color:#d97706;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Raw
                                        Material</span>
                                @elseif($product->product_type === 'ready_made')
                                    <span
                                        style="background:#e0e7ff;color:#4f46e5;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Ready
                                        Made</span>
                                @elseif($product->product_type === 'finished_product')
                                    <span
                                        style="background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Finished
                                        Product</span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;">N/A</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                {{ $product->brand?->name ?? 'N/A' }}</td>
                            <td style="padding:14px 20px; text-align: right; vertical-align: middle;">৳
                                {{ number_format($product->cost_price, 2) }}</td>
                            <td
                                style="padding:14px 20px; text-align: right; vertical-align: middle; font-weight: 700; color: #0f172a;">
                                ৳ {{ number_format($product->sale_price, 2) }}</td>
                            <td
                                style="padding:14px 20px; text-align: center; vertical-align: middle; font-weight: 600;">
                                {{ floatval($product->stock_qty) }} {{ $product->unit?->short_name ?? 'pcs' }}
                            </td>
                            <td style="padding:14px 20px; text-align: center; vertical-align: middle;">
                                <form action="{{ route('dashboard.products.toggle-stock', $product) }}"
                                    method="POST" style="margin: 0; display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <label class="status-toggle-switch">
                                        <input type="checkbox" onchange="this.form.submit()"
                                            {{ $product->stock_qty > 0 ? 'checked' : '' }}>
                                        <span class="status-toggle-slider"></span>
                                    </label>
                                </form>
                                <div style="margin-top:4px;">
                                    @if ($product->stock_qty <= 0)
                                        <span style="color:#b91c1c;font-size:11px;font-weight:600;"><i
                                                class="bi bi-x-circle-fill"></i> Out of Stock</span>
                                    @elseif($product->stock_qty <= $product->alert_qty)
                                        <span style="color:#d97706;font-size:11px;font-weight:600;"><i
                                                class="bi bi-exclamation-triangle-fill"></i> Low Stock</span>
                                    @else
                                        <span style="color:#15803d;font-size:11px;font-weight:600;"><i
                                                class="bi bi-check-circle-fill"></i> In Stock</span>
                                    @endif
                                </div>
                            </td>
                            <td
                                style="padding:14px 20px; text-align: center; vertical-align: middle; font-size: 16px;">
                                <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                    <!-- view btn  -->
                                    <a href="{{ route('dashboard.products.show', $product) }}" style="color:#0ea5e9;"
                                        title="View Product"><i class="bi bi-eye"></i></a>
                                    <!-- edit btn  -->
                                    <a href="{{ route('dashboard.products.edit', $product) }}" style="color:#6366f1;"
                                        title="Edit Product"><i class="bi bi-pencil-square"></i></a>
                                    <!-- delete btn  -->
                                    <form id="delete-product-{{ $product->id }}" method="POST"
                                        action="{{ route('dashboard.products.destroy', $product) }}"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button"
                                        onclick="confirmDeleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                        style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;"
                                        title="Delete Product">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding:48px;text-align:center;color:#64748b;">
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
