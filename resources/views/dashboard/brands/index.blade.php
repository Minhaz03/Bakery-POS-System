<x-layouts.admin title="Manage Brands">

    {{-- FilePond CSS --}}
    <link href="https://unpkg.com/filepond@^4/dist/filepond.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
        rel="stylesheet">
    <style>
        .filepond--root {
            font-family: 'Inter', sans-serif;
            margin-bottom: 0;
        }

        .filepond--panel-root {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
        }

        .filepond--panel-root:hover {
            border-color: var(--primary);
        }

        .filepond--drop-label {
            color: #94a3b8;
            font-size: 13px;
            height: 100% !important;
        }

        .filepond--drop-label label {
            font-weight: 600;
            cursor: pointer;
            color: #64748b;
        }

        .filepond--label-action {
            color: var(--primary);
            text-decoration: underline;
            text-decoration-color: var(--primary);
        }

        .filepond--item-panel {
            background: var(--primary);
        }

        .brand-upload-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .brand-upload-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            display: block;
            margin-bottom: 12px;
        }
    </style>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.products') }}" class="btn-topbar" style="padding:8px 12px;"><i
                class="bi bi-arrow-left"></i> Products</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Product Brands</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage item brand labels and vendors.</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;">

        {{-- ===== LEFT: Brands Table ===== --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">All Brands</span>
            </div>
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                            <th style="padding:12px 20px;width:56px;">Logo</th>
                            <th style="padding:12px 20px;">Brand Name</th>
                            <th style="padding:12px 20px;">Description</th>
                            <th style="padding:12px 20px;text-align:center;width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color:#334155;">
                        @forelse($brands as $brand)
                            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.1s;"
                                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <td style="padding:12px 20px;">
                                    @if ($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
                                            style="width:40px;height:40px;border-radius:8px;object-fit:cover;border:1px solid #e2e8f0;">
                                    @else
                                        <div
                                            style="width:40px;height:40px;border-radius:8px;background:#f1f5f9;color:#94a3b8;display:flex;align-items:center;justify-content:center;font-size:18px;">
                                            <i class="bi bi-award"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:12px 20px;font-weight:700;color:#0f172a;">{{ $brand->name }}</td>
                                <td
                                    style="padding:12px 20px;color:#64748b;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $brand->description ?? '—' }}
                                </td>
                                <td
                                    style="padding:12px 20px;text-align:center;display:flex;justify-content:center;align-items:center;gap:4px;">
                                    <button type="button"
                                        onclick="openEditModal({{ $brand->id }}, '{{ addslashes($brand->name) }}', '{{ addslashes($brand->description) }}')"
                                        style="background:none;border:none;color:#3b82f6;font-size:16px;cursor:pointer;padding:4px 6px;border-radius:6px;transition:background 0.1s;margin-right:4px;"
                                        title="Edit" onmouseover="this.style.background='#eff6ff'"
                                        onmouseout="this.style.background=''">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('dashboard.brands.destroy', $brand) }}"
                                        class="delete-brand-form" style="margin:0;display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="delete-btn"
                                            style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:4px 6px;border-radius:6px;transition:background 0.1s;"
                                            title="Delete" onmouseover="this.style.background='#fef2f2'"
                                            onmouseout="this.style.background=''">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:36px;text-align:center;color:#94a3b8;">
                                    <i class="bi bi-award"
                                        style="font-size:28px;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    No brands registered yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($brands->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                    {{ $brands->links() }}
                </div>
            @endif
        </div>

        {{-- ===== RIGHT: Add Brand Form (Sticky) ===== --}}
        <div style="position:sticky;top:80px;">
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="bi bi-plus-circle"
                            style="color:var(--primary);margin-right:6px;"></i>Add New Brand</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.brands.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label" for="brand_name">Brand Name <span
                                    style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" id="brand_name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required placeholder="e.g. Puratos">
                            @error('name')
                                <span
                                    style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="brand_desc">Description</label>
                            <textarea name="description" id="brand_desc" rows="3" class="form-control"
                                placeholder="Brief info about the brand...">{{ old('description') }}</textarea>
                        </div>

                        {{-- FilePond Logo Upload --}}
                        {{-- <div class="brand-upload-section">
                            <span class="brand-upload-label"><i class="bi bi-image" style="margin-right:4px;"></i> Brand Logo</span>
                            <input type="file" name="logo" id="brand_logo" class="filepond-image" accept="image/*">
                            @error('logo')<span style="color:#ef4444;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>@enderror
                        </div> --}}

                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            <i class="bi bi-plus-circle"></i> Save Brand
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- FilePond JS --}}
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js">
    </script>
    <script
        src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js">
    </script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateType,
            FilePondPluginImageExifOrientation
        );
        const filePondElem = document.querySelector('.filepond-image');
        if (filePondElem) {
            FilePond.create(filePondElem, {
                labelIdle: `<span style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:8px 0;text-align:center;width:100%;">
                                <i class="bi bi-cloud-arrow-up" style="font-size:28px;color:#94a3b8;display:block;"></i>
                                <span style="font-weight:700;color:#475569;font-size:13.5px;display:block;">Drop Logo Here</span>
                                <span style="font-size:12px;color:#94a3b8;display:block;">Or <span class="filepond--label-action" style="font-weight:600;">Browse Files</span></span>
                            </span>`,
                imagePreviewHeight: 160,
                stylePanelAspectRatio: '1:1',
                acceptedFileTypes: ['image/*'],
                allowMultiple: false,
                name: 'logo',
                storeAsFile: true,
            });
        }
    </script>

    <!-- Edit Modal -->
    <div id="editBrandModal"
        style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15, 23, 42, 0.6);z-index:9999;align-items:center;justify-content:center;">
        <div
            style="background:#fff;border-radius:12px;width:100%;max-width:500px;box-shadow:0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);">
            <div
                style="padding:16px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:18px;font-weight:700;color:#0f172a;">Edit Brand</h3>
                <button type="button" onclick="closeEditModal()"
                    style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
            </div>
            <div style="padding:24px;">
                <form id="editBrandForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label" for="edit_brand_name">Brand Name <span
                                style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="edit_brand_name" class="form-control" required>
                    </div>

                    <div class="form-group" style="margin-bottom:24px;">
                        <label class="form-label" for="edit_brand_desc">Description</label>
                        <textarea name="description" id="edit_brand_desc" rows="3" class="form-control"></textarea>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:12px;">
                        <button type="button" onclick="closeEditModal()" class="btn btn-light"
                            style="background:#f1f5f9;color:#475569;border:none;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Update
                            Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Edit Modal logic
        function openEditModal(id, name, description) {
            const modal = document.getElementById('editBrandModal');
            const form = document.getElementById('editBrandForm');
            const nameInput = document.getElementById('edit_brand_name');
            const descInput = document.getElementById('edit_brand_desc');

            // Generate Route URL dynamically
            let updateUrl = '{{ route('dashboard.brands.update', ':id') }}';
            updateUrl = updateUrl.replace(':id', id);

            // Set values
            form.action = updateUrl;
            nameInput.value = name;
            descInput.value = description;

            // Show modal
            modal.style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editBrandModal').style.display = 'none';
        }

        // Delete confirmation with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-brand-form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-layouts.admin>
