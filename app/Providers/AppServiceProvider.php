<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.navbar', function ($view) {
            if (Auth::check()) {
                $allNotifications = Notification::where('to_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $allNotifications = collect(); // Jika belum login, kosongkan
            }
    
            $view->with('allNotifications', $allNotifications);
        });
    }
}
