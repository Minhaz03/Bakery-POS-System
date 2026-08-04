<x-layouts.admin title="Notifications">
    <div class="topbar">
        <h2 class="topbar-title">Notifications</h2>
        @if($notifications->count() > 0)
            <form method="POST" action="{{ route('dashboard.notifications.markAllAsRead') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding:8px 16px;">
                    <i class="bi bi-check2-all"></i> Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="page-content">
        @if(session('success'))
            <div style="background:#dcfce7;color:#15803d;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:14px;font-weight:500;border:1px solid #bbf7d0;">
                <i class="bi bi-check-circle-fill" style="margin-right:8px;"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body" style="padding:0;">
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;text-align:left;font-size:14px;">
                        <tbody style="color:#334155;">
                            @forelse($notifications as $notification)
                                <tr style="border-bottom:1px solid #f1f5f9; {{ !$notification->is_read ? 'background:#f8fafc;' : '' }}">
                                    <td style="padding:20px;width:40px;vertical-align:top;">
                                        @if(!$notification->is_read)
                                            <span style="width:10px;height:10px;background:#6366f1;border-radius:50%;display:inline-block;margin-top:6px;"></span>
                                        @else
                                            <span style="width:10px;height:10px;background:#cbd5e1;border-radius:50%;display:inline-block;margin-top:6px;"></span>
                                        @endif
                                    </td>
                                    <td style="padding:20px;vertical-align:top;">
                                        <div style="font-weight:{{ !$notification->is_read ? '700' : '600' }};color:#0f172a;margin-bottom:4px;font-size:15px;">
                                            {{ $notification->title }}
                                        </div>
                                        <div style="color:#64748b;line-height:1.5;margin-bottom:8px;">
                                            {{ $notification->message }}
                                        </div>
                                        <div style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:12px;">
                                            <span><i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                            
                                            @if(!$notification->is_read)
                                                <form method="POST" action="{{ route('dashboard.notifications.markAsRead', $notification) }}" style="margin:0;">
                                                    @csrf
                                                    <button type="submit" style="background:none;border:none;color:#6366f1;font-size:12px;cursor:pointer;padding:0;">Mark as read</button>
                                                </form>
                                            @endif

                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" style="color:#0ea5e9;text-decoration:none;"><i class="bi bi-link-45deg"></i> View Details</a>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding:20px;text-align:right;vertical-align:top;width:80px;">
                                        <form method="POST" action="{{ route('dashboard.notifications.destroy', $notification) }}" style="margin:0;" onsubmit="return confirm('Delete this notification?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;padding:8px;border-radius:4px;" title="Delete" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='none'">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding:64px 24px;text-align:center;color:#64748b;">
                                        <i class="bi bi-bell-slash" style="font-size:48px;display:block;margin-bottom:16px;color:#cbd5e1;"></i>
                                        <p style="font-size:16px;font-weight:500;margin:0;">No notifications found</p>
                                        <p style="font-size:13.5px;margin-top:8px;">You're all caught up!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($notifications->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #e2e8f0;">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
