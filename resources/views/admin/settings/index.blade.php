<x-layouts.admin title="Settings & Profile">

    <style>
        /* Modern Tab Styling */
        .settings-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }
        .tab-btn {
            background: transparent;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
        }
        .tab-btn:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        .tab-btn.active {
            background: var(--sidebar-active-bg);
            color: var(--primary);
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.25s ease-in-out;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .text-danger { color: var(--danger); }
        .alert-error {
            padding: 12px 16px;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13.5px;
            list-style-type: none;
        }
    </style>

    <div style="max-width: 860px;">
        
        <!-- Validation Error List -->
        @if ($errors->any())
            <div class="alert-error">
                <div style="font-weight: 700; margin-bottom: 6px;">Please fix the following issues:</div>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tab Buttons Navigation -->
        <div class="settings-tabs">
            @can('settings.manage')
            <button class="tab-btn active" onclick="switchTab(event, 'tab-business')">
                <i class="bi bi-building"></i> Business Profile
            </button>
            @endcan
            <button class="tab-btn @cannot('settings.manage') active @endcannot" onclick="switchTab(event, 'tab-profile')">
                <i class="bi bi-person-gear"></i> Personal Info
            </button>
            <button class="tab-btn" onclick="switchTab(event, 'tab-security')">
                <i class="bi bi-shield-lock"></i> Security
            </button>
            <!-- <button class="tab-btn" onclick="switchTab(event, 'tab-danger')">
                <i class="bi bi-exclamation-triangle"></i> Danger Zone
            </button> -->
        </div>

        <!-- TAB 1: Business Profile (Admin only) -->
        @can('settings.manage')
        <div id="tab-business" class="tab-content active">
            <form method="POST" action="{{ route('dashboard.settings.update') }}">
                @csrf
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <i class="bi bi-building" style="color: var(--primary); font-size: 18px;"></i>
                        <span class="card-title">Business Information</span>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label" for="business_name">Business Name</label>
                                <input type="text" id="business_name" name="business_name" class="form-control"
                                       value="{{ $settings['business_name']->value ?? '' }}"
                                       placeholder="e.g. Sweet Crumbs Bakery">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="business_phone">Phone</label>
                                <input type="text" id="business_phone" name="business_phone" class="form-control"
                                       value="{{ $settings['business_phone']->value ?? '' }}"
                                       placeholder="+880 1xxx-xxxxxx">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="business_email">Email</label>
                                <input type="email" id="business_email" name="business_email" class="form-control"
                                       value="{{ $settings['business_email']->value ?? '' }}"
                                       placeholder="admin@yourbusiness.com">
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label" for="business_address">Address</label>
                                <input type="text" id="business_address" name="business_address" class="form-control"
                                       value="{{ $settings['business_address']->value ?? '' }}"
                                       placeholder="Full business address">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <i class="bi bi-currency-dollar" style="color: var(--success); font-size: 18px;"></i>
                        <span class="card-title">Currency Configuration</span>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label" for="currency_code">Currency Code</label>
                                <input type="text" id="currency_code" name="currency_code" class="form-control"
                                       value="{{ $settings['currency_code']->value ?? 'BDT' }}"
                                       placeholder="BDT">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="currency_symbol">Currency Symbol</label>
                                <input type="text" id="currency_symbol" name="currency_symbol" class="form-control"
                                       value="{{ $settings['currency_symbol']->value ?? '৳' }}"
                                       placeholder="৳">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Settings
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
        @endcan

        <!-- TAB 2: Personal Information (All Users) -->
        <div id="tab-profile" class="tab-content @cannot('settings.manage') active @endcannot">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <i class="bi bi-person-circle" style="color: var(--primary); font-size: 18px;"></i>
                        <span class="card-title">Profile Information</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="profile_name">Name</label>
                            <input type="text" id="profile_name" name="name" class="form-control"
                                   value="{{ old('name', $user->name) }}" required autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="profile_email">Email Address</label>
                            <input type="email" id="profile_email" name="email" class="form-control"
                                   value="{{ old('email', $user->email) }}" required autocomplete="email">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 3: Change Password (All Users) -->
        <div id="tab-security" class="tab-content">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <i class="bi bi-key" style="color: var(--warning); font-size: 18px;"></i>
                        <span class="card-title">Change Password</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-lock-fill"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 4: Danger Zone / Delete Account (All Users) -->
        <!-- <div id="tab-danger" class="tab-content">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="card" style="border-color: var(--danger); margin-bottom: 20px;">
                    <div class="card-header" style="background: rgba(239, 68, 68, 0.05); border-bottom-color: rgba(239, 68, 68, 0.1);">
                        <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger); font-size: 18px;"></i>
                        <span class="card-title text-danger">Delete Account</span>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 13.5px; color: #64748b; line-height: 1.5; margin-bottom: 20px;">
                            Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
                        </p>
                        <div class="form-group">
                            <label class="form-label" for="delete_password">Account Password</label>
                            <input type="password" id="delete_password" name="password" class="form-control" placeholder="Enter your password to delete account">
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <button type="submit" class="btn" style="background: var(--danger); color: #fff;" onclick="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')">
                        <i class="bi bi-trash"></i> Delete Account
                    </button>
                </div>
            </form>
        </div> -->

    </div>

    <!-- Switch Tab Script -->
    <script>
        function switchTab(evt, tabId) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab content and add active class to button
            document.getElementById(tabId).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>

</x-layouts.admin>
