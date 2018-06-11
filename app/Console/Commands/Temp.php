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

        $this->show91Order();
        die;


        dd(Show91Controller::orderDetail([
            'show91_order_no'     => 'ORD180610050803769786',
        ]));
        dd(Show91Controller::delete([
            'show91_order_no'     => 'ORD180609201239498796',
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


    public function show91Order()
    {
        $order = [
            "2018060920361300000848",
            "2018060913410800000475",
            "2018060716163100000434",
            "2018060600034100000005",
            "2018060519263100001162",
            "2018060413163400000800",
            "2018060403535700000159",
            "2018060300032200000014",
            "2018060215250500001333",
            "2018060122303000002706",
            "2018060117262000001932",
            "2018060115312200001675",
            "2018053122221700002179",
            "2018053122033800002144",
            "2018052820260200002148",
            "2018052810153200000573",
            "2018052718443500002379",
            "2018052618135200003088",
            "2018052615543600002317",
            "2018052613011100001556",
            "2018052514304700002926",
            "2018052417213800005748",
            "2018052416484100005139",
            "2018052413580200002585",
            "2018052411540100001196",
            "2018052411260000000951",
            "2018052410512300000721",
            "2018052405203000000197",
            "2018052322545000002326",
            "2018052310282400000629",
            "2018052223554800003361",
            "2018052217013700002154",
            "2018052214450100001665",
            "2018052210400800000738",
            "2018052208532700000319",
            "2018052208342400000272",
            "2018052200080900000019",
            "2018052200040500000008",
            "2018052018443500002179",
            "2018052015573800001665",
            "2018052011081200000765",
            "2018051915473600001821",
            "2018051914020700001513",
            "2018051913213800001368",
            "2018051909081700000580",
            "2018051801245100000133",
            "2018051718531700001440",
            "2018051712252600000647",
            "2018051712161800000627",
            "2018051711422300000541",
            "2018051709231000000276",
            "2018051708540000000230",
            "2018051610574600000476",
            "2018051517272500001684",
            "2018051517162500001654",
            "2018051517152200001651",
            "2018051511543500000801",
            "2018051419535800001292",
            "2018051417502600001090",
            "2018051416593700001008",
            "2018051413424800000694",
            "2018051412223900000556",
            "2018051406075200000168",
            "2018051404404400000150",
            "2018051403365100000137",
            "2018051322350000001628",
            "2018051322052400001578",
            "2018051320205800001406",
            "2018051318113000001210",
            "2018051315415000000938",
            "2018051220220000001316",
            "2018051219333800001250",
            "2018051119572500001747",
            "2018051113562500001026",
            "2018051113383300000976",
            "2018051110001400000459",
            "2018051103052300000205",
            "2018051101282100000157",
            "2018051100182000000035",
            "2018051021481000001730",
            "2018051020230300001531",
            "2018051015210800000941",
            "2018051015104800000922",
            "2018051011220100000521",
            "2018051010175800000404",
            "2018051000150600000024",
            "2018050922584400001616",
            "2018050913242100000582",
            "2018050910280100000316",
            "2018050902523300000103",
            "2018050815542600001015",
            "2018050813001000000674",
            "2018050723525400002058",
            "2018050723031900001965",
            "2018050720160900001567",
            "2018050718244100001354",
            "2018050718003300001304",
            "2018050716565900001177",
            "2018050708583200000282",
            "2018050705271300000185",
            "2018050700240200000044",
            "2018050622342800002285",
            "2018050622243000002258",
            "2018050621592700002203",
            "2018050605372700000243",
            "2018050600405000000084",
            "2018050516202100002218",
            "2018050513171500001714",
            "2018050509404600000980",
            "2018050421491600002329",
            "2018050417230000001647",
            "2018050416330100001527",
            "2018050416300800001520",
            "2018050411033800000678",
            "2018050320245500001792",
            "2018050318341500001617",
            "2018050317121300001449",
            "2018050316234200001364",
            "2018050314451700001140",
            "2018050312285600000849",
            "2018050311363700000726",
            "2018050308451700000269",
            "2018050303002400000162",
            "2018050218122100001184",
            "2018050212500100000542",
            "2018050211045000000371",
            "2018050109020800000359",
            "2018050108550100000345",
            "2018043015520700001358",
            "2018043014053500001146",
            "2018043013471500001111",
            "2018042923564100002538",
            "2018042920080500001930",
            "2018042916163700001437",
            "2018042915202400001315",
            "2018042914055000001155",
            "2018042912553800000989",
            "2018042911344300000783",
            "2018042910062800000543",
            "2018042901530900000169",
            "2018042901393800000140",
            "2018042901274100000117",
            "2018042821501600002411",
            "2018042816480100001691",
            "2018042810492000000772",
            "2018042805210400000263",
            "2018042711435700000746",
            "2018042709243400000299",
            "2018042701432600000102",
            "2018042620050800001168",
            "2018042614475000000688",
            "2018042613310300000570",
            "2018042523081300001359",
            "2018042407362000000152",
            "2018042006210300000096",
            "2018041918003100000845",
            "2018041915053100000615",
            "2018041914332000000569",
            "2018041817562200000683",
            "2018012016090500000981"
        ];

        // 打开文件资源，不存在则创建
        $fp = fopen(storage_path('logs/show91.csv'), 'a');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
        // 处理头部标题
        fputcsv($fp, [
            '内部订单',
            '91单号',
            '内部价格',
            '91价格',
            '内部状态',
            '91状态',
            '订单时间',
        ]);
        foreach ($order as $item) {
            // 查询我们的价格与91的价格与订单状态我们的状态与价格
            $orderInfo = \App\Models\Order::where('no', $item)->with(['detail'])->first();
            $orderDetail = $orderInfo->detail->pluck('field_value', 'field_name');

            // 查询91订单状态与价格
            if (isset($orderDetail['show91_order_no'])) {
                $show91Order = Show91Controller::orderDetail([
                    'show91_order_no' => $orderDetail['show91_order_no']
                ]);

                if ($show91Order) {
                    // 写入并关闭资源
                    fputcsv($fp, [
                        $orderInfo->no . "\t",
                        $orderDetail['show91_order_no'],
                        $orderInfo->amount,
                        $show91Order['data']['price'] + 0,
                        config('order.status_leveling')[$orderInfo->status],
                        $this->show91Status[$show91Order['data']['order_status']],
                    ]);

                }
            }
        }
        fclose($fp);
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