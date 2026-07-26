<x-layouts.admin title="Register Customer">

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <a href="{{ route('dashboard.customers') }}" class="btn-topbar" style="padding:8px 12px;"><i
                class="bi bi-arrow-left"></i> Back</a>
        <div>
            <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0;">Register Customer</h2>
            <p style="font-size:13.5px;color:#64748b;margin:4px 0 0 0;">Add a customer to track loyalty points and
                transaction history.</p>
        </div>
    </div>

    <div class="card" style="max-width:700px;">
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.customers.store') }}">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="name">Full Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            required placeholder="e.g. John Doe">
                        @error('name')
                            <span
                                style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone"
                            class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                            placeholder="e.g. 01711111111">
                        @error('phone')
                            <span
                                style="color:#ef4444;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email') }}" placeholder="e.g. john@example.com">
                    </div>

                    {{-- <div class="form-group">
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div> --}}

                    <div class="form-group">
                        <label class="form-label" for="loyalty_points">Initial Loyalty Points</label>
                        <input type="number" name="loyalty_points" id="loyalty_points" class="form-control"
                            value="{{ old('loyalty_points', 0) }}" required min="0">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="total_spent">Initial Total Spent (৳)</label>
                        <input type="number" step="0.01" name="total_spent" id="total_spent" class="form-control"
                            value="{{ old('total_spent', '0.00') }}" required min="0">
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="address">Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control" placeholder="Complete address...">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-top:24px;">
                    <label class="toggle">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span style="font-size:13.5px;font-weight:600;color:#374151;">Active Status</span>
                </div>

                <div style="margin-top:32px;display:flex;gap:12px;border-top:1px solid #f1f5f9;padding-top:20px;">
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                    <a href="{{ route('dashboard.customers') }}" class="btn btn-outline"
                        style="text-decoration:none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>
