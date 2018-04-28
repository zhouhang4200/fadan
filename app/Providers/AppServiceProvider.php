<?php

namespace App\Providers;

use Session;
use Illuminate\Support\Facades\Blade;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Extensions\Session\FlashHandler;
use Encore\RedisManager\RedisManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        RedisManager::auth(function($request){
            if (in_array(auth()->user()->id, [1, 17, 18])) {
                return true;
            }
            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
