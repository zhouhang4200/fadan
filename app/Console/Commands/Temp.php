<?php

namespace App\Console\Commands;

use App\Events\OrderApplyComplete;
use App\Events\OrderArbitrationing;
use App\Events\OrderRevoking;
use App\Extensions\Dailian\Controllers\Complete;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Extensions\Dailian\Controllers\Delete;
use App\Extensions\Dailian\Controllers\ForceRevoke;
use App\Extensions\Dailian\Controllers\Revoked;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\DailianMama;
use App\Services\KamenOrderApi;
use App\Services\Leveling\DD373Controller;
use App\Services\Leveling\MayiDailianController;
use App\Services\Leveling\Show91Controller;
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
        $no = $this->argument('no');
        $user = $this->argument('user');

        dd(Show91Controller::delete([
            'show91_order_no'     => 'ORD180609201236319728',
        ]));

        $order = [
            '2018052410595700000771',
            '2018052411030300000788',
            '2018052411070100000807',
            '2018052411092900000827',
            '2018052411125500000855',
            '2018052412444600001722',
            '2018052413052700001950',
            '2018052413125600002015',
            '2018052413293400002216',
            '2018052413365800002317',
            '2018052413485600002467',
            '2018052413572800002576',
            '2018052414011000002636',
            '2018052414035700002692',
            '2018052414361800003080',
            '2018052414364900003086',
            '2018052414365700003088',
            '2018052414381500003106',
            '2018052414484800003277',
            '2018052414521300003330',
            '2018052414580700003429',
            '2018052415083300003570',
            '2018052415094500003596',
            '2018052415110800003619',
            '2018052415164300003714',
            '2018052415171200003723',
            '2018052415275200003912',
            '2018052415282400003921',
            '2018052415323200003986',
            '2018052416063300004439',
            '2018052416272700004750',
            '2018052416274700004754',
            '2018052416275300004757',
            '2018052416291400004776',
            '2018052416311600004814',
            '2018052416383500004934',
            '2018052416475800005119',
            '2018052416513300005205',
            '2018052416533200005249',
            '2018052417260200005799',
            '2018052417284900005842',
            '2018052417591000006334',
            '2018052418143300006622',
            '2018052418214200006763',
            '2018052418263300006865',
            '2018052418450500007161',
            '2018052418532400007310',
            '2018052419074800007578',
            '2018052419083400007587',
            '2018052419115600007635',
            '2018052419162300007689',
            '2018052419463900007991',
            '2018052420161700008385',
            '2018052420171000008391',
            '2018052420214600008431',
            '2018052420483700008779',
            '2018052421322800009225',
            '2018052422180400009703',
            '2018052422211400009733',
            '2018052422441600010015',
            '2018052423031500010204',
            '2018052423210500010362',
            '2018052500464200000267',
            '2018052507190500000740',
            '2018052508521900000914',
            '2018052509410500001134',
            '2018052509453600001191',
            '2018052511160800001898',
            '2018052511303100001954',
            '2018052511571300002057',
            '2018052512093700002120',
            '2018052512403800002274',
            '2018052512460100002306',
            '2018052512541100002352',
            '2018052512580900002375',
            '2018052513102400002445',
            '2018052513151400002464',
            '2018052513181900002482',
            '2018052513300200002535',
            '2018052513372000002574',
            '2018052513492100002658',
            '2018052513501400002666',
            '2018052513524200002690',
            '2018052513581500002732',
            '2018052514022700002764',
            '2018052514131100002813',
            '2018052514195200002850',
            '2018052514221600002870',
            '2018052514292200002914',
            '2018052414424900003180',
            '2018052416301700004799',
            '2018052420185400008405',
            '2018052421005300008922',
            '2018052421071100008988',
            '2018052514362500002965',
            '2018052514384000002978',
            '2018052514392800002983',
            '2018052514450300003021',
            '2018052514505900003065',
            '2018052411304600000993',
            '2018052515081900003196',
            '2018052515164300003258',
            '2018052515200800003277',
            '2018052515342100003366',
            '2018052518301700004913',
            '2018052520342300005980',
            '2018052523254000007799',
            '2018052615291700002183',
            '2018052617100800002711',
            '2018052621211700003768',
            '2018052711322000000959',
            '2018052712582500001382',
            '2018052812303200000921',
            '2018052818363200001881',
            '2018052912061700000590',
            '2018052916321300001136',
            '2018052918044300001313',
            '2018053007211800000275',
            '2018053010401400000542',
            '2018053017142500001369',
            '2018053017184000001387',
            '2018053111072300000668',
            '2018053123421400002349',
            '2018060109113200000520',
            '2018060117450600001987',
            '2018060218042300001718',
        ];

        foreach ($order as $item) {
            $this->forceRevoke($item, 1);
        }

        die;
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
    public function complete($no)
    {

        $order  = \App\Models\Order::where('no', $no)->first();
        if ($order) {
            dump((new Complete())->run($order->no, $order->creator_primary_user_id, 0));
        }

    }

    /**
     * 完成订单
     * @param $no
     * @param $user
     */
    public function revoked($no)
    {

        $order  = \App\Models\Order::where('no', $no)->first();
        if ($order) {
            dump((new Revoked())->run($order->no, $order->creator_primary_user_id, 0));
        }

    }

    /**
     * 删除
     * @param $no
     * @param $user
     */
    public function delete($no, $user)
    {
        (new Delete())->run($no, $user, 0);
    }

    /**
     * 强的撤销
     * @param $no
     * @param $user
     */
    public function forceRevoke($no, $user)
    {
        $order  = \App\Models\Order::where('no', $no)->first();
        if ($order) {
            (new ForceRevoke())->run($order->no, $order->gainer_primary_user_id);
        }

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

    /**
     * 同步91己验收但集市没有验收的单
     */
    public function e()
    {
       $allOrder =  \App\Models\Order::where('service_id', 4)
           ->where('status', '14')
           ->get();

        foreach ($allOrder as $item) {
            $detail = OrderDetail::where('order_no', $item->no)->where('field_name', 'show91_order_no')->first();

            if ($detail) {

                if ($detail->field_value) {
                    $show91 = $this->queryShow91Order($detail->field_value);
                    if ($show91['data']['order_status'] == 4) {
                        myLog('show-91-14-1', [
                            'no'=> $item->no,
                            '91no' => $detail->field_value,
                            '91状态' => $this->show91Status[$show91['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '我们价格' => $item->amount,
                            '91' => $show91['data']['price'],
                            '是否相关' => $show91['data']['price'] == $item->amount ? '是' : '否',
                        ]);
                        try {
                            $this->complete($item->no, $item->creator_primary_user_id);
                        } catch (\Exception $exception) {
                            myLog('show-91-14-3', ['no'=> $item->no]);
                        }
                    } else {
                        myLog('show-91-14-2', [
                            'no'=> $item->no,
                            '91no' => $detail->field_value,
                            '91状态' => $this->show91Status[$show91['data']['order_status']],
                            '我们状态' => config('order.status_leveling')[$item->status],
                            '我们价格' => $item->amount,
                            '91' => $show91['data']['price'],
                            '是否相关' => $show91['data']['price'] == $item->amount ? '是' : '否',
                        ]);
                    }
                }
            }
        }
    }

}