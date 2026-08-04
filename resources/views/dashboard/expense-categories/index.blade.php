<x-layouts.admin title="Expense Categories">
    <div class="topbar">
        <h2 class="topbar-title">Expense Categories</h2>
    </div>

    <div class="page-content" x-data="{ showEditModal: false, editCategory: { id: '', name: '', description: '', is_active: true } }">
        @if(session('success'))
            <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;">
                <i class="bi bi-check-circle-fill" style="margin-right:8px;"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;color:#b91c1c;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #fecaca;">
                <i class="bi bi-exclamation-circle-fill" style="margin-right:8px;"></i> {{ session('error') }}
            </div>
        @endif

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">
            
            <!-- Left Column: Categories List -->
            <div>
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.expense-categories.index') }}" style="display:flex;gap:16px;align-items:flex-end;">
                            <div style="flex:1;max-width:300px;">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..." class="form-control">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('dashboard.expense-categories.index') }}" class="btn btn-outline" style="padding:10px 16px;">Clear</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body" style="padding:0;">
                        <div style="overflow-x:auto;">
                            <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                                        <th style="padding:16px 20px;">Category Name</th>
                                        <th style="padding:16px 20px;">Description</th>
                                        <th style="padding:16px 20px;text-align:center;">Status</th>
                                        <th style="padding:16px 20px;text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody style="color:#334155;">
                                    @forelse($categories as $category)
                                        <tr style="border-bottom:1px solid #f1f5f9;">
                                            <td style="padding:16px 20px;font-weight:600;color:#0f172a;">{{ $category->name }}</td>
                                            <td style="padding:16px 20px;color:#64748b;">{{ $category->description ?: '-' }}</td>
                                            <td style="padding:16px 20px;text-align:center;">
                                                <span style="background:{{ $category->is_active ? '#dcfce7' : '#f1f5f9' }};color:{{ $category->is_active ? '#15803d' : '#475569' }};padding:4px 10px;border-radius:999px;font-size:11px;font-weight:600;">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td style="padding:16px 20px;text-align:center;">
                                                <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
                                                    <button type="button" @click="editCategory = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description) }}', is_active: {{ $category->is_active ? 'true' : 'false' }} }; showEditModal = true" style="background:none;border:none;color:#6366f1;font-size:14.5px;cursor:pointer;padding:0;" title="Edit Category">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('dashboard.expense-categories.destroy', $category) }}" id="delete-form-{{ $category->id }}" style="margin:0;display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmDelete({{ $category->id }})" style="background:none;border:none;color:#ef4444;font-size:14.5px;cursor:pointer;padding:0;" title="Delete Category">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="padding:48px;text-align:center;color:#64748b;">
                                                <i class="bi bi-tags" style="font-size:32px;display:block;margin-bottom:12px;color:#cbd5e1;"></i>
                                                No expense categories found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($categories->hasPages())
                        <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                            {{ $categories->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Add Category Form -->
            <div class="card">
                <div class="card-body" style="padding:24px;">
                    <h3 style="margin:0 0 16px 0;font-size:18px;font-weight:700;color:#0f172a;border-bottom:1px solid #f1f5f9;padding-bottom:16px;">Add New Category</h3>
                    
                    <form action="{{ route('dashboard.expense-categories.store') }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 1rem;">
                            <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Category Name</label>
                            <input type="text" name="name" class="form-control" style="width: 100%;" required>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Description (Optional)</label>
                            <textarea name="description" class="form-control" style="width: 100%;" rows="4"></textarea>
                        </div>

                        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_active" value="1" checked id="create_is_active">
                            <label for="create_is_active" style="font-size: 13px; font-weight: 600; color: #475569;">Active</label>
                        </div>

                        <div style="text-align:right;">
                            <button type="submit" class="btn btn-primary" style="padding: 10px 24px;width:100%;">
                                <i class="bi bi-save"></i> Save Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Edit Category Modal -->
        <div x-show="showEditModal" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEditModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="`/dashboard/expense-categories/${editCategory.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" style="margin-bottom: 1rem; font-weight: 700;">Edit Expense Category</h3>
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Category Name</label>
                                <input type="text" name="name" x-model="editCategory.name" class="form-control" style="width: 100%;" required>
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label style="display:block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px;">Description (Optional)</label>
                                <textarea name="description" x-model="editCategory.description" class="form-control" style="width: 100%;" rows="3"></textarea>
                            </div>

                            <div style="margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" name="is_active" value="1" x-model="editCategory.is_active" id="edit_is_active">
                                <label for="edit_is_active" style="font-size: 13px; font-weight: 600; color: #475569;">Active</label>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
                            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">
                                Save Changes
                            </button>
                            <button type="button" @click="showEditModal = false" class="btn btn-outline" style="padding: 8px 16px;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the category.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
</x-layouts.admin>
