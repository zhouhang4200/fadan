<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\Registered\CreateUserAsset',
            'App\Listeners\Registered\AddDefaultPermission',
            'App\Listeners\Registered\UserWeightInit',
            'App\Listeners\Registered\CreateLevelingAutoSmsTemplate',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\Login\WriteLoginRecord',
        ],
        'App\Events\Punish' => [
            'App\Listeners\Punish\SendMessage',
        ],
        'App\Events\AutoRequestInterface' => [
            'App\Listeners\Leveling\ChangeStatus',
        ],
        // 订单完成事件
        'App\Events\OrderFinish' => [
            'App\Listeners\OrderFinish\tb',
        ],
        // 订单被接单事件
        'App\Events\OrderReceiving' => [
            'App\Listeners\OrderReceiving\tb',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
