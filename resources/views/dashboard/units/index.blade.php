<x-layouts.admin title="Manage Units">

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.products') }}" class="btn-topbar" style="padding:8px 12px;"><i class="bi bi-arrow-left"></i> Products</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Product Measurement Units</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Manage unit metrics for stocking ingredients and billing finished goods.</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:3fr 2fr;gap:24px;align-items:start;">
        
        <!-- Left: List of Units -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">All Registered Units</span>
            </div>
            <div class="card-body" style="padding:0;overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#475569;font-weight:600;">
                            <th style="padding:12px 20px;">ID</th>
                            <th style="padding:12px 20px;">Unit Name</th>
                            <th style="padding:12px 20px;">Short Symbol</th>
                            <th style="padding:12px 20px;">Base Unit & Conversion</th>
                            <th style="padding:12px 20px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color:#334155;">
                        @forelse($units as $unit)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 20px;font-weight:600;color:#64748b;">#{{ $unit->id }}</td>
                            <td style="padding:12px 20px;font-weight:700;color:#0f172a;">{{ $unit->name }}</td>
                            <td style="padding:12px 20px;font-family:monospace;color:#6366f1;font-weight:600;">{{ $unit->short_name }}</td>
                            <td style="padding:12px 20px;color:#475569;">
                                @if($unit->baseUnit)
                                    <span style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:12px;font-weight:600;display:inline-block;">
                                        = {{ floatval($unit->conversion_rate) }} {{ $unit->baseUnit->short_name }} ({{ $unit->operator }})
                                    </span>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;font-weight:600;">Base Unit</span>
                                @endif
                            </td>
                            <td style="padding:12px 20px;text-align:center;">
                                <form method="POST" action="{{ route('dashboard.units.destroy', $unit) }}" class="delete-unit-form" style="margin:0;display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-btn" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:0;"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding:24px;text-align:center;color:#94a3b8;">No units registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($units->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                    {{ $units->links() }}
                </div>
            @endif
        </div>

        <!-- Right: Add Unit Form -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Add New Unit</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.units.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="name">Unit Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Kilogram">
                        @error('name')
                            <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="short_name">Short Name / Symbol <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="short_name" id="short_name" class="form-control @error('short_name') is-invalid @enderror" value="{{ old('short_name') }}" required placeholder="e.g. kg">
                        @error('short_name')
                            <span style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:16px;margin-bottom:16px;">
                        <div class="form-group">
                            <label class="form-label" for="base_unit_id">Base Unit (Optional)</label>
                            <select name="base_unit_id" id="base_unit_id" class="form-control">
                                <option value="">None (This is a Base Unit)</option>
                                @foreach($allUnits as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <small style="color:#64748b;font-size:12px;">Leave blank if this is a primary unit (e.g., Gram). Select a base if this is a derived unit (e.g., Kilogram derived from Gram).</small>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="operator">Operator</label>
                                <select name="operator" id="operator" class="form-control">
                                    <option value="*">Multiply (*)</option>
                                    <option value="/">Divide (/)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label" for="conversion_rate">Conversion Rate</label>
                                <input type="number" step="0.0001" name="conversion_rate" id="conversion_rate" class="form-control" value="1" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:12px;">
                        <i class="bi bi-plus-circle"></i> Save Unit
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete confirmation with SweetAlert
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-unit-form');
                
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
