<x-guest-layout>
    <div class="desc-custom">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div style="background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.18); color: #6ee7b7; border-radius: 11px; padding: .7rem .95rem; font-size: .76rem; margin-bottom: 1.2rem;">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.2rem;">
        <form method="POST" action="{{ route('verification.send') }}" style="flex: 1; margin-right: 1rem;">
            @csrf
            <button type="submit" class="btn-submit-custom" style="margin-top: 0; padding: .65rem .85rem; font-size: .82rem;">
                Resend Verification
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link-accent-custom" style="background:none; border:none; cursor:pointer;">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
