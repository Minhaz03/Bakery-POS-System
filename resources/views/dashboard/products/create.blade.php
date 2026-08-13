<x-layouts.admin title="Add New Product">

    {{-- FilePond CSS --}}
    <link href="{{ asset('vendor/filepond/filepond.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}" rel="stylesheet">
    <style>
        .filepond--root { font-family: 'Inter', sans-serif; margin-bottom: 0; }
        .filepond--panel-root { background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; }
        .filepond--panel-root:hover { border-color: var(--primary); }
        .filepond--drop-label { color: #94a3b8; font-size: 13px; height: 100% !important; }
        .filepond--drop-label label { font-weight: 600; cursor: pointer; color: #64748b; }
        .filepond--label-action { color: var(--primary); text-decoration: underline; text-decoration-color: var(--primary); }
        .filepond--item-panel { background: var(--primary); }
        .filepond--image-preview-overlay-idle { color: rgba(0,0,0,0.3); }
        /* Two-panel form layout */
        .form-two-panel {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 24px;
            align-items: start;
        }
        .form-panel-right {
            position: sticky;
            top: 80px;
        }
        .panel-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .panel-card-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            margin: 0 0 14px 0;
        }
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }
        .col-span-2 { grid-column: 1 / -1; }
        .section-divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 20px 0;
        }
        .section-label {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0 0 16px 0;
        }
        .section-label i { color: var(--primary); font-size: 15px; }
        .toggle-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .toggle-row:last-child { border-bottom: none; }
        .toggle-row-label { font-size: 13px; font-weight: 600; color: #374151; flex: 1; }
        .toggle-row-sub { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }
    </style>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.products') }}" class="btn-topbar" style="padding:8px 12px;"><i class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Add New Product</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Add a new raw ingredient or finished baked item to the POS inventory catalogue.</p>
        </div>
    </div>

    @if ($errors->any())
        <div style="background-color: #fef2f2; border: 1px solid #f87171; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <i class="bi bi-exclamation-triangle-fill" style="color: #ef4444; font-size: 18px;"></i>
                <h4 style="margin: 0; color: #991b1b; font-size: 15px; font-weight: 700;">Please fix the following errors:</h4>
            </div>
            <ul style="margin: 0; padding-left: 24px; color: #b91c1c; font-size: 13.5px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-two-panel">

            {{-- ===== LEFT PANEL: FORM FIELDS ===== --}}
            <div>

                {{-- Card: Product Details --}}
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-body">
                        <p class="section-label"><i class="bi bi-file-text"></i> Product Details</p>

                        <div class="form-group">
                            <label class="form-label" for="name">Product Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Sourdough Loaf">
                            @error('name')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="sku">SKU</label>
                                <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku') }}" placeholder="Auto-generate if blank">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="barcode">Barcode</label>
                                <input type="text" name="barcode" id="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="Auto-generate if blank">
                            </div>
                        </div>

                        <hr class="section-divider">

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="product_type">Product Type <span style="color:#ef4444;">*</span></label>
                            <select name="product_type" id="product_type" class="form-control" required>
                                <option value="ready_made" {{ old('product_type') == 'ready_made' ? 'selected' : '' }}>Ready Made</option>
                                <option value="raw_material" {{ old('product_type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                                <option value="finished_product" {{ old('product_type') == 'finished_product' ? 'selected' : '' }}>Finished Product (Bakery)</option>
                            </select>
                        </div>

                        <hr class="section-divider">

                        <div class="form-grid-2">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="category_id">Category</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="brand_id">Brand</label>
                                <select name="brand_id" id="brand_id" class="form-control">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="unit_id">Unit</label>
                                <select name="unit_id" id="unit_id" class="form-control">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->short_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="tax_id">Tax / VAT</label>
                                <select name="tax_id" id="tax_id" class="form-control">
                                    <option value="">Select Tax</option>
                                    @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}" {{ old('tax_id', $taxes->where('is_default', true)->first()?->id) == $tax->id ? 'selected' : '' }}>{{ $tax->name }} ({{ floatval($tax->rate) }}%)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="section-divider">

                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="description">Product Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" placeholder="Describe the product (ingredients, allergens, etc.)...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Card: Pricing --}}
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-body">
                        <p class="section-label"><i class="bi bi-tags"></i> Pricing Settings</p>
                        <div class="form-grid-3">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="cost_price">Cost Price (৳) <span style="color:#ef4444;">*</span></label>
                                <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" value="{{ old('cost_price', '0.00') }}" required min="0">
                                @error('cost_price')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="sale_price">Sale / POS Price (৳) <span style="color:#ef4444;">*</span></label>
                                <input type="number" step="0.01" name="sale_price" id="sale_price" class="form-control" value="{{ old('sale_price', '0.00') }}" required min="0">
                                @error('sale_price')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="mrp_price">MRP Price (৳) <span style="color:#ef4444;">*</span></label>
                                <input type="number" step="0.01" name="mrp_price" id="mrp_price" class="form-control" value="{{ old('mrp_price', '0.00') }}" required min="0">
                                @error('mrp_price')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Stock --}}
                <div class="card">
                    <div class="card-body">
                        <p class="section-label"><i class="bi bi-boxes"></i> Stock Controls</p>
                        <div class="form-grid-3">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="stock_qty">Initial Stock <span style="color:#ef4444;">*</span></label>
                                <input type="number" step="0.001" name="stock_qty" id="stock_qty" class="form-control" value="{{ old('stock_qty', '0.000') }}" required min="0">
                                @error('stock_qty')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="alert_qty">Low Stock Alert <span style="color:#ef4444;">*</span></label>
                                <input type="number" step="0.001" name="alert_qty" id="alert_qty" class="form-control" value="{{ old('alert_qty', '5.000') }}" required min="0">
                                @error('alert_qty')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="reorder_qty">Reorder Level <span style="color:#ef4444;">*</span></label>
                                <input type="number" step="0.001" name="reorder_qty" id="reorder_qty" class="form-control" value="{{ old('reorder_qty', '0.000') }}" required min="0">
                                @error('reorder_qty')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== RIGHT PANEL: IMAGE + TOGGLES + SUBMIT ===== --}}
            <div class="form-panel-right">

                {{-- Image Upload Card --}}
                <div class="panel-card">
                    <p class="panel-card-title"><i class="bi bi-image" style="margin-right:4px;"></i> Product Image</p>
                    <input type="file" name="image" id="product_image" class="filepond-image" accept="image/*">
                    @error('image')<span style="color:#ef4444;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>@enderror
                </div>

                {{-- Toggles Card --}}
                <div class="panel-card">
                    <p class="panel-card-title"><i class="bi bi-toggle-on" style="margin-right:4px;"></i> Options</p>
                    <div class="toggle-row">
                        <div style="flex:1;">
                            <div class="toggle-row-label">Active Product</div>
                            <div class="toggle-row-sub">Visible in inventory</div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-row">
                        <div style="flex:1;">
                            <div class="toggle-row-label">POS Checkout</div>
                            <div class="toggle-row-sub">Available at POS terminal</div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="is_pos_enabled" value="1" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                </div>

                {{-- Submit Card --}}
                <div class="panel-card" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        <i class="bi bi-check2-circle"></i> Save Product
                    </button>
                    <a href="{{ route('dashboard.products') }}" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:8px;text-decoration:none;">
                        Cancel
                    </a>
                </div>

            </div>
        </div>

    </form>

    {{-- FilePond JS --}}
    <script src="{{ asset('vendor/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ asset('vendor/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ asset('vendor/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{ asset('vendor/filepond/filepond.min.js') }}"></script>
    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateType,
            FilePondPluginImageExifOrientation
        );
        FilePond.create(document.querySelector('.filepond-image'), {
            labelIdle: `<span style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:8px 0;text-align:center;width:100%;">
                            <i class="bi bi-cloud-arrow-up" style="font-size:28px;color:#94a3b8;display:block;"></i>
                            <span style="font-weight:700;color:#475569;font-size:13.5px;display:block;">Drop Image Here</span>
                            <span style="font-size:12px;color:#94a3b8;display:block;">Or <span class="filepond--label-action" style="font-weight:600;">Browse Files</span></span>
                        </span>`,
            imagePreviewHeight: 200,
            stylePanelAspectRatio: '1:1',
            acceptedFileTypes: ['image/*'],
            allowMultiple: false,
            name: 'image',
            storeAsFile: true,
        });
    </script>

</x-layouts.admin>
