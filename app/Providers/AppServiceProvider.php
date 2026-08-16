<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant 'Super Admin' role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        View::composer('components.layouts.admin', function ($view) {
            $unreadNotificationsCount = 0;
            $latestNotifications = collect([]);
            $openTicketsCount = 0;

            if (auth()->check()) {
                $unreadNotificationsCount = Notification::unread()->count();
                $latestNotifications = Notification::orderBy('created_at', 'desc')->take(5)->get();
                $openTicketsCount = \App\Models\SupportTicket::open()->count();
            }

            $view->with([
                'unreadNotificationsCount' => $unreadNotificationsCount,
                'latestNotifications' => $latestNotifications,
                'openTicketsCount' => $openTicketsCount,
            ]);
        });

        View::composer('components.layouts.saas', function ($view) {
            $pendingSaasTicketsCount = 0;
            $unreadSaasNotificationsCount = 0;
            $latestSaasNotifications = collect([]);

            try {
                $pendingSaasTicketsCount = \App\Models\SupportTicket::withoutGlobalScopes()->pendingAdmin()->count();
                $unreadSaasNotificationsCount = Notification::withoutGlobalScopes()
                    ->whereNull('tenant_id')
                    ->where('is_read', false)
                    ->count();
                $latestSaasNotifications = Notification::withoutGlobalScopes()
                    ->whereNull('tenant_id')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Throwable $e) {
                // Table might not exist yet during migration
            }

            $view->with([
                'pendingSaasTicketsCount' => $pendingSaasTicketsCount,
                'unreadSaasNotificationsCount' => $unreadSaasNotificationsCount,
                'latestSaasNotifications' => $latestSaasNotifications,
            ]);
        });
    }
}
