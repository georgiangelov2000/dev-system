<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use View;
use Auth;

class AppServiceProvider extends ServiceProvider {

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Paginator::useBootstrap();

        view()->composer('app', function ($view) {
            $isAuth = Auth::check();

            return $view
                    ->with('isAuth', $isAuth);
        });
    }

}
