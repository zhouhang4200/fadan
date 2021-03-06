<?php

namespace App\Console\Commands;

use DB;
use RedisFacade;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
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
        $carbon = new Carbon;

        $orders = RedisFacade::hGetAll('complete_orders');

        if (isset($orders) && is_array($orders) && count($orders) > 0) {
            foreach ($orders as $orderNo => $time) {
                try {
                    $order = Order::where('no', $orderNo)->first();

                    if (! isset($order) || empty($order)) {
                        RedisFacade::hDel('complete_orders', $orderNo);
                        continue;
                    }

                    if ($order->status != 14) {
                        RedisFacade::hDel('complete_orders', $orderNo);
                        continue;
                    }
                    // 除了cnf 推荐号之外的都是3天
                    $overTime = $carbon->parse($time)->addDays(3);

                    // 如果是dnf推荐号,则时间为1天
                    $isDnf = OrderDetail::where('order_no', $order->no)
                        ->where('field_name', 'game_leveling_type')
                        ->where('field_value', '推荐号')
                        ->first();

                    if (isset($isDnf) && ! empty($isDnf) && $order->game_id == 86) {
                        $overTime = $carbon->parse($time)->addDays(1);
                    }
                    // 是否到了完成时间
                    $readyOnTime = $carbon->diffInSeconds($overTime, false);

                    if ($readyOnTime < 0) {
                        DailianFactory::choose('complete')->run($orderNo, 0, true);
                        RedisFacade::hDel('complete_orders', $orderNo);
                    }
                } catch (DailianException $e) {
                    myLog('auto-complete-fail', ['订单号' => $order->no ?? '', '失败原因' => $e->getMessage()]);
                    continue;
                } catch (Exception $e) {
                    myLog('auto-complete-fail', ['订单号' => $order->no ?? '', '失败原因' => $e->getMessage()]);
                    continue;
                }
            }
        }
    }
}
