<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Spatie\Permission\PermissionRegistrar', function ($app) {
            return new \Spatie\Permission\PermissionRegistrar($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}