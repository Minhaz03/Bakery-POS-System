<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BakeryPOS') }} — Account Verification</title>
    <link rel="icon" href="{{ asset('favPOS.png') }}" type="image/png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --amber:   #f59e0b;
            --amber2:  #fbbf24;
            --bg:      #0c0a09;
            --surface: #1c1917;
            --text:    #fafaf9;
            --muted:   #a8a29e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Background */
        .bg-canvas {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 60% 50% at 50% 10%, rgba(245,158,11,.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 50% 90%, rgba(99,102,241,.06) 0%, transparent 65%),
                var(--bg);
        }

        .grain {
            position: fixed; inset: 0; z-index: 1; pointer-events: none; opacity: .025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 200px;
        }

        .guest-container {
            position: relative; z-index: 2;
            width: 100%; max-width: 440px;
            padding: 1.5rem;
        }

        .auth-card {
            background: rgba(22,19,17,.88);
            backdrop-filter: blur(24px) saturate(1.6);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 24px;
            padding: 2.2rem 2rem;
            box-shadow:
                0 0 0 1px rgba(245,158,11,.05),
                0 30px 70px rgba(0,0,0,.6),
                0 0 50px -20px rgba(245,158,11,.1);
        }

        .logo-wrap {
            display: flex; flex-direction: column; align-items: center;
            margin-bottom: 1.8rem;
        }

        .logo-box {
            width: 50px; height: 50px; border-radius: 14px;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: .65rem;
            box-shadow: 0 6px 20px rgba(245,158,11,.3);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem; font-weight: 800; color: var(--text);
            letter-spacing: -.01em;
        }

        .brand-sub {
            font-size: .65rem; font-weight: 700; color: var(--amber);
            text-transform: uppercase; letter-spacing: .08em; margin-top: .15rem;
        }

        /* Common Custom Styles for components child views */
        .form-input-custom {
            width: 100%;
            background: rgba(10,8,6,.75) !important;
            border: 1.5px solid rgba(255,255,255,.08) !important;
            border-radius: 12px !important;
            padding: .75rem .9rem !important;
            font-size: .86rem !important;
            color: var(--text) !important;
            outline: none !important;
            transition: border-color .2s, box-shadow .2s, background .2s !important;
        }

        .form-input-custom:focus {
            border-color: rgba(245,158,11,.45) !important;
            background: rgba(18,14,10,.9) !important;
            box-shadow: 0 0 0 3.5px rgba(245,158,11,.1) !important;
        }

        .btn-submit-custom {
            width: 100%; padding: .8rem 1rem; border: none; cursor: pointer;
            border-radius: 12px; font-family: 'Outfit', sans-serif;
            font-size: .92rem; font-weight: 700; letter-spacing: .02em;
            background: linear-gradient(135deg, var(--amber) 0%, #f97316 100%);
            color: #0c0a09;
            box-shadow: 0 6px 22px rgba(245,158,11,.25);
            transition: all .2s; display: flex; align-items: center; justify-content: center;
            gap: .5rem; margin-top: 1.2rem;
        }

        .btn-submit-custom:hover {
            background: linear-gradient(135deg, var(--amber2) 0%, #fb923c 100%);
            box-shadow: 0 8px 28px rgba(245,158,11,.35);
            transform: translateY(-1px);
        }

        .btn-submit-custom:active { transform: scale(.98); }

        .label-custom {
            font-size: .7rem !important; font-weight: 600 !important;
            letter-spacing: .05em !important; text-transform: uppercase !important;
            color: #a8a29e !important; margin-bottom: .4rem !important; display: block;
        }

        .link-accent-custom {
            font-size: .72rem; color: var(--amber); font-weight: 600;
            text-decoration: none; transition: color .2s;
        }
        .link-accent-custom:hover { color: var(--amber2); }

        .desc-custom {
            font-size: .78rem; color: var(--muted); line-height: 1.6;
            margin-bottom: 1.2rem; text-align: center;
        }
    </style>
</head>
<body>
    <div class="bg-canvas"></div>
    <div class="grain"></div>

    <div class="guest-container">
        <div class="auth-card">
            
            <div class="logo-wrap">
                <a href="/" style="text-decoration:none; display:flex; flex-direction:column; align-items:center;">
                    <div class="logo-box">🧁</div>
                    <span class="brand-name">{{ config('app.name', 'BakeryPOS') }}</span>
                    <span class="brand-sub">Verification Portal</span>
                </a>
            </div>

            <div>
                {{ $slot }}
            </div>

        </div>
    </div>
</body>
</html>
