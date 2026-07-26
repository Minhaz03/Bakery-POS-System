<x-guest-layout>
    <div class="desc-custom">
        This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div style="margin-bottom: 1rem;">
            <label for="password" class="label-custom">Password</label>
            <input id="password" class="form-input-custom" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            @if ($errors->has('password'))
                <span style="font-size: .7rem; color: #fb7185; font-weight: 500; margin-top: .3rem; display: block;">
                    <i class="bi bi-x-circle-fill"></i> {{ $errors->first('password') }}
                </span>
            @endif
        </div>

        <button type="submit" class="btn-submit-custom">
            <i class="bi bi-shield-check-fill"></i> Confirm Password
        </button>
    </form>
</x-guest-layout>
