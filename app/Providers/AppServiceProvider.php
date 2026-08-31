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
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with([
                    'notifications' => Notification::where('user_id', auth()->id())
                        ->latest()
                        ->limit(5)
                        ->get(),
                    'unreadCount' => Notification::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->count(),
                ]);
            }
        });

    }
}
