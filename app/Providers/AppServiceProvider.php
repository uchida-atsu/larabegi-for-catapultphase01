<?php

// namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Tweet;

use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unreadCount = Tweet::whereDoesntHave('reads', function ($query) {
                    $query->where('user_id', Auth::id());
                })->count();
            } else {
                $unreadCount = 0;
            }
            $view->with('unreadCount', $unreadCount);
        });
    }
}
