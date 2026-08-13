<x-layouts.admin title="Edit Category">

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

        .form-two-panel { display: grid; grid-template-columns: 1fr 260px; gap: 24px; align-items: start; max-width: 860px; }
        .form-panel-right { position: sticky; top: 80px; }
        .panel-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .panel-card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; margin: 0 0 14px 0; }
        .section-label { font-size: 13px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 7px; margin: 0 0 16px 0; }
        .section-label i { color: var(--primary); font-size: 15px; }
        .toggle-row { display: flex; align-items: center; gap: 10px; padding: 10px 0; }
        .toggle-row-label { font-size: 13px; font-weight: 600; color: #374151; flex: 1; }
        .toggle-row-sub { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }
        .current-image-badge {
            display: flex; align-items: center; gap: 10px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 10px; margin-bottom: 12px;
        }
        .current-image-badge img { width: 52px; height: 52px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0; }
        .current-image-badge-label { font-size: 12.5px; font-weight: 700; color: #334155; display: block; margin-bottom: 2px; }
        .current-image-badge-text { font-size: 12px; color: #64748b; line-height: 1.5; }
    </style>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.categories') }}" class="btn-topbar" style="padding:8px 12px;"><i class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Edit Category: {{ $category->name }}</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Update category properties and hierarchy.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-two-panel">

            {{-- ===== LEFT PANEL: FIELDS ===== --}}
            <div class="card">
                <div class="card-body">
                    <p class="section-label"><i class="bi bi-tags"></i> Category Details</p>

                    <div class="form-group">
                        <label class="form-label" for="name">Category Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required placeholder="e.g. Pastries">
                        @error('name')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="parent_id">Parent Category</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">None (Primary Category)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')<span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Brief details about the category...">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===== RIGHT PANEL: IMAGE + TOGGLE + SUBMIT ===== --}}
            <div class="form-panel-right">

                {{-- Image Upload --}}
                <div class="panel-card">
                    <p class="panel-card-title"><i class="bi bi-image" style="margin-right:4px;"></i> Category Image</p>
                    @if($category->image)
                        <div class="current-image-badge">
                            <img src="{{ asset('storage/' . $category->image) }}" alt="Current">
                            <div>
                                <span class="current-image-badge-label">Current Image</span>
                                <span class="current-image-badge-text">Upload below to replace</span>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="image" id="category_image" class="filepond-image" accept="image/*">
                    @error('image')<span style="color:#ef4444;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>@enderror
                </div>

                {{-- Active Toggle --}}
                <div class="panel-card">
                    <p class="panel-card-title"><i class="bi bi-toggle-on" style="margin-right:4px;"></i> Status</p>
                    <div class="toggle-row">
                        <div style="flex:1;">
                            <div class="toggle-row-label">Active Category</div>
                            <div class="toggle-row-sub">Visible on POS & Catalogues</div>
                        </div>
                        <label class="toggle">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="panel-card" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        <i class="bi bi-check2-circle"></i> Save Changes
                    </button>
                    <a href="{{ route('dashboard.categories') }}" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:8px;text-decoration:none;">
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
