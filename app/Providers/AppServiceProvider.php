<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        View::composer('components.layouts.admin', function ($view) {
            $unreadNotificationsCount = 0;
            $latestNotifications = collect([]);

            if (auth()->check()) {
                $unreadNotificationsCount = Notification::unread()->count();
                $latestNotifications = Notification::orderBy('created_at', 'desc')->take(5)->get();
            }

            $view->with([
                'unreadNotificationsCount' => $unreadNotificationsCount,
                'latestNotifications' => $latestNotifications
            ]);
        });
    }
}
