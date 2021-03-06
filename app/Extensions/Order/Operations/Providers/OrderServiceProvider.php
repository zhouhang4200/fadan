<?php

namespace App\Extensions\Order\Operations\Providers;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('order', function ($app) {
            return new \App\Extensions\Order\Operations\Base\Order;
        });
    }
}
