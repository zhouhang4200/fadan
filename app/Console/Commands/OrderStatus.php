<?php

namespace App\Console\Commands;

use App\Models\OrderDetail;
use App\Services\RedisConnect;

use App\Services\Show91;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;

/**
 * 订单状态对比
 * Class OrderStatus
 * @package App\Console\Commands
 */
class OrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:Status {platform=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查询订单状态';

    /**
     * @var array
     */
    protected $show91Status = [
        0 => "已发布",
        1 => "代练中",
        2 => "待验收",
        3 => "待结算",
        4 => "已结算",
        5 => "已挂起",
        6 => "已撤单",
        7 => "已取消",
        10 => "等待工作室接单",
        11 => "等待玩家付款",
        12 => "玩家超时未付款",
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $platform= $this->argument('platform');

        if($platform == 1) {
            $this->show91();
        } else if($platform == 2) {

        }
    }

    public function show91()
    {
        // 获取所有没有接单的单
        $allOrder = \App\Models\Order::where('service_id', 4)->get();

        foreach ($allOrder as $item) {

            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

            if (isset($show91OrderNO->field_value)) {
                // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                // 91 是待验收
                if (isset($orderDetail['data'])) {
                    myLog('show-91-order-status', [
                        '类型' => '双方都存在订单',
                        '我们订单号' => $item->no,
                        '91订单号' => $show91OrderNO->field_value,
                        '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
                    ]);
                } else {
                    myLog('show-91-order-status', [
                        '说明' => '我们有单，91没单',
                        '我们订单号' => $item->no,
                        '我们状态' => config('order.status_leveling')[$item->status],
                    ]);
                }
            } else {
                myLog('show-91-order-status', [
                    '说明' => '没有91单号',
                    '我们订单号' => $item->no,
                    '我们状态' => config('order.status_leveling')[$item->status],
                ]);
            }
        }
    }
}