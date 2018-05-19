<?php

namespace App\Console\Commands;

use DB;
use Redis;
use Carbon\Carbon;
use App\Models\Order;
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
    protected $signature = 'auto:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '72H到期后改申请完成中的订单为已完成订单';

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
        try {
            $carbon = new Carbon;

            $orders = Redis::hGetAll('complete_orders');

            if (isset($orders) && is_array($orders) && count($orders) > 0) {
                foreach ($orders as $orderNo => $time) {
                    $order = Order::where('no', $orderNo)->first();

                    if (! isset($order) || empty($order)) {
                        Redis::hDel('complete_orders', $orderNo);
                        continue;
                    }

                    if ($order->status != 14) {
                        Redis::hDel('complete_orders', $orderNo);
                        continue;
                    }

                    $overTime = $carbon->parse($time)->addHours(3);

                    $readyOnTime = $carbon->diffInSeconds($overTime, false);

                    if ($readyOnTime < 0) {
                        DailianFactory::choose('complete')->run($orderNo, 0, true);
                        Redis::hDel('complete_orders', $orderNo);
                        myLog('auto-complete-success', ['自动完成' => '成功']);
                    }
                }
            }
        } catch (DailianException $e) {
            myLog('auto-complete-fail', ['失败原因' => $e->getMessage()]);
        } catch (Exception $e) {
            myLog('auto-complete-fail', ['失败原因' => $e->getMessage()]);
        }
    }
}
