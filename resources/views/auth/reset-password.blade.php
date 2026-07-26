<x-guest-layout>
    <div class="desc-custom">
        Create a new secure password for your operator account.
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div style="margin-bottom: 1rem;">
            <label for="email" class="label-custom">Email Address</label>
            <input id="email" class="form-input-custom" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
            @if ($errors->has('email'))
                <span style="font-size: .7rem; color: #fb7185; font-weight: 500; margin-top: .3rem; display: block;">
                    <i class="bi bi-x-circle-fill"></i> {{ $errors->first('email') }}
                </span>
            @endif
        </div>

        <!-- Password -->
        <div style="margin-bottom: 1rem;">
            <label for="password" class="label-custom">New Password</label>
            <input id="password" class="form-input-custom" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            @if ($errors->has('password'))
                <span style="font-size: .7rem; color: #fb7185; font-weight: 500; margin-top: .3rem; display: block;">
                    <i class="bi bi-x-circle-fill"></i> {{ $errors->first('password') }}
                </span>
            @endif
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom: 1.2rem;">
            <label for="password_confirmation" class="label-custom">Confirm Password</label>
            <input id="password_confirmation" class="form-input-custom" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            @if ($errors->has('password_confirmation'))
                <span style="font-size: .7rem; color: #fb7185; font-weight: 500; margin-top: .3rem; display: block;">
                    <i class="bi bi-x-circle-fill"></i> {{ $errors->first('password_confirmation') }}
                </span>
            @endif
        </div>

        <button type="submit" class="btn-submit-custom">
            <i class="bi bi-shield-check-fill"></i> Reset Password &amp; Login
        </button>
    </form>
</x-guest-layout>
