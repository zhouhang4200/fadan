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
            'App\Listeners\ChangeTaobaoTradeStatus',
        ],
        // 订单撤销中事件
        'App\Events\OrderRevoking' => [
            'App\Listeners\OrderRevoking\SendSms',
            'App\Listeners\ChangeStatus',
        ],
        // 订单仲裁中事件
        'App\Events\OrderArbitrationing' => [
            'App\Listeners\OrderArbitrationing\SendSms',
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 订单申请验收事件
        'App\Events\OrderApplyComplete' => [
            'App\Listeners\OrderApplyComplete\SendSms',
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 异常
        'App\Events\OrderAbnormal' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 取消异常
        'App\Events\OrderCancelAbnormal' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 取消申诉
        'App\Events\OrderCancelArbitration' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 取消完成
        'App\Events\OrderCancelComplete' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],

        // 完成
        'App\Events\OrderComplete' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\WriteOrderBasicData',
            'App\Listeners\ChangeStatus',
        ],
        // 锁定
        'App\Events\OrderLock' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 未接单
        'App\Events\OrderNoReceive' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 下架
        'App\Events\OrderOffSaled' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 接单
        'App\Events\OrderPlaying' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\WriteOrderBasicData',
            'App\Listeners\ChangeStatus',
            'App\Listeners\OrderReceiving\SendSms',
        ],
        // 不同意撤销
        'App\Events\OrderRefuseRevoke' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 取消锁定
        'App\Events\OrderUnLock' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],
        // 取消撤销
        'App\Events\OrderUnRevoke' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\ChangeStatus',
        ],

        // 订单仲裁后事件
        'App\Events\OrderArbitrationed' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\WriteOrderBasicData',
            'App\Listeners\ChangeStatus',
        ],
        // 订单撤销后事件
        'App\Events\OrderRevoked' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\WriteOrderBasicData',
            'App\Listeners\ChangeStatus',
        ],
        // 订单撤单后事件
        'App\Events\OrderDelete' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\WriteOrderBasicData',
            'App\Listeners\ChangeStatus',
        ],
        // 订单强制撤销后事件
        'App\Events\OrderForceRevoke' => [
            'App\Listeners\ChangeTaobaoTradeStatus',
            'App\Listeners\WriteOrderBasicData',
            'App\Listeners\ChangeStatus',
        ],
        // 创建或修改订单基础数据
        'App\Events\OrderBasicData' => [
            'App\Listeners\OrderBasicData\WriteOrderBasicData',
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
