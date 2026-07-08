<x-layouts.admin title="Module Control Panel">

    <!-- Page Header -->
    <div style="margin-bottom:24px;">
        <p style="color:#64748b;font-size:14px;margin:4px 0 0;">Enable or disable system modules. Core modules are always active.</p>
    </div>

    <!-- Core Modules (always active, info only) -->
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <i class="bi bi-lock-fill" style="color:#6366f1;font-size:18px;"></i>
            <span class="card-title">Core Modules</span>
            <span style="margin-left:auto;font-size:12px;color:#64748b;">Always active — cannot be disabled</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;">
                @foreach($modules['core'] as $key => $module)
                <div class="module-card active">
                    <div class="module-card-icon"><i class="bi {{ $module['icon'] }}"></i></div>
                    <div class="module-card-info">
                        <div class="module-card-name">{{ $module['name'] }}</div>
                        <div class="module-card-desc">{{ $module['description'] }}</div>
                        <span class="module-badge badge-core">Always On</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Infrastructure Modules (toggleable) -->
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <i class="bi bi-building" style="color:#10b981;font-size:18px;"></i>
            <span class="card-title">Infrastructure Modules</span>
            <span style="margin-left:auto;font-size:12px;color:#64748b;">Toggle independently</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;">
                @foreach($modules['infrastructure'] as $key => $module)
                @php $isEnabled = $infrastructureStatus[$key] ?? false; @endphp
                <div class="module-card {{ $isEnabled ? 'active' : '' }}">
                    <div class="module-card-icon"><i class="bi {{ $module['icon'] }}"></i></div>
                    <div class="module-card-info">
                        <div class="module-card-name">{{ $module['name'] }}</div>
                        <div class="module-card-desc">{{ $module['description'] }}</div>
                        <span class="module-badge {{ $isEnabled ? 'badge-active' : 'badge-inactive' }}">
                            {{ $isEnabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('dashboard.modules.toggle-infrastructure', $key) }}" style="flex-shrink:0;">
                        @csrf
                        <input type="hidden" name="enabled" value="{{ $isEnabled ? '0' : '1' }}">
                        <label class="toggle" title="{{ $isEnabled ? 'Disable' : 'Enable' }} {{ $module['name'] }}">
                            <input type="checkbox" onchange="this.closest('form').submit()" {{ $isEnabled ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Business Type Modules (mutually exclusive) -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-briefcase" style="color:#f59e0b;font-size:18px;"></i>
            <span class="card-title">Business Type Module</span>
            <span style="margin-left:auto;font-size:12px;color:#64748b;">Select one — mutually exclusive</span>
        </div>
        <div class="card-body">
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Changing the business type module will activate the new module's features. Existing data is preserved when switching.
            </div>
            <form method="POST" action="{{ route('dashboard.modules.set-business-type') }}" id="businessTypeForm">
                @csrf
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;margin-bottom:20px;">
                    @foreach($modules['business_type'] as $key => $module)
                    @php $isActive = $activeBusinessType === $key; @endphp
                    <label style="cursor:pointer;" for="bt_{{ $key }}">
                        <div class="module-card {{ $isActive ? 'active' : '' }}" style="cursor:pointer;position:relative;">
                            <input type="radio" name="business_type" id="bt_{{ $key }}" value="{{ $key }}"
                                   {{ $isActive ? 'checked' : '' }}
                                   style="position:absolute;top:14px;right:14px;width:16px;height:16px;accent-color:#6366f1;">
                            <div class="module-card-icon"><i class="bi {{ $module['icon'] }}"></i></div>
                            <div class="module-card-info">
                                <div class="module-card-name">{{ $module['name'] }}</div>
                                <div class="module-card-desc">{{ $module['description'] }}</div>
                                @if($isActive)
                                    <span class="module-badge badge-active">Currently Active</span>
                                @else
                                    <span class="module-badge badge-inactive">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Apply Business Type
                </button>
            </form>
        </div>
    </div>

</x-layouts.admin>
