<?php

namespace App\Console\Commands;

use App\Models\OrderDetail;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Show91;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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
        $allOrder = \App\Models\Order::where('service_id', 4)->get();

        foreach ($allOrder as $item) {
            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();
            if (isset($show91OrderNO->field_value) && !empty($show91OrderNO->field_value)) {
                // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);
                // 91 是待验收
                if ($orderDetail['data']) {
                    myLog('91-show-order-query', [
                        '我们订单号' => $item->no,
                        '91订单号' => $show91OrderNO->field_value,
                        '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
                        '91从格' => $orderDetail['data']['price'],
                        '我们价格' => $item->amount,
                        '价格' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                    ]);
                }
            }
        }
    }
}
