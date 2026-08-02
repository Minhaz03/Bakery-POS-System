<x-layouts.saas title="Global Settings">

    <style>
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px; }
        .page-title-wrap { display: flex; align-items: center; gap: 16px; }
        .page-header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; }
        .page-header p { font-size: 13.5px; color: #64748b; margin: 4px 0 0 0; }
        
        .header-icon { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #f1f5f9, #cbd5e1); color: #475569; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.5); }
        
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 24px; overflow: hidden; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; align-items: center; gap: 10px; }
        .card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
        .card-body { padding: 24px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 8px; display: block; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: all 0.2s; color: #0f172a; background: #fff; }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        
        .toggle-wrap { display: flex; align-items: center; gap: 10px; }
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input:checked + .slider { background-color: #6366f1; }
        input:checked + .slider:before { transform: translateX(20px); }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; border: none; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { background: #4f46e5; box-shadow: 0 4px 12px rgba(99,102,241,0.2); }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media(max-width: 768px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>

    <div class="page-header">
        <div class="page-title-wrap">
            <div class="header-icon"><i class="bi bi-gear-fill"></i></div>
            <div>
                <h1>Global Settings</h1>
                <p>Configure system-wide variables, payment gateways, and integrations.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; border-left:4px solid #10b981; font-size:14px; font-weight:600;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#fef2f2; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px; border-left:4px solid #ef4444; font-size:14px; font-weight:600;">
            @foreach($errors->all() as $err)
                <div style="margin-bottom:4px;"><i class="bi bi-exclamation-triangle-fill"></i> {{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('saas.settings.store') }}">
        @csrf

        <div class="grid-2">
            <!-- General Settings -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-sliders" style="color:#6366f1;font-size:18px;"></i>
                    <span class="card-title">General Information</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Application Name</label>
                        <input type="text" name="app_name" class="form-control" placeholder="e.g. POS System" value="{{ old('app_name', $settings['app_name'] ?? '') }}">
                        <small style="color:#64748b; font-size:12px; margin-top:4px; display:block;">This name appears in emails and across the platform.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Support Email Address</label>
                        <input type="email" name="support_email" class="form-control" placeholder="e.g. support@pos.com" value="{{ old('support_email', $settings['support_email'] ?? '') }}">
                    </div>
                </div>
            </div>

            <!-- Payment Gateway Settings -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-credit-card-fill" style="color:#10b981;font-size:18px;"></i>
                    <span class="card-title">SSLCommerz Gateway</span>
                </div>
                <div class="card-body">
                    <div class="form-group toggle-wrap" style="margin-bottom: 24px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <label class="toggle-switch">
                            <input type="checkbox" name="sslcommerz_is_sandbox" value="1" {{ old('sslcommerz_is_sandbox', $settings['sslcommerz_is_sandbox'] ?? true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <div>
                            <div style="font-weight:600; font-size:13.5px; color:#0f172a;">Sandbox Mode</div>
                            <div style="font-size:12px; color:#64748b;">Enable for testing. Disable for live production transactions.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Store ID</label>
                        <input type="text" name="sslcommerz_store_id" class="form-control" placeholder="Your SSLCommerz Store ID" value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id'] ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Store Password</label>
                        <input type="password" name="sslcommerz_store_password" class="form-control" placeholder="Your SSLCommerz Store Password" value="{{ old('sslcommerz_store_password', $settings['sslcommerz_store_password'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: right; margin-top: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save All Settings
            </button>
        </div>
    </form>

</x-layouts.saas>
