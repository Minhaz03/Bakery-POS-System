@php
    $activeTab = request()->query('tab', 'login');
    if (old('_form') === 'register' || $errors->has('name') || $errors->has('password_confirmation')) {
        $activeTab = 'register';
    } elseif (old('_form') === 'forgot') {
        $activeTab = 'forgot';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="scroll-behavior: smooth;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="BakeryPOS — Smart Point of Sale & Inventory Management system built for modern craft bakeries. Manage sales, recipes, production batches, and stock in real-time.">

    <title>BakeryPOS — Smart Bakery Point of Sale System</title>
    <link rel="icon" href="{{ asset('favPOS.png') }}">

    <!-- Google Fonts -->
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">

    <!-- Alpine.js -->
    <script defer src="{{ asset('vendor/alpinejs/cdn.min.js') }}"></script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --amber: #f59e0b;
            --amber2: #fbbf24;
            --rose: #f43f5e;
            --indigo: #6366f1;
            --violet: #8b5cf6;
            --emerald: #10b981;
            --bg: #0c0a09;
            --surface: #1c1917;
            --surface2: #292524;
            --border: rgba(255, 255, 255, 0.07);
            --text: #fafaf9;
            --muted: #a8a29e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── Background ── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 80% 60% at 10% 5%, rgba(245, 158, 11, .14) 0%, transparent 55%),
                radial-gradient(ellipse 60% 50% at 85% 90%, rgba(99, 102, 241, .10) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 55% 50%, rgba(244, 63, 94, .05) 0%, transparent 70%),
                var(--bg);
        }

        .grain {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            opacity: .028;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 200px;
        }

        /* ── Page Wrap ── */
        .page-wrap {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Sticky Header ── */
        .site-header {
            position: sticky;
            top: 0;
            background: rgba(12, 10, 9, 0.85);
            backdrop-filter: blur(16px);
            padding: .85rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, .05);
            z-index: 100;
        }

        .site-header-brand {
            display: flex;
            align-items: center;
            gap: .65rem;
            font-family: 'Outfit', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            color: var(--text);
            text-decoration: none;
        }

        .header-logo {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: 0 4px 14px rgba(245, 158, 11, .25);
        }

        .header-pill {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--amber);
            background: rgba(245, 158, 11, .1);
            border: 1px solid rgba(245, 158, 11, .2);
            border-radius: 100px;
            padding: .2rem .65rem;
        }

        /* Navigation links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
        }

        .nav-link {
            color: var(--muted);
            text-decoration: none;
            font-size: .82rem;
            font-weight: 600;
            transition: color .2s;
            cursor: pointer;
        }

        .nav-link:hover {
            color: var(--amber2);
        }

        .header-status {
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 7px #34d399;
            animation: statusbeat 2s infinite ease-in-out;
        }

        @keyframes statusbeat {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .45;
            }
        }

        /* ── Main Layout / Sections ── */
        .hero-section {
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            padding: 3rem 2.5rem 4rem;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 4.5rem;
            align-items: center;
            width: 100%;
        }

        @media (max-width: 1100px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 3.5rem;
            }

            .left-panel {
                text-align: center;
            }

            .hero-sub {
                margin-left: auto;
                margin-right: auto;
            }

            .stats-row {
                justify-content: center;
            }
        }

        /* ════════ LEFT PANEL (HERO) ════════ */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: rgba(245, 158, 11, .09);
            border: 1px solid rgba(245, 158, 11, .22);
            border-radius: 100px;
            padding: .32rem .85rem;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .07em;
            color: var(--amber2);
            text-transform: uppercase;
            margin-bottom: 1.4rem;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.2rem, 4.5vw, 3.8rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -.03em;
            color: var(--text);
            margin-bottom: 1.2rem;
        }

        .hero-title .accent {
            background: linear-gradient(135deg, var(--amber) 0%, var(--amber2) 40%, #fb923c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.75;
            max-width: 520px;
            margin-bottom: 2.2rem;
        }

        /* CTA buttons */
        .hero-ctas {
            display: flex;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        @media (max-width: 1100px) {
            .hero-ctas {
                justify-content: center;
            }
        }

        .cta-primary {
            padding: .8rem 1.4rem;
            border-radius: 12px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            font-size: .88rem;
            text-decoration: none;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            color: #0c0a09;
            box-shadow: 0 4px 16px rgba(245, 158, 11, .22);
            transition: all .2s;
            cursor: pointer;
            border: none;
        }

        .cta-primary:hover {
            background: linear-gradient(135deg, var(--amber2) 0%, #fb923c 100%);
            box-shadow: 0 6px 20px rgba(245, 158, 11, .32);
            transform: translateY(-1px);
        }

        .cta-secondary {
            padding: .8rem 1.4rem;
            border-radius: 12px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            font-size: .88rem;
            text-decoration: none;
            background: rgba(255, 255, 255, .04);
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, .08);
            transition: all .2s;
        }

        .cta-secondary:hover {
            background: rgba(255, 255, 255, .08);
            border-color: rgba(245, 158, 11, .3);
            color: var(--amber2);
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .75rem;
        }

        .stat-card {
            background: rgba(28, 25, 23, .75);
            border: 1px solid rgba(255, 255, 255, .07);
            border-radius: 16px;
            padding: 1rem 1.1rem;
            position: relative;
            overflow: hidden;
            transition: border-color .25s, transform .25s, box-shadow .25s;
            text-align: left;
        }

        .stat-card:hover {
            border-color: rgba(245, 158, 11, .22);
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, .3);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(245, 158, 11, .055) 0%, transparent 65%);
            opacity: 0;
            transition: opacity .25s;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            margin-bottom: .55rem;
        }

        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }

        .stat-label {
            font-size: .67rem;
            color: var(--muted);
            font-weight: 500;
            margin-top: .22rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .stat-trend {
            font-size: .65rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-top: .28rem;
            color: #34d399;
        }

        /* ════════ FEATURES SECTION ════════ */
        .features-section {
            padding: 5rem 2.5rem;
            background: rgba(20, 16, 14, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .features-container {
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .section-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text);
            margin-top: .5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        @media (max-width: 960px) {
            .features-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        .feat-card {
            background: rgba(28, 25, 23, .6);
            border: 1px solid rgba(255, 255, 255, .06);
            border-radius: 18px;
            padding: 1.4rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: border-color .25s, background .25s, transform .25s;
        }

        .feat-card:hover {
            border-color: rgba(245, 158, 11, .18);
            background: rgba(40, 35, 30, .75);
            transform: translateY(-2px);
        }

        .feat-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .feat-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: .3rem;
            font-family: 'Outfit', sans-serif;
        }

        .feat-desc {
            font-size: .76rem;
            color: var(--muted);
            line-height: 1.55;
        }

        /* Live Ticker */
        .live-ticker {
            display: flex;
            align-items: center;
            gap: .65rem;
            background: rgba(16, 185, 129, .055);
            border: 1px solid rgba(16, 185, 129, .14);
            border-radius: 12px;
            padding: .55rem .9rem;
            margin-top: 1.5rem;
            overflow: hidden;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
            background: #34d399;
            box-shadow: 0 0 6px #34d399;
            animation: statusbeat 2s infinite;
        }

        .ticker-scroll {
            overflow: hidden;
            flex: 1;
        }

        .ticker-text {
            font-size: .72rem;
            color: #6ee7b7;
            white-space: nowrap;
            display: inline-block;
            animation: tickerscroll 20s linear infinite;
        }

        @keyframes tickerscroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* ════════ PRICING SECTION ════════ */
        .pricing-section {
            padding: 5rem 2.5rem;
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 1rem;
        }

        @media (max-width: 900px) {
            .pricing-grid {
                grid-template-columns: 1fr;
                max-width: 440px;
                margin: 1rem auto 0;
            }
        }

        .pricing-card {
            background: rgba(28, 25, 23, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 24px;
            padding: 2.2rem 2rem;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pricing-card.featured {
            border-color: rgba(245, 158, 11, 0.3);
            background: rgba(36, 30, 26, 0.8);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), 0 0 40px -10px rgba(245, 158, 11, 0.15);
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            border-color: rgba(245, 158, 11, 0.25);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .pricing-card.featured:hover {
            border-color: rgba(245, 158, 11, 0.5);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6), 0 0 50px -5px rgba(245, 158, 11, 0.2);
        }

        .featured-badge {
            position: absolute;
            top: 1.1rem;
            right: 1.1rem;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            color: #0c0a09;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.28rem 0.75rem;
            border-radius: 100px;
        }

        .plan-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .plan-price-wrap {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding-bottom: 1.5rem;
        }

        .plan-price {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text);
        }

        .plan-currency {
            font-size: 1.4rem;
            color: var(--text);
            font-weight: 700;
        }

        .plan-cycle {
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 500;
        }

        .plan-features-list {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            flex: 1;
        }

        .plan-feature-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.82rem;
            color: var(--text);
        }

        .plan-feature-item i {
            font-size: 0.95rem;
            color: var(--amber);
        }

        .plan-feature-item.disabled {
            color: #57534e;
        }

        .plan-feature-item.disabled i {
            color: #44403c;
        }

        .plan-btn {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            color: var(--text);
            text-decoration: none;
            display: inline-block;
        }

        .plan-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(245, 158, 11, 0.3);
            color: var(--amber2);
        }

        .pricing-card.featured .plan-btn {
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            color: #0c0a09;
            border: none;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.25);
        }

        .pricing-card.featured .plan-btn:hover {
            background: linear-gradient(135deg, var(--amber2) 0%, #fb923c 100%);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35);
            color: #0c0a09;
        }

        /* ════════ RIGHT PANEL / AUTH CARD ════════ */
        .right-panel {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: rgba(22, 19, 17, .88);
            backdrop-filter: blur(28px) saturate(1.7);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 28px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(245, 158, 11, .055),
                0 40px 90px rgba(0, 0, 0, .65),
                0 0 70px -20px rgba(245, 158, 11, .14);
        }

        /* Auth Card Header */
        .auth-header {
            padding: 1.5rem 1.8rem .2rem;
            display: flex;
            align-items: center;
            gap: .9rem;
        }

        .auth-logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 6px 22px rgba(245, 158, 11, .3);
        }

        .auth-brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .auth-brand-sub {
            font-size: .64rem;
            font-weight: 600;
            color: var(--amber);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        /* Tab Nav */
        .tab-nav {
            display: flex;
            gap: .3rem;
            padding: 1rem 1.3rem .5rem;
        }

        .tab-btn {
            flex: 1;
            padding: .6rem .4rem;
            border-radius: 11px;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: .74rem;
            font-weight: 600;
            letter-spacing: .02em;
            transition: all .22s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            color: var(--muted);
            background: transparent;
        }

        .tab-btn.tab-active {
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            color: #0c0a09;
            border-color: transparent;
            box-shadow: 0 4px 18px rgba(245, 158, 11, .28);
        }

        .tab-btn:not(.tab-active):hover {
            background: rgba(245, 158, 11, .08);
            color: var(--amber2);
            border-color: rgba(245, 158, 11, .12);
        }

        /* Auth Body */
        .auth-body {
            padding: 1.2rem 1.8rem 1.6rem;
        }

        .form-eyebrow {
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--amber);
            margin-bottom: .3rem;
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: .22rem;
        }

        .form-sub {
            font-size: .76rem;
            color: var(--muted);
            margin-bottom: 1.3rem;
            line-height: 1.55;
        }

        /* Fields */
        .field-group {
            margin-bottom: .9rem;
        }

        .field-label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .4rem;
        }

        .field-label-text {
            font-size: .69rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #a8a29e;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #57534e;
            font-size: .88rem;
            pointer-events: none;
            transition: color .2s;
        }

        .form-input {
            width: 100%;
            background: rgba(10, 8, 6, .75);
            border: 1.5px solid rgba(255, 255, 255, .08);
            border-radius: 13px;
            padding: .75rem .9rem .75rem 2.55rem;
            font-size: .86rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            -webkit-appearance: none;
        }

        .form-input::placeholder {
            color: #44403c;
        }

        .form-input:focus {
            border-color: rgba(245, 158, 11, .45);
            background: rgba(18, 14, 10, .9);
            box-shadow: 0 0 0 3.5px rgba(245, 158, 11, .1);
        }

        .form-input:focus+.field-icon,
        .field-wrap:focus-within .field-icon {
            color: var(--amber);
        }

        .has-eye {
            padding-right: 2.8rem;
        }

        .eye-btn {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #57534e;
            cursor: pointer;
            background: transparent;
            border: none;
            transition: color .2s;
            font-size: .88rem;
        }

        .eye-btn:hover {
            color: var(--amber2);
        }

        .field-error {
            font-size: .69rem;
            color: #fb7185;
            font-weight: 500;
            margin-top: .32rem;
            display: flex;
            align-items: center;
            gap: .28rem;
        }

        /* Alerts */
        .alert {
            border-radius: 11px;
            padding: .7rem .95rem;
            font-size: .76rem;
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            margin-bottom: 1rem;
        }

        .alert-error {
            background: rgba(244, 63, 94, .08);
            border: 1px solid rgba(244, 63, 94, .18);
            color: #fda4af;
        }

        .alert-success {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .18);
            color: #6ee7b7;
        }

        .alert i {
            margin-top: .1rem;
            flex-shrink: 0;
        }

        .alert strong {
            display: block;
            margin-bottom: .18rem;
            font-weight: 700;
        }

        /* Checkbox */
        .check-wrap {
            display: flex;
            align-items: center;
            gap: .45rem;
            cursor: pointer;
            font-size: .77rem;
            color: var(--muted);
            user-select: none;
        }

        .check-box {
            width: 15px;
            height: 15px;
            border-radius: 5px;
            border: 1.5px solid rgba(255, 255, 255, .13);
            background: rgba(10, 8, 6, .75);
            flex-shrink: 0;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: all .18s;
        }

        .check-box:checked {
            background: var(--amber);
            border-color: var(--amber);
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 10 10' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.5 5l2.5 2.5L8.5 2' stroke='%230c0a09' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-size: 65%;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: .88rem 1rem;
            border: none;
            cursor: pointer;
            border-radius: 13px;
            font-family: 'Outfit', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .02em;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            color: #0c0a09;
            box-shadow: 0 6px 24px rgba(245, 158, 11, .27);
            transition: all .22s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--amber2) 0%, #fb923c 100%);
            box-shadow: 0 8px 32px rgba(245, 158, 11, .38);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: scale(.985);
        }

        /* Grid 2-col */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .7rem;
        }

        /* Subtle link */
        .link-muted {
            font-size: .72rem;
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
        }

        .link-accent {
            font-size: .72rem;
            color: var(--amber);
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            transition: color .2s;
        }

        .link-accent:hover {
            color: var(--amber2);
        }

        /* Trust Bar */
        .trust-bar {
            padding: .85rem 1.8rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.3rem;
            border-top: 1px solid rgba(255, 255, 255, .05);
        }

        .trust-item {
            font-size: .63rem;
            color: #57534e;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .trust-sep {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #44403c;
        }

        /* Footer */
        .site-footer {
            text-align: center;
            padding: 1.5rem;
            font-size: .75rem;
            color: #57534e;
            border-top: 1px solid rgba(255, 255, 255, .04);
            background: rgba(12, 10, 9, 0.95);
        }

        /* Alpine cloak */
        [x-cloak] {
            display: none !important;
        }

        /* Tab transition */
        .tab-panel {
            animation: tabfadein .28s cubic-bezier(.22, 1, .36, 1);
        }

        @keyframes tabfadein {
            from {
                opacity: 0;
                transform: translateY(7px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
            }

            .stats-row .stat-card:last-child {
                grid-column: span 2;
            }

            .hero-section {
                padding: 1.5rem .75rem;
            }

            .features-section {
                padding: 3rem 1rem;
            }

            .pricing-section {
                padding: 3rem 1rem;
            }

            .site-header {
                padding: .75rem 1rem;
            }

            .auth-body {
                padding: 1rem 1.2rem 1.4rem;
            }

            .auth-header {
                padding: 1.2rem 1.2rem .2rem;
            }

            .tab-nav {
                padding: .85rem 1rem .4rem;
            }

            .trust-bar {
                gap: .8rem;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body x-data="{ tab: '{{ $activeTab }}' }"
    @set-tab.window="tab = $event.detail; document.getElementById('hero-auth-section').scrollIntoView({ behavior: 'smooth' });">

    <div class="bg-canvas"></div>
    <div class="grain"></div>

    <div class="page-wrap">

        <!-- ── Header ── -->
        <header class="site-header">
            <a href="#" class="site-header-brand">
                <div class="header-logo">🧁</div>
                <span>{{ config('', 'BakeryPOS') }}</span>
                <span class="header-pill">Bakery Edition</span>
            </a>

            <nav class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#pricing" class="nav-link">Pricing</a>
                <a href="{{ route('login') }}" @click.prevent="$dispatch('set-tab', 'login')" class="nav-link">Sign In</a>
                <a href="{{ route('register') }}" @click.prevent="$dispatch('set-tab', 'register')" class="cta-primary"
                    style="padding: .5rem 1rem; font-size: .78rem; box-shadow: none;">Get Started</a>
            </nav>

            <div class="header-status">
                <span class="status-dot"></span>
                <span style="font-size:.7rem;color:#6ee7b7;font-weight:600;">System Online</span>
            </div>
        </header>

        <!-- ── Hero & Access Section ── -->
        <section id="hero-auth-section" class="hero-section">
            <main class="main-grid">

                <!-- LEFT: HERO & STATS -->
                <div class="left-panel">

                    <div class="brand-badge">
                        <i class="bi bi-lightning-charge-fill"></i>
                        All-in-One Bakery Management Platform
                    </div>

                    <h1 class="hero-title">
                        Run Your Bakery<br>
                        <span class="accent">Smarter, Faster</span><br>
                        &amp; Profitably
                    </h1>

                    <p class="hero-sub">
                        The complete Point of Sale &amp; Inventory platform built exclusively for modern craft bakeries.
                        Formulate recipes, schedule batches, track customer orders, and manage raw stocks.
                    </p>

                    <div class="hero-ctas">
                        <a href="#features" class="cta-primary">
                            Explore Features
                        </a>
                        <a href="#pricing" class="cta-secondary">
                            See Pricing
                        </a>
                    </div>

                    <!-- Animated Stats -->
                    <div class="stats-row" x-data="statsAnimator()">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:rgba(245,158,11,.13);">
                                <i class="bi bi-graph-up-arrow" style="color:var(--amber);"></i>
                            </div>
                            <div class="stat-value">৳<span x-text="fmt(sales)">0</span></div>
                            <div class="stat-label">Today's Revenue</div>
                            <div class="stat-trend">
                                <i class="bi bi-arrow-up-short"></i> +12.4% vs yesterday
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon" style="background:rgba(99,102,241,.13);">
                                <i class="bi bi-bag-check" style="color:var(--indigo);"></i>
                            </div>
                            <div class="stat-value" x-text="orders">0</div>
                            <div class="stat-label">Orders Today</div>
                            <div class="stat-trend">
                                <i class="bi bi-arrow-up-short"></i> +8 new orders
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon" style="background:rgba(52,211,153,.11);">
                                <i class="bi bi-boxes" style="color:#34d399;"></i>
                            </div>
                            <div class="stat-value" x-text="items">0</div>
                            <div class="stat-label">Active SKUs</div>
                            <div class="stat-trend" style="color:#34d399;">
                                <i class="bi bi-check2-circle"></i> Stock Healthy
                            </div>
                        </div>
                    </div>

                </div><!-- /left-panel -->


                <!-- RIGHT: AUTH CARD -->
                <div class="right-panel">

                    <div class="auth-card">

                        <!-- Card Brand Header -->
                        <div class="auth-header">
                            <div class="auth-logo">🧁</div>
                            <div>
                                <div class="auth-brand-name">{{ config('', 'BakeryPOS') }}</div>
                                <div class="auth-brand-sub">Bakery Management System</div>
                            </div>
                        </div>

                        <!-- Tab Buttons -->
                        <div class="tab-nav">
                            <button type="button" id="tab-btn-login" class="tab-btn"
                                :class="tab === 'login' ? 'tab-active' : ''" @click="tab = 'login'">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                            <button type="button" id="tab-btn-register" class="tab-btn"
                                :class="tab === 'register' ? 'tab-active' : ''" @click="tab = 'register'">
                                <i class="bi bi-person-plus-fill"></i> Register
                            </button>
                            <button type="button" id="tab-btn-forgot" class="tab-btn"
                                :class="tab === 'forgot' ? 'tab-active' : ''" @click="tab = 'forgot'">
                                <i class="bi bi-shield-lock-fill"></i> Reset
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="auth-body">

                            {{-- ─── LOGIN ─── --}}
                            <div x-show="tab === 'login'" x-cloak class="tab-panel">

                                <div class="form-eyebrow">
                                    <i class="bi bi-door-open"></i>&nbsp; Welcome Back
                                </div>
                                <h2 class="form-title">Sign in to your account</h2>
                                <p class="form-sub">Access your bakery dashboard and manage daily operations.</p>

                                @if (old('_form') === 'login' && $errors->any())
                                    <div class="alert alert-error">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        <div>
                                            <strong>Login Failed</strong>
                                            Check your email &amp; password and try again.
                                        </div>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}" id="login-form">
                                    @csrf
                                    <input type="hidden" name="_form" value="login">

                                    <!-- Email -->
                                    <div class="field-group">
                                        <div class="field-label-row">
                                            <span class="field-label-text">Email Address</span>
                                        </div>
                                        <div class="field-wrap">
                                            <span class="field-icon"><i class="bi bi-envelope"></i></span>
                                            <input type="email" id="login_email" name="email"
                                                value="{{ old('_form') === 'login' ? old('email') : '' }}" required
                                                autofocus autocomplete="username" class="form-input"
                                                placeholder="name@bakery.com">
                                        </div>
                                        @if (old('_form') === 'login' && $errors->has('email'))
                                            <div class="field-error">
                                                <i class="bi bi-x-circle-fill"></i>
                                                {{ $errors->first('email') }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Password -->
                                    <div class="field-group" x-data="{ showPass: false }">
                                        <div class="field-label-row">
                                            <span class="field-label-text">Password</span>
                                            <button type="button" @click="tab = 'forgot'" class="link-accent">
                                                Forgot password?
                                            </button>
                                        </div>
                                        <div class="field-wrap">
                                            <span class="field-icon"><i class="bi bi-lock"></i></span>
                                            <input :type="showPass ? 'text' : 'password'" id="login_password"
                                                name="password" required autocomplete="current-password"
                                                class="form-input has-eye" placeholder="••••••••">
                                            <button type="button" @click="showPass = !showPass" class="eye-btn">
                                                <i class="bi" :class="showPass ? 'bi-eye-slash' : 'bi-eye'"></i>
                                            </button>
                                        </div>
                                        @if (old('_form') === 'login' && $errors->has('password'))
                                            <div class="field-error">
                                                <i class="bi bi-x-circle-fill"></i>
                                                {{ $errors->first('password') }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Remember -->
                                    <label class="check-wrap">
                                        <input type="checkbox" name="remember" class="check-box">
                                        Remember me for 30 days
                                    </label>

                                    <button type="submit" class="btn-submit" id="login-submit-btn">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        Sign In to Dashboard
                                    </button>
                                </form>

                                <p style="text-align:center;margin-top:.9rem;">
                                    <span class="link-muted">Don't have an account?</span>
                                    <button type="button" @click="tab = 'register'" class="link-accent"
                                        style="margin-left:.35rem;">
                                        Create one now →
                                    </button>
                                </p>
                            </div>


                            {{-- ─── REGISTER ─── --}}
                            <div x-show="tab === 'register'" x-cloak class="tab-panel">

                                <div class="form-eyebrow">
                                    <i class="bi bi-person-badge-fill"></i>&nbsp; New Operator
                                </div>
                                <h2 class="form-title">Create your account</h2>
                                <p class="form-sub">Set up a supervisor or cashier account for your bakery store.</p>

                                @if (old('_form') === 'register' && $errors->any())
                                    <div class="alert alert-error">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        <div>
                                            <strong>Registration Error</strong>
                                            Please correct the highlighted fields below.
                                        </div>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register') }}" id="register-form">
                                    @csrf
                                    <input type="hidden" name="_form" value="register">

                                    <!-- Full Name -->
                                    <div class="field-group">
                                        <div class="field-label-row">
                                            <span class="field-label-text">Full Name</span>
                                        </div>
                                        <div class="field-wrap">
                                            <span class="field-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" id="reg_name" name="name"
                                                value="{{ old('_form') === 'register' ? old('name') : '' }}" required
                                                autofocus autocomplete="name" class="form-input"
                                                placeholder="e.g. Arif Hossain">
                                        </div>
                                        @if (old('_form') === 'register' && $errors->has('name'))
                                            <div class="field-error">
                                                <i class="bi bi-x-circle-fill"></i>
                                                {{ $errors->first('name') }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Email -->
                                    <div class="field-group">
                                        <div class="field-label-row">
                                            <span class="field-label-text">Email Address</span>
                                        </div>
                                        <div class="field-wrap">
                                            <span class="field-icon"><i class="bi bi-envelope"></i></span>
                                            <input type="email" id="reg_email" name="email"
                                                value="{{ old('_form') === 'register' ? old('email') : '' }}" required
                                                autocomplete="username" class="form-input"
                                                placeholder="operator@bakery.com">
                                        </div>
                                        @if (old('_form') === 'register' && $errors->has('email'))
                                            <div class="field-error">
                                                <i class="bi bi-x-circle-fill"></i>
                                                {{ $errors->first('email') }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Passwords -->
                                    <div class="grid-2">
                                        <div class="field-group" x-data="{ showPass: false }">
                                            <div class="field-label-row">
                                                <span class="field-label-text">Password</span>
                                            </div>
                                            <div class="field-wrap">
                                                <span class="field-icon"><i class="bi bi-lock"></i></span>
                                                <input :type="showPass ? 'text' : 'password'" id="reg_password"
                                                    name="password" required autocomplete="new-password"
                                                    class="form-input has-eye" placeholder="••••••••">
                                                <button type="button" @click="showPass = !showPass" class="eye-btn">
                                                    <i class="bi"
                                                        :class="showPass ? 'bi-eye-slash' : 'bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="field-group" x-data="{ showPass: false }">
                                            <div class="field-label-row">
                                                <span class="field-label-text">Confirm</span>
                                            </div>
                                            <div class="field-wrap">
                                                <span class="field-icon"><i class="bi bi-lock-check"></i></span>
                                                <input :type="showPass ? 'text' : 'password'"
                                                    id="reg_password_confirmation" name="password_confirmation"
                                                    required autocomplete="new-password" class="form-input has-eye"
                                                    placeholder="••••••••">
                                                <button type="button" @click="showPass = !showPass" class="eye-btn">
                                                    <i class="bi"
                                                        :class="showPass ? 'bi-eye-slash' : 'bi-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @if (old('_form') === 'register' && $errors->has('password'))
                                        <div class="field-error" style="margin-top:-.4rem;margin-bottom:.7rem;">
                                            <i class="bi bi-x-circle-fill"></i>
                                            {{ $errors->first('password') }}
                                        </div>
                                    @endif
                                    @if (old('_form') === 'register' && $errors->has('password_confirmation'))
                                        <div class="field-error" style="margin-top:-.4rem;margin-bottom:.7rem;">
                                            <i class="bi bi-x-circle-fill"></i>
                                            {{ $errors->first('password_confirmation') }}
                                        </div>
                                    @endif

                                    <button type="submit" class="btn-submit" id="register-submit-btn">
                                        <i class="bi bi-person-check-fill"></i>
                                        Create Operator Account
                                    </button>
                                </form>

                                <p style="text-align:center;margin-top:.9rem;">
                                    <span class="link-muted">Already have an account?</span>
                                    <button type="button" @click="tab = 'login'" class="link-accent"
                                        style="margin-left:.35rem;">
                                        Sign in instead →
                                    </button>
                                </p>
                            </div>


                            {{-- ─── FORGOT PASSWORD ─── --}}
                            <div x-show="tab === 'forgot'" x-cloak class="tab-panel">

                                <div class="form-eyebrow">
                                    <i class="bi bi-shield-lock-fill"></i>&nbsp; Account Recovery
                                </div>
                                <h2 class="form-title">Reset your password</h2>
                                <p class="form-sub">Enter your registered email and we'll send a secure password reset
                                    link.</p>

                                @if (session('status'))
                                    <div class="alert alert-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <div>
                                            <strong>Reset Link Sent!</strong>
                                            {{ session('status') }}
                                        </div>
                                    </div>
                                @endif

                                @if (old('_form') === 'forgot' && $errors->any())
                                    <div class="alert alert-error">
                                        <i class="bi bi-exclamation-circle-fill"></i>
                                        <div>
                                            <strong>Email Error</strong>
                                            {{ $errors->first('email') }}
                                        </div>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('password.email') }}" id="forgot-form">
                                    @csrf
                                    <input type="hidden" name="_form" value="forgot">

                                    <div class="field-group">
                                        <div class="field-label-row">
                                            <span class="field-label-text">Registered Email</span>
                                        </div>
                                        <div class="field-wrap">
                                            <span class="field-icon"><i class="bi bi-envelope-at"></i></span>
                                            <input type="email" id="forgot_email" name="email"
                                                value="{{ old('_form') === 'forgot' ? old('email') : '' }}" required
                                                autofocus class="form-input" placeholder="operator@bakery.com">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-submit" id="forgot-submit-btn">
                                        <i class="bi bi-send-fill"></i>
                                        Send Recovery Email
                                    </button>
                                </form>

                                <p style="text-align:center;margin-top:1rem;">
                                    <button type="button" @click="tab = 'login'"
                                        style="font-size:.75rem;color:#78716c;background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.3rem;"
                                        onmouseover="this.style.color='#fbbf24'"
                                        onmouseout="this.style.color='#78716c'">
                                        <i class="bi bi-arrow-left"></i> Back to Sign In
                                    </button>
                                </p>
                            </div>

                        </div><!-- /auth-body -->

                        <!-- Trust Bar -->
                        <div class="trust-bar">
                            <span class="trust-item">
                                <i class="bi bi-shield-check" style="color:#34d399;font-size:.8rem;"></i>
                                Encrypted &amp; Secure
                            </span>
                            <span class="trust-sep"></span>
                            <span class="trust-item">
                                <i class="bi bi-lock" style="color:var(--amber);font-size:.8rem;"></i>
                                SSL Protected
                            </span>
                            <span class="trust-sep"></span>
                            <span class="trust-item">
                                <i class="bi bi-hdd-rack" style="color:var(--violet);font-size:.8rem;"></i>
                                99.9% Uptime
                            </span>
                        </div>

                    </div><!-- /auth-card -->

                </div><!-- /right-panel -->

            </main>
        </section>

        <!-- ── Features Section ── -->
        <section id="features" class="features-section">
            <div class="features-container">
                <div class="section-header">
                    <span class="brand-badge">
                        <i class="bi bi-star-fill"></i> System Capabilities
                    </span>
                    <h2>Engineered Specifically for Modern Bakeries</h2>
                    <p style="color:var(--muted);font-size:.9rem;max-width:550px;margin:.5rem auto 0;line-height:1.6;">
                        Unlock complete control over your ingredients, recipes, batches, and sales from a single
                        real-time console.
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feat-card">
                        <div class="feat-icon-wrap" style="background:rgba(245,158,11,.12);">
                            <i class="bi bi-receipt-cutoff" style="color:var(--amber);"></i>
                        </div>
                        <div>
                            <div class="feat-title">POS Terminal</div>
                            <div class="feat-desc">Blazing-fast checkout with barcode scan, split payments, walk-in
                                customer mapping &amp; instant receipts.</div>
                        </div>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon-wrap" style="background:rgba(99,102,241,.12);">
                            <i class="bi bi-journal-bookmark-fill" style="color:var(--indigo);"></i>
                        </div>
                        <div>
                            <div class="feat-title">Recipe Manager</div>
                            <div class="feat-desc">Define precise ingredient formulas, calculate production costing,
                                and perform automatic batch-scaling.</div>
                        </div>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon-wrap" style="background:rgba(244,63,94,.12);">
                            <i class="bi bi-fire" style="color:#fb7185;"></i>
                        </div>
                        <div>
                            <div class="feat-title">Production Batches</div>
                            <div class="feat-desc">Schedule oven runs, track active bakes, record wastage, and
                                auto-deduct raw materials upon batch completion.</div>
                        </div>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon-wrap" style="background:rgba(52,211,153,.11);">
                            <i class="bi bi-boxes" style="color:#34d399;"></i>
                        </div>
                        <div>
                            <div class="feat-title">Live Inventory</div>
                            <div class="feat-desc">Real-time stock ledger, automated low-stock reorder alerts, supplier
                                tracking, and purchase orders.</div>
                        </div>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon-wrap" style="background:rgba(251,146,60,.12);">
                            <i class="bi bi-clipboard-heart-fill" style="color:#fb923c;"></i>
                        </div>
                        <div>
                            <div class="feat-title">Custom Orders</div>
                            <div class="feat-desc">Manage special orders, custom cakes, and catering contracts with
                                secure delivery status and deposit tracking.</div>
                        </div>
                    </div>

                    <div class="feat-card">
                        <div class="feat-icon-wrap" style="background:rgba(139,92,246,.12);">
                            <i class="bi bi-bar-chart-line-fill" style="color:var(--violet);"></i>
                        </div>
                        <div>
                            <div class="feat-title">Analytics &amp; Reports</div>
                            <div class="feat-desc">Visual Profit &amp; Loss statements, top selling items, inventory
                                valuation, and cashier shift reports.</div>
                        </div>
                    </div>
                </div>

                <!-- Live Activity Ticker -->
                <div class="live-ticker">
                    <span class="live-dot"></span>
                    <div class="ticker-scroll">
                        <span class="ticker-text">
                            🧁&nbsp;Croissants batch complete&nbsp;&nbsp;·&nbsp;&nbsp;🧾&nbsp;Order #1047 processed
                            ৳420&nbsp;&nbsp;·&nbsp;&nbsp;📦&nbsp;Butter stock low — reorder
                            recommended&nbsp;&nbsp;·&nbsp;&nbsp;🎂&nbsp;Custom cake order #C-21
                            confirmed&nbsp;&nbsp;·&nbsp;&nbsp;💰&nbsp;Today's revenue
                            ৳18,450&nbsp;&nbsp;·&nbsp;&nbsp;🥐&nbsp;Sourdough batch #14
                            baking&nbsp;&nbsp;·&nbsp;&nbsp;🛒&nbsp;Purchase order PO-88
                            received&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;🧁&nbsp;Croissants batch
                            complete&nbsp;&nbsp;·&nbsp;&nbsp;🧾&nbsp;Order #1047 processed
                            ৳420&nbsp;&nbsp;·&nbsp;&nbsp;📦&nbsp;Butter stock low — reorder
                            recommended&nbsp;&nbsp;·&nbsp;&nbsp;🎂&nbsp;Custom cake order #C-21
                            confirmed&nbsp;&nbsp;·&nbsp;&nbsp;💰&nbsp;Today's revenue
                            ৳18,450&nbsp;&nbsp;·&nbsp;&nbsp;🥐&nbsp;Sourdough batch #14
                            baking&nbsp;&nbsp;·&nbsp;&nbsp;🛒&nbsp;Purchase order PO-88 received
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── Pricing Section ── -->
        <section id="pricing" class="pricing-section">
            <div class="section-header" style="margin-bottom: 3.5rem;">
                <span class="brand-badge">
                    <i class="bi bi-tags-fill"></i> Pricing Plans
                </span>
                <h2>Simple, Transparent Pricing</h2>
                <p style="color:var(--muted);font-size:.9rem;max-width:500px;margin:.5rem auto 0;line-height:1.6;">
                    Select the perfect plan designed to support craft micro-bakeries up to multi-outlet operations.
                </p>
            </div>

            <div class="pricing-grid">
                @if (!empty($plans) && count($plans) > 0)
                    @foreach ($plans as $plan)
                        @php
                            $isFeatured = strtolower($plan->name) === 'standard plan';
                            // Limit labels
                            $productLimit = $plan->limit_products >= 9999 ? 'Unlimited' : $plan->limit_products;
                            $userLimit = $plan->limit_users >= 9999 ? 'Unlimited' : $plan->limit_users;
                        @endphp
                        <div class="pricing-card {{ $isFeatured ? 'featured' : '' }}">
                            @if ($isFeatured)
                                <span class="featured-badge">Popular</span>
                            @endif
                            <div class="plan-name">{{ $plan->name }}</div>
                            <div class="plan-price-wrap">
                                <span class="plan-currency">৳</span>
                                <span class="plan-price">{{ number_format($plan->price, 0) }}</span>
                                <span class="plan-cycle">/{{ $plan->billing_cycle ?? 'month' }}</span>
                            </div>
                            <ul class="plan-features-list">
                                <li class="plan-feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Up to <strong>{{ $productLimit }}</strong> Products</span>
                                </li>
                                <li class="plan-feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Up to <strong>{{ $userLimit }}</strong> Users</span>
                                </li>
                                <li class="plan-feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>POS Cashier Terminal</span>
                                </li>
                                <li class="plan-feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Live Stock Ledger</span>
                                </li>
                                @if (strtolower($plan->name) === 'enterprise plan')
                                    <li class="plan-feature-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Formula cost analysis</span>
                                    </li>
                                    <li class="plan-feature-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>24/7 Dedicated Support</span>
                                    </li>
                                @elseif(strtolower($plan->name) === 'standard plan')
                                    <li class="plan-feature-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Formula cost analysis</span>
                                    </li>
                                    <li class="plan-feature-item disabled">
                                        <i class="bi bi-x-circle"></i>
                                        <span>24/7 Dedicated Support</span>
                                    </li>
                                @else
                                    <li class="plan-feature-item disabled">
                                        <i class="bi bi-x-circle"></i>
                                        <span>Formula cost analysis</span>
                                    </li>
                                    <li class="plan-feature-item disabled">
                                        <i class="bi bi-x-circle"></i>
                                        <span>24/7 Dedicated Support</span>
                                    </li>
                                @endif
                            </ul>
                            <button type="button" class="plan-btn" @click="$dispatch('set-tab', 'register')">
                                Get Started
                            </button>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback if database is empty -->
                    <!-- Basic -->
                    <div class="pricing-card">
                        <div class="plan-name">Basic Plan</div>
                        <div class="plan-price-wrap">
                            <span class="plan-currency">৳</span>
                            <span class="plan-price">999</span>
                            <span class="plan-cycle">/month</span>
                        </div>
                        <ul class="plan-features-list">
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Up to <strong>50</strong> Products</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Up to <strong>2</strong> Users</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>POS Cashier Terminal</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Live Stock Ledger</span>
                            </li>
                            <li class="plan-feature-item disabled">
                                <i class="bi bi-x-circle"></i>
                                <span>Formula cost analysis</span>
                            </li>
                        </ul>
                        <button type="button" class="plan-btn" @click="$dispatch('set-tab', 'register')">
                            Get Started
                        </button>
                    </div>

                    <!-- Standard -->
                    <div class="pricing-card featured">
                        <span class="featured-badge">Popular</span>
                        <div class="plan-name">Standard Plan</div>
                        <div class="plan-price-wrap">
                            <span class="plan-currency">৳</span>
                            <span class="plan-price">1,999</span>
                            <span class="plan-cycle">/month</span>
                        </div>
                        <ul class="plan-features-list">
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Up to <strong>200</strong> Products</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Up to <strong>5</strong> Users</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>POS Cashier Terminal</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Live Stock Ledger</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Formula cost analysis</span>
                            </li>
                        </ul>
                        <button type="button" class="plan-btn" @click="$dispatch('set-tab', 'register')">
                            Get Started
                        </button>
                    </div>

                    <!-- Enterprise -->
                    <div class="pricing-card">
                        <div class="plan-name">Enterprise Plan</div>
                        <div class="plan-price-wrap">
                            <span class="plan-currency">৳</span>
                            <span class="plan-price">4,999</span>
                            <span class="plan-cycle">/month</span>
                        </div>
                        <ul class="plan-features-list">
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Unlimited</strong> Products</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span><strong>Unlimited</strong> Users</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>POS Cashier Terminal</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Live Stock Ledger</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Formula cost analysis</span>
                            </li>
                            <li class="plan-feature-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>24/7 Dedicated Support</span>
                            </li>
                        </ul>
                        <button type="button" class="plan-btn" @click="$dispatch('set-tab', 'register')">
                            Get Started
                        </button>
                    </div>
                @endif
            </div>
        </section>

        <!-- ── Footer ── -->
        <footer class="site-footer">
            &copy; {{ date('Y') }} {{ config('', 'BakeryPOS') }} &mdash; Solution Clime. All rights reserved.
            &nbsp;·&nbsp; Built for Modern Craft Bakeries.
        </footer>

    </div><!-- /page-wrap -->

    <script>
        function statsAnimator() {
            return {
                sales: 0,
                orders: 0,
                items: 0,
                _ts: 18450,
                _to: 247,
                _ti: 134,
                fmt(v) {
                    return Math.round(v).toLocaleString('en-US');
                },
                init() {
                    const dur = 1800,
                        steps = 60;
                    let tick = 0;
                    const timer = setInterval(() => {
                        tick++;
                        const ease = 1 - Math.pow(1 - tick / steps, 3);
                        this.sales = this._ts * ease;
                        this.orders = Math.round(this._to * ease);
                        this.items = Math.round(this._ti * ease);
                        if (tick >= steps) clearInterval(timer);
                    }, dur / steps);
                }
            };
        }
    </script>

</body>

</html>
