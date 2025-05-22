<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Pass unread notification count to all views for admin
        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                $unreadCount = Auth::user()->unreadNotifications->count();
                $view->with('unreadNotificationCount', $unreadCount);
            }
        });
    }
}
