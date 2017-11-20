<?php
namespace App\Extensions\Weight;

/**
 * Class WeightServiceProvider
 * @package App\Extensions\Weight
 */
class WeightServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the provider.
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
        $this->app->bind('weight', function ($app) {
            return new \App\Extensions\Weight\Weight();
        });
    }

}
