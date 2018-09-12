<?php

namespace App\Console\Commands;

use App\Extensions\Dailian\Controllers\Arbitrationed;
use App\Extensions\Dailian\Controllers\Complete;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Recharge;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\OrderRepository;
use App\Services\KamenOrderApi;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Leveling\Show91Controller;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

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
     * @var array
     */
    private $apiUrl = [
        "http://ls.kamennet.com/",
        "http://api1.kabaling.com/",
        "http://ls1.kabaling.com/",
        "http://api2.kabaling.com/",
        "http://ls.kabaling.com/",
        "http://api3.kamennet.com/",
        "http://api4.kabaling.com/",
        "http://api1.kamennet.com/",
        "http://ls1.kamennet.com/",
        "http://ls2.kamennet.com/",
        "http://api2.kamennet.com/",
        "http://ls2.kabaling.com/"
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $a = [

            1439121077,
            1439068525,
            1439035548,
            1439008543,
            1439002667,
            1438996311,
            1438973853,
            1438961679,
            1438943578,
            1438940676,
            1438933511,
            1438932409,
            1438928329,
            1438927485,
            1438888443,
            1438869888,
            1438863998,
            1438842680,
            1438706095,
            1438693579,
            1438682873,
            1438681890,
            1438670273,
            1438662933,
            1438640130,
            1438559405,
            1438552844,
            1438550904,
            1438511436,
            1438271131,
            1438256449,
            1438179625,
        ];

        foreach ($a as $item) {
            $param =  'SiteId=107560&OrderNo=' . $item. '&OrderStatus=' . strtolower(urlencode('成功'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('充值成功')) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

            $url =  $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' .  $param  .  $sign;

            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));

            myLog('km-api', ['107560', $item, $response->getBody()->getContents()]);
        }

die;


        $updateData['tid'] = 132184583048396707;
        \DB::connection()->enableQueryLog();
        TaobaoTrade::where('tid', $updateData['tid'])->first();


dd(
    \DB::getQueryLog()
);
die;
      dd(  Show91Controller::delete([
          'show91_order_no' => 'ORD180723143905497765'
      ]));
        // 查询所有代练订单
        $query = Order::where('service_id', 4)->groupBy('foreign_order_no');

        $query->chunk(500, function ($orders) {
            foreach ($orders as $item) {

                if ($item->foreign_order_no) {
                    // 查找相同的淘宝单号
                    $all = Order::where('foreign_order_no', $item->foreign_order_no)->orderBy('created_at', 'asc')->pluck('creator_primary_user_id', 'no')->toArray();

                    $i = 0;
//                    $insert = [];
                    foreach ($all as $key => $val) {
                        if ($i != 0) {
                            $exist = OrderDetail::where('order_no', $key)->where('field_name', 'is_repeat')->first();

                            if (!$exist) {
                                $insert = [
                                    'order_no' =>   $key,
                                    'creator_primary_user_id' => $val,
                                    'field_display_name' => '是否为重发',
                                    'field_name' => 'is_repeat',
                                    'field_name_alias' => 'is_repeat',
                                    'field_value' => 1,
//                                'created_at' => date('Y-m-d H:i:s'),
//                                'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                OrderDetail::create($insert);
                            } else {
                                $exist->field_value = 1;
                                $exist->field_name_alias = 'is_repeat';
                                $exist->save();
                            }
                        }
                        $i++;
                    }
//                    if (count($insert)) {
//
//                    }
                }
            }
        });

        die;
        $type = $this->argument('type');
      dd(  Show91Controller::getMessage([
          'show91_order_no' => 'ORD180521105449004776'
        ]));
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
            if(isset($detail['dd373_order_no'])) {
                DD373Controller::updateOrder($detail);
            } else {
                myLog('sync', [$item->no]);
            }
        }
    }
}
