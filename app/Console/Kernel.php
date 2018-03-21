<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\DailySettlementPlatformAsset',
        'App\Console\Commands\DailySettlementUserAsset',
        'App\Console\Commands\OrderAssign',
        'App\Console\Commands\OrderAssignTemp',
        'App\Console\Commands\OrderTestData',
        'App\Console\Commands\WriteUserOrderDetails',
        'App\Console\Commands\WriteUserOrderMoney',
        'App\Console\Commands\Temp',
        'App\Console\Commands\UserWeightUpdate',
        'App\Console\Commands\OrderConfirm',
        'App\Console\Commands\WriteDataEveryDay',
        'App\Console\Commands\EmployeeStatistic', // 代练平台员工统计
        'App\Console\Commands\OrderStatistic', // 代练平台订单统计
        'App\Console\Commands\ChangeCompleteOrderStatus', // 24小时自动更新待验收为完成
        'App\Console\Commands\PlatformStatistic', // 平台订单统计
        'App\Console\Commands\AddNoticeOrderFromRedis',
        'App\Console\Commands\Task\RoomCardRecharge',
        'App\Console\Commands\TestDeleteOrder', // 删除订单
        'App\Console\Commands\TestAppealOrder', // 仲裁订单
        'App\Console\Commands\AddOurNoticeOrderFromRedis', // 从redis获取我们平台操作失败的报警订单
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('daily-settlement:user-asset')->daily();
        $schedule->command('daily-settlement:platform-asset')->daily();;
        $schedule->command('UserWeightUpdate')->daily();
        $schedule->command('write:orders')->daily();
        $schedule->command('write:data')->daily();
        $schedule->command('write:user-order-moneys')->daily();
        $schedule->command('Order:Confirm')->everyFiveMinutes();
        $schedule->command('employee:statistic')->daily();
        $schedule->command('order:statistic')->daily();
        $schedule->command('change:status')->everyMinute();
        $schedule->command('platform:statistic')->daily();
        $schedule->command('order:notice')->everyMinute();
        $schedule->command('add:notice')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
