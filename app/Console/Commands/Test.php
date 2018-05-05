<?php

namespace App\Console\Commands;

use App\Models\OrderDetail;
use App\Repositories\Frontend\OrderRepository;
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
    protected $signature = 'Test  {type}';

    protected $orderRepository;
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
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
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
        $type = $this->argument('type');
        if ($type == 1) {
            $this->shos91();
        } elseif ($type == 2) {
            $this->dd373();
        } elseif ($type == 3) {
            $this->my();
        } else {
            $this->syncDD373();
        }

    }

    /**
     * dd373订单查询
     */
    public function dd373()
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
                            '第三方' => $orderDetail['data']['platformOrderNo'],
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
                    myLog('dd373-show-order-query-err', [$item->no, '平台' => $show91OrderNO->field_value, '状态' => config('order.status_leveling')[$item->status]]);
                }

            }
        }
    }

    public function shos91()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('gainer_primary_user_id', 8456)->get();

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
                        '91状态' => isset($this->show91Status[$orderDetail['data']['order_status']]) ? $this->show91Status[$orderDetail['data']['order_status']] : '',
                        '我们状态' => config('order.status_leveling')[$item->status],
                        '91从格' => $orderDetail['data']['price'],
                        '我们价格' => $item->amount,
                        '价格' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                    ]);
                }
            }
        }
    }

    public function my()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('gainer_primary_user_id', 8737)->get();

        foreach ($allOrder as $item) {
            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'mayi_order_no')->first();
            if (isset($show91OrderNO->field_value) && !empty($show91OrderNO->field_value)) {
                // 按接单方取订单详情
                try {
                    $orderDetail = MayiDailianController::orderDetail(['mayi_order_no' => $show91OrderNO->field_value]);
                    if ($orderDetail['data']) {
                        myLog('my-show-order-query', [
                            '第三方' => $item->no,
                            '我们订单号' => $item->no,
                            '第三方订单号' => $show91OrderNO->field_value,
                            '第三方状态' => $orderDetail['data']['status_type'],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '第三方价格' => $orderDetail['data']['paymoney'],
                            '我们价格' => $item->amount,
                            '价格' => $item->amount == $orderDetail['data']['paymoney'] ? '是' : '否'
                        ]);
                    }
                } catch (\Exception $exception) {
                    myLog('my-show-order-query-err', [$item->no]);
                }

            }
        }
    }

    public function syncDD373()
    {
        $allOrder = \App\Models\Order::where('service_id', 4)->where('status', 1)->get();
        foreach ($allOrder as $item) {
            $detail = $this->orderRepository->levelingDetail($item->no);
            DD373Controller::updateOrder($detail);
        }
    }
}
