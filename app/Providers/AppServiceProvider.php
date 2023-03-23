<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Artisan;

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
        // Artisan::call("cache:clear");
		// Artisan::call("route:clear");
		// Artisan::call("config:clear");
		// Artisan::call("view:clear");
    }
}
