<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\PettyCash;
use Illuminate\Support\Facades\Auth;

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
            if (Auth::check()) {
                $hasOpenPettyCash = PettyCash::where('status', 'open')
                    ->where('user_id', Auth::id())
                    ->exists();

                $view->with('hasOpenPettyCash', $hasOpenPettyCash);
            } else {
                $view->with('hasOpenPettyCash', false);
            }
        });
    }
}
