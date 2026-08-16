<x-layouts.admin title="403 - Access Denied">
    <div style="min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 40px; max-width: 500px; width: 100%; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px;">
                <i class="bi bi-shield-slash-fill"></i>
            </div>
            <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Access Denied</h2>
            <p style="font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                You do not have sufficient permissions to perform this action or access this resource.
            </p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-outline" style="font-size: 13px; padding: 8px 18px;">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="font-size: 13px; padding: 8px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none;">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
