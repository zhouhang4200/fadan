<?php

namespace App\Console\Commands;

use App\Events\OrderApplyComplete;
use App\Events\OrderArbitrationing;
use App\Events\OrderRevoking;
use App\Extensions\Dailian\Controllers\Complete;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Extensions\Dailian\Controllers\Revoked;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\DailianMama;
use App\Services\Show91;
use App\Services\SmSApi;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use LogisticsDummySendRequest;
use OSS\Core\OssException;
use OSS\OssClient;
use TopClient;
use TradeFullinfoGetRequest;
use TraderatesGetRequest;

/**
 * Class OrderTestData
 * @package App\Console\Commands
 */
class Temp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Temp {no}{user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试';

    protected $message = [];

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

    protected $messageBeginId = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $status = $this->argument('no');
        $user = $this->argument('user');

        dd($this->complete($status, $user));

        // 我们是待接单
        if ($status == 1) {
            // 获取所有没有接单的单
            $allOrder = \App\Models\Order::where('service_id', 4)->where('status', 1)->get();

            foreach ($allOrder as $item) {

                $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

                if ($show91OrderNO->field_value) {
                    // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                    $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                    // 代练中
                    if ($orderDetail['data']['order_status'] == 1) {

                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/receive/order', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '要修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);
                    } else if ($orderDetail['data']['order_status'] == 2) {
                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/receive/order', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '待验收改接单',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);

                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/apply/complete', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '改成待验收',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);

                    } else {
                        myLog('temp-log', [
                            '类型' => '不用修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '状态码' => $orderDetail['data']['order_status']
                        ]);
                    }
                } else {
                    myLog('temp-log', [
                        '类型' => '没有91单号',
                        '我们订单号' => $item->no,
//                    '91订单号' => $show91OrderNO->field_value,
//                    '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
//                    '状态码' => $orderDetail['data']['order_status']
                    ]);
                }
            }

        } else if($status == 13) {
            // 获取所有没有接单的单
            $allOrder = \App\Models\Order::where('service_id', 4)->where('status', 13)->get();

            foreach ($allOrder as $item) {

                $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

                if (isset($show91OrderNO->field_value)) {
                    // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                    $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                    // 91 是待验收
                    if ($orderDetail['data']['order_status'] == 2 || $orderDetail['data']['order_status'] == 3) {

                        // 调用自己接单接口
                        $client = new Client();
                        $response = $client->request('POST', 'http://js.qsios.com/api/apply/complete', [
                            'form_params' => [
                                'sign' => 'a46ae5de453bfaadc8548a3e48c151db',
                                'orderNo' => $show91OrderNO->field_value,
                            ],
                        ]);
                        $result = json_decode($response->getBody()->getContents());

                        myLog('temp-log', [
                            '类型' => '要修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '修改结果' => $result
                        ]);

                    } else {
                        myLog('temp-log', [
                            '类型' => '不用修改',
                            '我们订单号' => $item->no,
                            '91订单号' => $show91OrderNO->field_value,
                            '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '状态码' => $orderDetail['data']['order_status']
                        ]);
                    }
                } else {
                    myLog('temp-log', [
                        '类型' => '没有91单号',
                        '我们订单号' => $item->no,
//                    '91订单号' => $show91OrderNO->field_value,
//                    '状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
//                    '状态码' => $orderDetail['data']['order_status']
                    ]);
                }
            }
        } else  {
//            (new Revoked())->run('2018042109054600000281', 8711, 0);
//            $this->addPrice();
            dd($this->queryShow91Order($status));
        }
    }

    public function get($orderNO, $beginId = 0)
    {
        $message = DailianMama::chatOldList($orderNO, $beginId);

        if (count($message['list'])) {
            $this->message = array_merge($this->message, $message['list']);
            $this->get($orderNO, $message['beginid']);
        }
    }

    /**
     * 对比91订单状态;
     */
    public function show91OrderStatus()
    {
        // 获取所有没有接单的单
        $allOrder = \App\Models\Order::where('service_id', 4)->get();

        foreach ($allOrder as $item) {

            $show91OrderNO = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

            if (isset($show91OrderNO->field_value) && $show91OrderNO->field_value) {
                // 如果91订单状态是接单，调我们自己接单接口，如果不是记录一下他们状态
                $orderDetail = Show91::orderDetail(['oid' => $show91OrderNO->field_value]);

                // 91 是待验收
                if (isset($orderDetail['data'])) {

                    myLog('status-log', [
                        '类型' => '双方存在订单',
                        '我们订单号' => $item->no,
                        '91订单号' => $show91OrderNO->field_value,
                        '91状态' => $this->show91Status[$orderDetail['data']['order_status']],
                        '我们状态' => config('order.status_leveling')[$item->status],
                        '我们价格' => $item->amount,
                        '91价格' => $orderDetail['data']['price'],
                        '价格相等' => $item->amount == $orderDetail['data']['price'] ? '是' : '否'
                    ]);

                } else {
                    myLog('status-log', [
                        '类型' => '没有91单信息',
                        '我们订单号' => $item->no,
                        '我们状态' => config('order.status_leveling')[$item->status],
                    ]);
                }
            } else {
                myLog('status-log', [
                    '类型' => '没有91单号',
                    '我们订单号' => $item->no,
                    '我们状态' => config('order.status_leveling')[$item->status],
                ]);
            }
        }
    }

    /**
     * 查询show91订单
     * @param $orderNO
     */
    public function queryShow91Order($orderNO)
    {
        return Show91::orderDetail(['oid' => $orderNO]);
    }

    /**
     * 完成订单
     * @param $no
     * @param $user
     */
    public function complete($no, $user)
    {
        (new Complete())->run($no, $user, 0);
    }

    public function addPrice()
    {
        $params = [
            'account' => config('show91.account'),
            'sign' => config('show91.sign'),
        ];

        $options = [
            'oid' => 'ORD180419220853663712',
            'appwd' => config('show91.password'),
            'cash' => 6,
        ];

        $options = array_merge($params, $options);

        $client = new Client;
        $response = $client->request('POST', config('show91.url.addPrice'), [
            'query' => $options,
        ]);
       dd($response->getBody()->getContents());
    }

    public function e()
    {
       $allOrder =  \App\Models\Order::where('service_id', 4)->whereIn('status', [1,13,14,16,20])->get();

        foreach ($allOrder as $item) {
            $detail = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

            if ($detail) {

                if ($detail->field_value) {
                    $show91 = $this->queryShow91Order($detail->field_value);

                    myLog('price', [
                        'no'=> $item->no,
                        '91no' => $detail->field_value,
                        '我们价格' => $item->amount,
                        '91' => $show91['data']['price'],
                        '是否相关' => $show91['data']['price'] == $item->amount ? '是' : '否',
                    ]);
                } else {
                    myLog('price', [
                        'no'=> $item->no,
                        '我们价格' => $item->amount,
                        '错误' => '没有91单号',
                    ]);
                }
            } else {
                myLog('price', [
                    'no'=> $item->no,
                    '我们价格' => $item->amount,
                    '错误' => '没有详情',
                ]);
            }
        }
    }

}