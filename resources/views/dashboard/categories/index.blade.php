<x-layouts.admin title="Categories">

    <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Categories Directory</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Organize and group products for inventory and POS
                checkout lists.</p>
        </div>
        <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary"
            style="margin-left:auto;text-decoration:none;">
            <i class="bi bi-plus-circle"></i> Add New Category
        </a>
    </div>

    <!-- Filters and Search Bar -->
    <div class="card" style="margin-bottom:24px;">
        <div class="card-body" style="padding:16px 20px;">
            <form method="GET" action="{{ route('dashboard.categories') }}"
                style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin:0;">
                <div style="flex:1;min-width:240px;position:relative;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Search categories by name..." style="padding-left:36px;">
                    <i class="bi bi-search"
                        style="position:absolute;left:13px;top:10px;color:#94a3b8;font-size:14px;"></i>
                </div>
                <select name="status" class="form-control" style="width:160px;cursor:pointer;"
                    onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm">Filter</button>
                @if (request()->anyFilled(['search', 'status']))
                    <a href="{{ route('dashboard.categories') }}" class="btn btn-outline btn-sm"
                        style="color:#ef4444;border-color:#fecaca;">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Categories Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin-bottom:24px;">
        @forelse($categories as $category)
            <div class="card" style="position:relative;">
                <div class="card-header" style="display:flex;justify-content:between;align-items:center;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                style="width:36px;height:36px;border-radius:8px;object-fit:cover;">
                        @else
                            <div
                                style="width:36px;height:36px;border-radius:8px;background:rgba(99,102,241,0.1);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:18px;">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                        @endif
                        <div>
                            <span class="card-title"
                                style="font-size:15px;display:block;line-height:1.2;">{{ $category->name }}</span>
                            @if ($category->parent)
                                <small style="font-size:11px;color:#64748b;">Sub of
                                    {{ $category->parent->name }}</small>
                            @else
                                <small style="font-size:11px;color:#10b981;">Parent Category</small>
                            @endif
                        </div>
                    </div>
                    <span
                        style="margin-left:auto;background:{{ $category->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $category->is_active ? '#15803d' : '#475569' }};padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <p
                        style="font-size:13px;color:#64748b;line-height:1.5;margin:0 0 16px 0;height:40px;overflow:hidden;text-overflow:ellipsis;">
                        {{ $category->description ?? 'No description provided.' }}
                    </p>
                    <div
                        style="display:flex;justify-content:between;align-items:center;border-top:1px solid #f1f5f9;padding-top:12px;">
                        <span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="bi bi-box-seam"
                                style="color:var(--primary);"></i> {{ $category->products_count }} Products</span>
                        <div style="margin-left:auto;display:flex;align-items:center;gap:12px;">
                            <a href="{{ route('dashboard.categories.edit', $category) }}"
                                style="color:#6366f1;font-size:16px;" title="Edit"><i
                                    class="bi bi-pencil-square"></i></a>
                            <form id="delete-category-{{ $category->id }}" method="POST"
                                action="{{ route('dashboard.categories.destroy', $category) }}" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button"
                                onclick="confirmDeleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;"
                                title="Delete Category">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column:1/-1;padding:48px;text-align:center;">
                <i class="bi bi-tags" style="font-size:48px;color:#94a3b8;display:block;margin-bottom:12px;"></i>
                <span style="font-size:16px;font-weight:600;color:#475569;">No categories found</span>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
        {{ $categories->links() }}
    </div>

    <script>
        function confirmDeleteCategory(id, name) {
            Swal.fire({
                title: 'Delete Category?',
                html: `Are you sure you want to delete category <strong>"${name}"</strong>? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: '#ffffff',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-category-' + id).submit();
                }
            });
        }
    </script>
</x-layouts.admin>
