<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('POS-System', 'POS System') }}</title>
    <link rel="icon" href="{{ asset('favPOS.png') }}" type="image/png">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        @media (max-width: 768px) {
            .topbar {
                padding: 0 12px;
                gap: 10px;
                height: 56px;
            }
            .topbar-title {
                font-size: 15px;
            }
            .page-content {
                padding: 16px 12px;
            }
            .btn-topbar {
                padding: 6px 10px;
            }
        }
        @media (max-width: 576px) {
            .hide-on-mobile {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    @if (session()->has('impersonated_by'))
        <div
            style="background: #1e293b; color: #f8fafc; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 9999; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="bi bi-incognito" style="font-size: 20px; color: #818cf8;"></i>
                <span style="font-size: 14px; font-weight: 600;">
                    You are currently impersonating <span style="color: #818cf8;">{{ auth()->user()->name }}</span>.
                </span>
            </div>
            <form method="POST" action="{{ route('impersonation.leave') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn"
                    style="background: #334155; color: #f8fafc; border: 1px solid #475569; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                    onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#334155'">
                    <i class="bi bi-box-arrow-left"></i> Return to Super Admin
                </button>
            </form>
        </div>
    @endif
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
            <div class="nav-section-label">Core</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 nav-icon"></i> Dashboard
                </a>
            </div>

            <div class="nav-section-label">Inventory</div>
            <div class="nav-item">
                {{-- <a href="{{ route('dashboard.products') }}"
                    class="nav-link {{ request()->routeIs('dashboard.products') ? 'active' : '' }}"><i
                        class="bi bi-box-seam nav-icon"></i> Products</a> --}}
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.pos-items') }}"
                    class="nav-link {{ request()->routeIs('dashboard.pos-items') ? 'active' : '' }}"><i
                        class="bi bi-grid-3x3-gap nav-icon"></i> POS Items</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.categories') }}"
                    class="nav-link {{ request()->routeIs('dashboard.categories') ? 'active' : '' }}"><i
                        class="bi bi-tags nav-icon"></i> Categories</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.stock-ledger') }}"
                    class="nav-link {{ request()->routeIs('dashboard.stock-ledger') ? 'active' : '' }}"><i
                        class="bi bi-layers nav-icon"></i> Stock Ledger</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.suppliers') }}"
                    class="nav-link {{ request()->routeIs('dashboard.suppliers') ? 'active' : '' }}"><i
                        class="bi bi-people nav-icon"></i> Suppliers</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.purchases') }}"
                    class="nav-link {{ request()->routeIs('dashboard.purchases*') ? 'active' : '' }}"><i
                        class="bi bi-receipt nav-icon"></i> Purchases</a>
            </div>

            <div class="nav-section-label">POS & Sales</div>
            <div class="nav-item">
                <a href="{{ route('dashboard.pos-terminal') }}"
                    class="nav-link {{ request()->routeIs('dashboard.pos-terminal') ? 'active' : '' }}"><i
                        class="bi bi-calculator nav-icon"></i> POS Terminal</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.sales') }}"
                    class="nav-link {{ request()->routeIs('dashboard.sales*') ? 'active' : '' }}"><i
                        class="bi bi-graph-up nav-icon"></i> Sales</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.customers') }}"
                    class="nav-link {{ request()->routeIs('dashboard.customers') ? 'active' : '' }}"><i
                        class="bi bi-person-badge nav-icon"></i> Customers</a>
            </div>

            <div class="nav-section-label">Bakery</div>
            <div class="nav-item">
                <a href="{{ route('dashboard.recipes') }}"
                    class="nav-link {{ request()->routeIs('dashboard.recipes') ? 'active' : '' }}"><i
                        class="bi bi-egg-fried nav-icon"></i> Recipes</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.production') }}"
                    class="nav-link {{ request()->routeIs('dashboard.production') ? 'active' : '' }}"><i
                        class="bi bi-clipboard-check nav-icon"></i> Production</a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.custom-orders') }}"
                    class="nav-link {{ request()->routeIs('dashboard.custom-orders') ? 'active' : '' }}"><i
                        class="bi bi-calendar-event nav-icon"></i> Custom Orders</a>
            </div>

            <div class="nav-section-label">Reports</div>
            <div class="nav-item" x-data="{ open: {{ request()->routeIs('dashboard.reports.*') ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                    class="nav-link {{ request()->routeIs('dashboard.reports.*') ? 'active' : '' }}"
                    style="width: 100%; border: none; background: none; text-align: left; display: flex; align-items: center; cursor: pointer; padding: 9px 12px; transition: all 0.15s;">
                    <i class="bi bi-file-earmark-text nav-icon"></i>
                    <span>Reports</span>
                    <i class="bi" :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"
                        style="font-size: 10px; margin-left: auto;"></i>
                </button>
                <div x-show="open"
                    style="padding-left: 12px; display: flex; flex-direction: column; gap: 2px; margin-top: 4px;">
                    <a href="{{ route('dashboard.reports.index') }}"
                        class="nav-link {{ request()->routeIs('dashboard.reports.index') ? 'active' : '' }}"
                        style="font-size: 13px; padding: 7px 12px;">
                        <i class="bi bi-grid nav-icon" style="font-size:12px;"></i> All Reports
                    </a>
                    <a href="{{ route('dashboard.reports.sales') }}"
                        class="nav-link {{ request()->routeIs('dashboard.reports.sales') ? 'active' : '' }}"
                        style="font-size: 13px; padding: 7px 12px;">
                        <i class="bi bi-graph-up nav-icon" style="font-size:12px;"></i> Sales Report
                    </a>
                    <a href="{{ route('dashboard.reports.purchases') }}"
                        class="nav-link {{ request()->routeIs('dashboard.reports.purchases') ? 'active' : '' }}"
                        style="font-size: 13px; padding: 7px 12px;">
                        <i class="bi bi-cart nav-icon" style="font-size:12px;"></i> Purchases
                    </a>
                    <a href="{{ route('dashboard.reports.stock') }}"
                        class="nav-link {{ request()->routeIs('dashboard.reports.stock') ? 'active' : '' }}"
                        style="font-size: 13px; padding: 7px 12px;">
                        <i class="bi bi-box nav-icon" style="font-size:12px;"></i> Stock Report
                    </a>
                    <a href="{{ route('dashboard.reports.production') }}"
                        class="nav-link {{ request()->routeIs('dashboard.reports.production') ? 'active' : '' }}"
                        style="font-size: 13px; padding: 7px 12px;">
                        <i class="bi bi-clipboard-check nav-icon" style="font-size:12px;"></i> Production
                    </a>
                    <a href="{{ route('dashboard.reports.profit-loss') }}"
                        class="nav-link {{ request()->routeIs('dashboard.reports.profit-loss') ? 'active' : '' }}"
                        style="font-size: 13px; padding: 7px 12px;">
                        <i class="bi bi-cash-coin nav-icon" style="font-size:12px;"></i> Profit & Loss
                    </a>
                </div>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.analytics') }}"
                    class="nav-link {{ request()->routeIs('dashboard.analytics') ? 'active' : '' }}"><i
                        class="bi bi-bar-chart-line nav-icon"></i> Analytics</a>
            </div>

            <div class="nav-section-label">Administration</div>
            @can('users.view')
                <div class="nav-item">
                    <a href="{{ route('dashboard.users.index') }}"
                        class="nav-link {{ request()->routeIs('dashboard.users*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill nav-icon"></i> Users
                    </a>
                </div>
            @endcan
            @can('roles.view')
                <div class="nav-item">
                    <a href="{{ route('dashboard.roles.index') }}"
                        class="nav-link {{ request()->routeIs('dashboard.roles*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock nav-icon"></i> Roles & Permissions
                    </a>
                </div>
            @endcan
            <div class="nav-item">
                <a href="{{ route('dashboard.settings.index') }}"
                    class="nav-link {{ request()->routeIs('dashboard.settings*') ? 'active' : '' }}">
                    <i class="bi bi-gear nav-icon"></i> Settings
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.billing') }}"
                    class="nav-link {{ request()->routeIs('dashboard.billing') ? 'active' : '' }}">
                    <i class="bi bi-credit-card nav-icon"></i> Billing & Plans
                </a>
            </div>
            @can('modules.manage')
                <div class="nav-item">
                    <a href="{{ route('dashboard.modules.index') }}"
                        class="nav-link {{ request()->routeIs('dashboard.modules*') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2 nav-icon"></i> Modules
                    </a>
                </div>
            @endcan
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                    <div class="sidebar-user-role">{{ Auth::user()->roles->first()?->name ?? 'User' }}</div>
                </div>
            </div>
            <div class="sidebar-footer-actions">

                <form method="POST" action="{{ route('logout') }}" style="margin:0;flex:1;display:flex;">
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
                <a href="#" class="btn-topbar"><i class="bi bi-bell"></i></a>
                <!-- Profile Dropdown -->
                <div class="profile-dropdown-wrap" id="profileDropdownWrap">
                    <button type="button" class="btn-topbar" id="profileDropdownBtn"
                        onclick="toggleProfileDropdown()" style="gap:8px;">
                        <span
                            style="width:28px;height:28px;border-radius:50%;background:var(--primary);display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="hide-on-mobile"
                            style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <div class="profile-dropdown-menu" id="profileDropdownMenu">
                        <div class="profile-dropdown-header">
                            <div class="profile-dropdown-name">{{ Auth::user()->name }}</div>
                            <div class="profile-dropdown-email">{{ Auth::user()->email }}</div>
                        </div>
                        <div style="padding: 4px 0;">
                            {{-- <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                                <i class="bi bi-person-circle" style="color:#6366f1;font-size:15px;"></i> My Profile
                            </a> --}}
                            <a href="{{ route('dashboard.settings.index') }}" class="profile-dropdown-item">
                                <i class="bi bi-gear" style="color:#64748b;font-size:15px;"></i> Settings
                            </a>
                            <div class="profile-dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
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
