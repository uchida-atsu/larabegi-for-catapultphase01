<?php

namespace App\Providers;

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
            $unreadCount = 0;
            if (Auth::check()) {
                $unreadCount = Tweet::where('user_id', '!=', Auth::id()) // ★ 自分の投稿は除外
                ->whereDoesntHave('reads', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->count();

            }
            $view->with('unreadCount', $unreadCount);
        });
    }
}
