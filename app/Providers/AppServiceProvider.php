<?php

namespace App\Providers;

use App\Models\UserTransferAccountInfo;
use Session;
use Illuminate\Support\Facades\Blade;
use App\Observers\ModelObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
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
        $this->share();
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

    public function share()
    {
        view()->composer('frontend/v1/layouts/app', function ($view) {
            $transferInfo = UserTransferAccountInfo::where('user_id', auth()->user()->getPrimaryUserId())->first();
            $view->with('transferInfo', $transferInfo);
        });
    }
}
