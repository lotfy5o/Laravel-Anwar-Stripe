<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\View\View;
use Illuminate\Support\Facades;
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
    public function boot(): void
    {
        Facades\View::composer('*', function (View $view) {
            $cart = Cart::session()->first();
            $view->with('cart', $cart);
        });
    }
}
