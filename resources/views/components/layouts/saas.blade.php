<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('POS-System', 'POS System') }}</title>
    <link rel="icon" href="{{ asset('favPOS.png') }}" type="image/png">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">
    <!-- Google Fonts -->
    
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 -->
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-accent: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #6366f1;
            --sidebar-active-bg: rgba(99, 102, 241, 0.15);
            --topbar-bg: #ffffff;
            --body-bg: #f1f5f9;
            --card-bg: #ffffff;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 12px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            margin: 0;
            min-height: 100vh;
            color: #1e293b;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand-icon {
            width: 38px;
            height: 38px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
        }

        .sidebar-brand-name {
            font-size: 16px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .sidebar-brand-sub {
            font-size: 11px;
            color: var(--sidebar-text);
            font-weight: 400;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
            padding: 16px 8px 6px;
        }

        .nav-item {
            display: block;
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            color: var(--sidebar-text);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #e2e8f0;
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: #a5b4fc;
        }

        .nav-link .nav-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 12px 16px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            background: var(--sidebar-accent);
        }

        .sidebar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: var(--sidebar-text);
        }

        .sidebar-footer-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }

        .sidebar-action-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 7px 10px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
        }

        .sidebar-action-btn.settings {
            background: rgba(255, 255, 255, 0.07);
            color: #94a3b8;
        }

        .sidebar-action-btn.settings:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
        }

        .sidebar-action-btn.logout {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
        }

        .sidebar-action-btn.logout:hover {
            background: rgba(239, 68, 68, 0.22);
            color: #fca5a5;
        }

        /* Topbar profile dropdown */
        .profile-dropdown-wrap {
            position: relative;
        }

        .profile-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.12);
            min-width: 200px;
            z-index: 999;
            overflow: hidden;
            animation: dropFadeIn 0.18s ease;
        }

        .profile-dropdown-menu.open {
            display: block;
        }

        @keyframes dropFadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-dropdown-header {
            padding: 14px 16px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .profile-dropdown-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .profile-dropdown-email {
            font-size: 11.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            transition: background 0.12s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .profile-dropdown-item:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .profile-dropdown-item.danger {
            color: #ef4444;
        }

        .profile-dropdown-item.danger:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        .profile-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 4px 0;
        }

        /* ── Main Content ── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--topbar-bg);
            height: 64px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 16px;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            flex: 1;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-topbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-topbar:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ── Cards ── */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .card-body {
            padding: 22px;
        }

        /* ── Alert Toasts ── */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast-msg {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            min-width: 280px;
            max-width: 380px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease;
        }

        .toast-msg.success {
            background: #f0fdf4;
            color: #166534;
            border-left: 4px solid var(--success);
        }

        .toast-msg.error {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .toast-msg.info {
            background: #eff6ff;
            color: #1d4ed8;
            border-left: 4px solid #3b82f6;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* ── Module Toggle Card ── */
        .module-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: box-shadow 0.15s, border-color 0.15s;
        }

        .module-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
            border-color: #cbd5e1;
        }

        .module-card.active {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.03);
        }

        .module-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #64748b;
            flex-shrink: 0;
        }

        .module-card.active .module-card-icon {
            background: rgba(99, 102, 241, 0.12);
            color: var(--primary);
        }

        .module-card-info {
            flex: 1;
        }

        .module-card-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .module-card-desc {
            font-size: 12.5px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.5;
        }

        .module-badge {
            font-size: 10.5px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 999px;
            margin-top: 6px;
            display: inline-block;
        }

        .badge-core {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .badge-active {
            background: #f0fdf4;
            color: #15803d;
        }

        .badge-inactive {
            background: #f8fafc;
            color: #64748b;
        }

        /* Toggle Switch */
        .toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #cbd5e1;
            border-radius: 24px;
            transition: 0.2s;
        }

        .toggle-slider:before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            left: 3px;
            top: 3px;
            transition: 0.2s;
        }

        .toggle input:checked+.toggle-slider {
            background: var(--primary);
        }

        .toggle input:checked+.toggle-slider:before {
            transform: translateX(20px);
        }

        /* Form Controls */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 9px 13px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            color: #1e293b;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-group {
            margin-bottom: 18px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            border: 1.5px solid #d1d5db;
            color: #374151;
        }

        .btn-outline:hover {
            background: #f9fafb;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12.5px;
        }

        /* ── Responsive ── */
        .btn-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #0f172a;
            padding: 0;
            margin-right: 12px;
            line-height: 1;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(2px);
        }

        .sidebar {
            transition: transform 0.3s ease;
        }

        .main-wrapper {
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 991.98px) {
            .btn-sidebar-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon"><i class="bi bi-shop"></i></div>
            <div>
                <div class="sidebar-brand-name">POS System</div>
                <div class="sidebar-brand-sub">Bakery Edition</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">SaaS Admin</div>
            <div class="nav-item">
                <a href="{{ route('saas.dashboard') }}" class="nav-link {{ request()->routeIs('saas.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('saas.tenants.index') }}" class="nav-link {{ request()->routeIs('saas.tenants.*') ? 'active' : '' }}">
                    <i class="bi bi-buildings nav-icon"></i> Tenants
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('saas.subscriptions.index') }}" class="nav-link {{ request()->routeIs('saas.subscriptions.*') ? 'active' : '' }}">
                    <i class="bi bi-card-list nav-icon"></i> Subscriptions
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('saas.users.index') }}" class="nav-link {{ request()->routeIs('saas.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people nav-icon"></i> Users
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('saas.tickets.index') }}" class="nav-link {{ request()->routeIs('saas.tickets.*') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <i class="bi bi-headset nav-icon"></i> Support Tickets
                    </span>
                    @if(isset($pendingSaasTicketsCount) && $pendingSaasTicketsCount > 0)
                        <span style="background:rgba(239,68,68,0.25);color:#fca5a5;font-size:11px;font-weight:700;padding:1px 6px;border-radius:10px;">
                            {{ $pendingSaasTicketsCount }}
                        </span>
                    @endif
                </a>
            </div>
            <div class="nav-item" style="margin-top:20px;">
                <div style="padding: 0 16px 8px 16px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Platform</div>
            </div>
            <div class="nav-item">
                <a href="{{ route('saas.settings.index') }}" class="nav-link {{ request()->routeIs('saas.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear nav-icon"></i> Settings
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('saas.plans.index') }}" class="nav-link {{ request()->routeIs('saas.plans.*') ? 'active' : '' }}">
                    <i class="bi bi-tags nav-icon"></i> Pricing Plans
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(Auth::guard('admin')->user()?->name ?? 'A', 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::guard('admin')->user()?->name ?? 'Admin' }}</div>
                    <div class="sidebar-user-role">Super Admin</div>
                </div>
            </div>
            <div class="sidebar-footer-actions">
                
                <form method="POST" action="{{ route('saas.logout') }}" style="margin:0;flex:1;display:flex;">
                    @csrf
                    <button type="submit" class="sidebar-action-btn logout" title="Log Out" style="width:100%;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <button class="btn-sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
            <div class="topbar-actions">
                <!-- SaaS Notifications Dropdown -->
                <div x-data="{ open: false }" style="position:relative;" @click.away="open = false">
                    <button type="button" @click="open = !open" class="btn-topbar" style="position:relative;border:none;background:transparent;cursor:pointer;">
                        <i class="bi bi-bell"></i>
                        @if(isset($unreadSaasNotificationsCount) && $unreadSaasNotificationsCount > 0)
                            <span style="position:absolute;top:4px;right:4px;width:8px;height:8px;background:#ef4444;border-radius:50%;"></span>
                        @endif
                    </button>
                    
                    <div x-show="open" style="display:none;position:absolute;right:0;top:calc(100% + 8px);width:320px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:999;">
                        <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-weight:700;color:#0f172a;font-size:13.5px;">Platform Alerts</span>
                            @if(isset($unreadSaasNotificationsCount) && $unreadSaasNotificationsCount > 0)
                                <span style="background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;padding:1px 6px;border-radius:10px;">
                                    {{ $unreadSaasNotificationsCount }} new
                                </span>
                            @endif
                        </div>
                        
                        <div style="max-height:300px;overflow-y:auto;">
                            @if(isset($latestSaasNotifications) && count($latestSaasNotifications) > 0)
                                @foreach($latestSaasNotifications as $notification)
                                    <a href="{{ $notification->action_url ?? route('saas.tickets.index') }}" style="text-decoration:none;display:block;padding:12px 16px;border-bottom:1px solid #f1f5f9;transition:background 0.2s;{{ !$notification->is_read ? 'background:#f8fafc;' : '' }}" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='{{ !$notification->is_read ? '#f8fafc' : 'transparent' }}'">
                                        <div style="font-size:13px;font-weight:700;color:#0f172a;margin-bottom:3px;display:flex;justify-content:space-between;">
                                            <span>{{ $notification->title }}</span>
                                            @if(!$notification->is_read)
                                                <span style="width:6px;height:6px;background:#ef4444;border-radius:50%;display:inline-block;margin-top:5px;"></span>
                                            @endif
                                        </div>
                                        <div style="font-size:12px;color:#64748b;line-height:1.4;margin-bottom:4px;">{{ $notification->message }}</div>
                                        <div style="font-size:11px;color:#94a3b8;"><i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}</div>
                                    </a>
                                @endforeach
                            @else
                                <div style="padding:24px 16px;text-align:center;color:#94a3b8;font-size:13px;">
                                    <i class="bi bi-bell-slash" style="font-size:24px;display:block;margin-bottom:8px;color:#cbd5e1;"></i>
                                    No alerts right now.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <button type="button" class="btn-topbar" id="profileDropdownBtn"
                        onclick="toggleProfileDropdown()" style="gap:8px;">
                        <span
                            style="width:28px;height:28px;border-radius:50%;background:var(--primary);display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr(Auth::guard('admin')->user()?->name ?? 'A', 0, 1)) }}
                        </span>
                        <span
                            style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::guard('admin')->user()?->name ?? 'Admin' }}</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <div class="profile-dropdown-menu" id="profileDropdownMenu">
                        <div class="profile-dropdown-header">
                            <div class="profile-dropdown-name">{{ Auth::guard('admin')->user()?->name ?? 'Admin' }}</div>
                            <div class="profile-dropdown-email">{{ Auth::guard('admin')->user()?->email ?? 'admin@saas.com' }}</div>
                        </div>
                        <div style="padding: 4px 0;">
                            <a href="{{ route('saas.settings.index') }}" class="profile-dropdown-item">
                                <i class="bi bi-gear" style="color:#64748b;font-size:15px;"></i> Settings
                            </a>
                            <div class="profile-dropdown-divider"></div>
                            <form method="POST" action="{{ route('saas.logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="profile-dropdown-item danger">
                                    <i class="bi bi-box-arrow-right" style="font-size:15px;"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">
            {{ $slot }}
        </main>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container" id="toastContainer">
        @if (session('success'))
            <div class="toast-msg success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="toast-msg error"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="toast-msg info"><i class="bi bi-info-circle-fill"></i> {{ session('info') }}</div>
        @endif
    </div>

    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // Profile Dropdown Toggle
        function toggleProfileDropdown() {
            const menu = document.getElementById('profileDropdownMenu');
            menu.classList.toggle('open');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('profileDropdownWrap');
            if (wrap && !wrap.contains(e.target)) {
                const menu = document.getElementById('profileDropdownMenu');
                if (menu) menu.classList.remove('open');
            }
        });

        // Auto-dismiss toasts
        document.querySelectorAll('.toast-msg').forEach(t => {
            setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translateX(20px)';
                t.style.transition = '0.4s';
                setTimeout(() => t.remove(), 400);
            }, 4000);
        });
    </script>
</body>

</html>
