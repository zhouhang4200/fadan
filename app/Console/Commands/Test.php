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

    protected $dd373 = [
        1 => "未接单",
        4 => "代练中",
        5 => "待验收",
        6 => "已完成",
        9 => "已撤消",
        10 => "已结算",
        11 => "已锁定",
        12 => "异常",
        13 => "仲裁中",
        14 => "已仲裁",

    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $allOrder = \App\Models\Order::where('service_id', 4)->where('gainer_primary_user_id', 8739)->get();

        foreach ($allOrder as $item) {
            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'dd373_order_no')->first();
            if (isset($show91OrderNO->field_value) && !empty($show91OrderNO->field_value)) {
                // 按接单方取订单详情
                try {
                    $orderDetail = DD373Controller::orderDetail(['dd373_order_no' => $show91OrderNO->field_value]);
                    if ($orderDetail['data']) {
                        myLog('dd373-show-order-query', [
                            '第三方' => $item->no,
                            '我们订单号' => $item->no,
                            '第三方订单号' => $show91OrderNO->field_value,
                            '第三方状态' => isset($this->dd373[$orderDetail['data']['orderStatus']]) ? $this->dd373[$orderDetail['data']['orderStatus']] : '',
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '第三方价格' => $orderDetail['data']['price'],
                            '我们价格' => $item->amount,
                            '价格' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                        ]);
                    }
                } catch (\Exception $exception) {
                    myLog('dd373-show-order-query-err', [$item->no]);
                }

            }
        }
    }

}
