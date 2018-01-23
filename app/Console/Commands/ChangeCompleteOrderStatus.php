<?php

namespace App\Console\Commands;

use DB;
use Redis;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Exceptions\DailianException;
use App\Extensions\Dailian\Controllers\DailianFactory;

/**
 * 代练平台 申请完成订单 24小时，发单没有点完成操作， 系统自动完成该订单
 */
class ChangeCompleteOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '24H到期直接改申请完成订单为已完成订单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 24小时自动更改订单状态为已结算
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $carbon = new Carbon;
            $now = $carbon->parse(Carbon::now()->toDateTimeString());
            $orders = Redis::hGetAll('complete_orders');

            foreach ($orders as $orderNo => $time) {
                $overTime = $carbon->parse($carbon->parse($time)->addDays(1)->toDateTimeString());
                // $overTime = $carbon->parse($carbon->parse($time)->addMinutes(1)->toDateTimeString());
                $readyOnTime = $carbon->diffInSeconds($overTime, false);
  
                if ($readyOnTime <= 0) {
                    Redis::hDel('complete_orders', $orderNo);
                    // 订单完成操作
                    DailianFactory::choose('complete')->run($orderNo, 0);
                }
            }
        } catch (DailianException $e) {
            DB::rollback();
        }
        DB::commit();
    }
}
