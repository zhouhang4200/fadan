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
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(Weight::class, function () {
            return new Weight();
        });

        $this->app->alias(Weight::class, 'weight');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Weight::class, 'weight'];
    }
}
