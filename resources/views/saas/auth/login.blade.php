<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaaS Admin Login</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">
    <!-- Google Fonts -->
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; }
    </style>
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f1f5f9;">
        <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 400px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background: #6366f1; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 24px; margin-bottom: 15px;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0;">SaaS Admin</h1>
                <p style="font-size: 14px; color: #64748b; margin-top: 5px;">Super Administrator Login</p>
            </div>

            @if($errors->any())
                <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="margin: 0; color: #b91c1c; font-size: 13.5px; font-weight: 500;">
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('saas.login.post') }}">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; outline: none; transition: border-color 0.2s;">
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">Password</label>
                    <input type="password" name="password" required style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 14px; outline: none; transition: border-color 0.2s;">
                </div>
                <div style="margin-bottom: 24px; display: flex; align-items: center;">
                    <input type="checkbox" name="remember" id="remember" style="margin-right: 8px;">
                    <label for="remember" style="font-size: 13px; color: #475569;">Remember me</label>
                </div>
                <button type="submit" style="width: 100%; background: #6366f1; color: white; border: none; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
    </div>
</body>
</html>
