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
            'App\Listeners\Registered\CreateTransferAccountInfo',
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
            'App\Listeners\OrderFinish\SendSms',
        ],
        // 订单被接单事件
        'App\Events\OrderReceiving' => [
            'App\Listeners\OrderReceiving\SendSms',
        ],
        // 订单撤销中事件
        'App\Events\OrderRevoking' => [
            'App\Listeners\OrderRevoking\SendSms',
        ],
        // 订单仲裁中事件
        'App\Events\OrderArbitrationing' => [
            'App\Listeners\OrderArbitrationing\SendSms',
        ],
        // 订单申请验收事件
        'App\Events\OrderApplyComplete' => [
            'App\Listeners\OrderApplyComplete\SendSms',
        ],
        // 订单仲裁后事件
        'App\Events\OrderArbitrationed' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
        ],
        // 订单撤销后事件
        'App\Events\OrderRevoked' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
        ],
        // 订单撤单后事件
        'App\Events\OrderDelete' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
        ],
        // 订单强制撤销后事件
        'App\Events\OrderForceRevoke' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
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
