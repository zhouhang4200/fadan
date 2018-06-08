<?php

namespace App\Http\Controllers;

use App\Events\OrderReceiving;
use App\Http\Controllers\Backend\Data\DayDataController;
use App\Services\KamenOrderApi;
use App\Services\SmSApi;
use App\Services\TmallOrderApi;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Services\Leveling\WanziController;
use Auth;
use App\Models\OrderBasicData;
use Asset;
// use GuzzleHttp\Client;
use App\Extensions\Asset\Recharge;
use App\Extensions\Asset\Withdraw;
use App\Extensions\Asset\Freeze;
use App\Extensions\Asset\Unfreeze;
use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Refund;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Models\PlatformAsset;
use Carbon\Carbon;
use App\Repositories\Commands\PlatformAssetDailyRepository;
use Order as OrderFacede;
use App\Extensions\Order\Operations\Create;
use App\Extensions\Order\Operations\GrabClose;
use App\Extensions\Order\Operations\Receiving;
use App\Extensions\Order\Operations\Delivery;
use App\Extensions\Order\Operations\DeliveryFailure;
use App\Extensions\Order\Operations\AskForAfterService;
use App\Extensions\Order\Operations\AfterServiceComplete;
use App\Extensions\Order\Operations\TurnBack;
use App\Extensions\Order\Operations\Complete;

use App\Models\ForeignOrder;

use App\Extensions\Order\Operations\Payment;
use App\Extensions\Order\Operations\Cancel;


use App\Repositories\Frontend\UserWithdrawOrderRepository;
use App\Repositories\Api\UserRechargeOrderRepository;

use Artisan;
use App\Models\UserAmountFlow;
use App\Models\UserWithdrawOrder;
use App\Models\Order as OrderModel;
use App\Models\UserReceivingUserControl;
use App\Models\Order;
use Log;
use Exception;
use App\Services\Show91;

use App\Events\NotificationEvent;
use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\ThirdServer;
use App\Models\ThirdArea;
use App\Models\ThirdGame;
use App\Models\OrderDetail;

class TestController extends Controller
{
    public function insertLOL()
    {
        event(new OrderReceiving(Order::where('no', '2018041821030200000926')->first()));
        $options = [
            'aid' => 1,
        ];
        die;
        
        $res = Show91::getServer($options);

        $goodsTemplateId = GoodsTemplate::where('game_id', 78)->value('id');
        $goodsTemplateWidgetRegionId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
                            ->where('field_name', 'region')
                            ->value('id');

        $goodsTemplateWidgetServeId = GoodsTemplateWidget::where('goods_template_id', $goodsTemplateId)
                            ->where('field_name', 'serve')
                            ->value('id');

        $serves = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetServeId)
                ->pluck('field_value', 'id')->toArray();

        $regions = GoodsTemplateWidgetValue::where('goods_template_widget_id', $goodsTemplateWidgetRegionId)
                ->pluck('field_value', 'id')->toArray();

        // dd($serves, $regions);
        // dd($res['servers']);
        // $count = count($servers);
        // dd($count);
        $arr = [];
        foreach ($res['servers'] as $key => $server) {
            $arr[' '.$server['id']] = $server['server_name'];
        }

        // dd($arr);

        $options1 = [
            'aid' => 2,
        ];
        
        $res1 = Show91::getServer($options1);

        $arr1 = [];
        foreach ($res1['servers'] as $key => $server) {
            $arr1[' '.$server['id']] = $server['server_name'];
        }
        //
        $options2 = [
            'aid' => 30,
        ];

        $res2 = Show91::getServer($options2);

        $arr2 = [];
        foreach ($res2['servers'] as $key => $server) {
            $arr2[' '.$server['id']] = $server['server_name'];
        }
        //
        $options3 = [
            'aid' => 437,
        ];

        $res3 = Show91::getServer($options3);

        $arr3 = [];
        foreach ($res3['servers'] as $key => $server) {
            $arr3[' '.$server['id']] = $server['server_name'];
        }

        $twoArr = array_merge($arr, $arr1);
        $threeArr = array_merge($twoArr, $arr2);
        $thirdArrs = array_merge($threeArr, $arr3);

        // dd($thirdArrs);
        // 
        $keyArr = [];
        foreach ($thirdArrs as $key => $thirdArr) {
            foreach ($serves as $key1 => $serve) {
                if ($thirdArr == $serve) {
                    $keyArr[trim($key)] = $key1;
                }
            }
        }
        //
        $serverDatas = [];
        foreach ($keyArr as $thirdServeId => $serveId) {
            $serverDatas[$thirdServeId]['game_id'] = 78;
            $serverDatas[$thirdServeId]['third_id'] = 1;
            $serverDatas[$thirdServeId]['server_id'] = $serveId;
            $serverDatas[$thirdServeId]['third_server_id'] = $thirdServeId;
            $serverDatas[$thirdServeId]['created_at'] = date('Y-m-d H:i:s', time());
            $serverDatas[$thirdServeId]['updated_at'] = date('Y-m-d H:i:s', time());
        }
        // dd($serverDatas);
        $serverDatas = array_values($serverDatas);
        // dd($serverDatas);
        ThirdServer::insert($serverDatas);

        // 区
        $regionDatas = [];

        $options = [
            'gid' => 1,
        ];
        
        $res = Show91::getAreas($options);
        $thirdRegions = [];
        foreach ($res['areas'] as $key => $value) {
            $thirdRegions[$value['id']] = $value['area_name'];
        }

        // 我们的区
        $regionDatas = [];
        foreach ($thirdRegions as $thirdId => $thirdRegion) {
            foreach ($regions as $id => $region) {
                if ($thirdRegion == $region) {
                    $regionDatas[$thirdId] = $id;
                }
            }
        }

        $insertDatas = [];
        foreach ($regionDatas as $third => $id) {
            $insertDatas[$third]['game_id'] = 78;
            $insertDatas[$third]['third_id'] = 1;
            $insertDatas[$third]['area_id'] = $id;
            $insertDatas[$third]['third_area_id'] = $third;
            $insertDatas[$third]['created_at'] = date('Y-m-d H:i:s', time());
            $insertDatas[$third]['updated_at'] = date('Y-m-d H:i:s', time());
        }

        $insertDatas = array_values($insertDatas);

        ThirdArea::insert($insertDatas);

        // 游戏
        $gameDatas = [
            'third_id' => 1,
            'game_id' => 78,
            'third_game_id' => 1,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];
        ThirdGame::create($gameDatas);

        dd('写入成功');
    }


    public function index()
    {
        // $orders = OrderBasicData::where('date', '0000-00-00')->get();

        // foreach ($orders as $key => $order) {
        //     $date = Carbon::parse($order->order_created_at)->toDateString();
        //     $order->date = $date;
        //     $order->save();
        // }

        $thirdOrders = OrderBasicData::where('third', '0')->get();

        foreach ($thirdOrders as $thirdOrder) {
            $model = OrderDetail::where('order_no', $thirdOrder->order_no)->where('field_name', 'third')->first();
            if ($model->field_value) {
                $thirdOrder->third = $model->field_value;
                $thirdOrder->save();
            }
        }

        // $datas = OrderBasicData::get();

        // foreach ($datas as $data) {
        //     $ord = OrderModel::where('no', $data->order_no)->first();
        //     if ($ord->created_at->toDateTimeString() != $data->order_created_at) {
        //         $data->order_created_at = $ord->created_at->toDateTimeString();
        //         $data->save();
        //     }
        // }
dd('OK');


        $order = OrderModel::where('no', '2018060409274600000002')->first();
        dd($order->created_at->toDateTimeString());

        $client = new Client();
        $res = $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
            'json' => [
                'msgtype' => 'text',
                'text' => [
                    'content' => '订单双金必须大于0元'
                ],
                'at' => [
                    'isAtAll' => true
                ]
            ]
        ]);

        dd(3, $res->getBody()->getContents());


        $data = "bbuz\/0T\/HzAQ+ZFWscOAprjyPn6CZBuYQthl6wI5Dqx17vtLiozA9zDd6yqb800VxOzRc6xENvBKMUGNDgJFJyv8tXsSWr86fDvi3HmKKut\/9oLrRegK1KKkSwWPpceHM1kpgYYJnVDXu39YbsMPmW+\/kwDb0j\/N7p16fm1HpnHx+wXHUANrO31ieIQK1pnuZByaRSwEcL15RaUM5EfktcOm8IzRfxDNEdW6Xc584heOYhfme+pj1uT5L8kmRQWCuvuCEVlkvdJq5BxmWGcBeiIZijTDJbXzxAlJ9WqgXI8fYDTgkgn7lB28mynQB7dUKaPos1PaKyQkN45mjZyY42stJRQ9EFPxVa72oJJPDfGEmgMy69LGIvv8OZXcIbnVdLEOdX+1\/u4K6eP17M9GIVxJ6RSyStWjomXBy9SAr6IrqQYNTcH2vjoCwworE1hzPptinF2RimSOCetYGV0jFbMHjGyXCVPBGtJv9d997Id88kusgmvcKCpfaYqhMNN9rEs1AIYHe5V9aq6rY89A49iqyXGjW8XvFJKZic8SeF4ImVfSHZnWU1zwo7haAHHfPYk1l9+T+IYRgeDqocrl\/u99EtWCCMMhjk0N1ExAMLijDNzRZHuADS6u0kIMhuLwyDb5WAFWA4MS8vBwiDoBDyRzpNEXQLla5Y3drJugMgnYm3MoJQOYKnxonouj3VpfcA3jIY0lv7zlMDZj45ynM18XF99vtrQWJvkhxWUMgpLhIFYtIoDo\/sP5+9EGFCwnVBj9CNSMZPjIhf0fIbVDweapZbf3Wn8CAuFbWiRpzqGrT\/2xFABKaIt9Cg4B+K2I9V9wnAmakeno5deMeORCLgmqVZlY8sL3YdggL50osFunxSz54c2NgfWG4G2jM0mXbcxIM65dj2CEDJcXQWIyF4D8RFJlWmRJwwPhsLk0raB3K+LAPjcxuBehWYKM0UC+YLvi0XXESkjNxdFKN\/\/fg99WWgjJ9NHVzBgEjxmwx2c9F3jKauvNHatqhPnp6BaEmvlzzz99D1DklfGUL9IvHBhuzV4eX9B56wLI107xXFKWIgsKwQM2jZ3NFu7WYAiFrJgDo\/29XuKnzNgpYKHYG44t8yYXr985mVgKyflUd6Gm2y8+7ahoxBcW4Wyc9TZXlk+o\/rI9wk\/amDZ1wrU2uYmBcZjzpZFcvWG4n1qVim4hNzsMhA4eZHbYaojfOFRCuaeIuQWaHGcaLwYmbUfQRfEDm2c0ABAIW5PfDxQyymMrG1pXEIpyxMGyILsX+X63dw\/wNgALQmL5bD8uHkbedXaA94C0mwGvIlcjjHJZlhlHb71ihg3Ec3PFLa3pg84hDnQVnxy\/G19UdVpdkbVwnO3WJ8KhFE+UiU3xSSr6tGgTnneZ6bk1nlD9dUADEUnS6R4bRzaH8fmSJbalb6YOKry4nQqRLwr1CTfgRvaljm+dpdkR6phxw\/pHiXJioJNDokkDPbdzfsjYU7Teewm2ww7KKFi6LMzpPXCWcmOZwmPuBXVM3njP1iL\/8RDdRjTApW6xN5eM0iGPZwISXCJOp5CKQ+8O46yNlKPQiV\/4tFecieA6UqiTn+MHp5g\/9z5ZN9JCUDWFWGwMClPybIlTCA==";
        $iv = '1234567891111152';
        $key = '45584685d8e4f5e8';

        $datas = openssl_decrypt($data, 'aes-128-cbc', $key, 0, $iv);

dd($datas);





        $datas['wanzi_order_no'] = 'ORD180521165250247826';
        $info = WanziController::getArbitrationInfo($datas);


        $infos['arbitration_id'] = 35;
        $infos['content'] = '添加的证据';
        $infos['pic'] = '';
        $data = WanziController::addArbitrationInfo($infos);
        // dd($data);
dd($info);




        $orderDetail = OrderDetail::where('order_no', '2018051418371000000004')
        ->pluck('field_value', 'field_name')
            ->toArray();

        $detail = call_user_func_array([config('leveling.controller')[3], config('leveling.action')['orderDetail']], [$orderDetail]);
dd($detail);

        event(new OrderReceiving(Order::where('no', '2018041821030200000926')->first()));;die;
        $this->testReceiveOrder();
        return $this->addPrice();
        return $this->getDailianmamaInfo();
        return $this->testSwitch('hang', 10);
        $this->testClone();
        try {
            $order = Order::find(9);
            throw new Exception('我是错误信息');
        } catch (Exception $e) {
            dd($e->getMessage(), $order);

        }
        dd(4);
//        return (new SmSApi())->send(2, 18500132452, '您的订单已经被打接单，请不要登号', 1);
//        $client = new Client();
//        $result = $client->post('www.show91.com/oauth/addOrder', [
//            'headers' => [
//                'Content-type' => 'multipart/form-data',
//            ],
//            'form_params' => [
//                'account' => 'EFAE2BC69B8D4E16A3649992F031BDDB',
//                'sign' => '89abb1dfef56cdf21c315b3bc3670c5d',
//            ]
//        ]);

        $res = $client->post('www.show91.com/oauth/addOrder',[
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'body' => [
                    'account' => 'EFAE2BC69B8D4E16A3649992F031BDDB',
                    'sign' => '89abb1dfef56cdf21c315b3bc3670c5d',
            ]
        ]);
        dd($res->getStatusCode());

dd(1);
        $order = Order::where('no', '2017122715401700000011')->first();

        dd($order->levelingConsult->first()->toArray());
        $this->encrypt();
        return $this->decrypt();

        event(new NotificationEvent('orderRefund', ['amount' => '500.00', 'user_id' => 3]));
        exit("1234");
    }

    public function testReceiveOrder() 
    {
        $request = new HttpRequest();
        $request->setUrl('http://www.test.com/api/partner/order/receive');
        $request->setMethod(HTTP_METH_POST);

        $request->setQueryData(array(
          'sign' => 'd3e8dfceb794d8dbd771ee4db6573cf7',
          'order_no' => 'XQ20180503002810-13107',
          'app_id' => 'fPHUSGXWN461NRb5VGeFp0xoYYaGOAc0rXIqnMxRwAvCpYcQKR0xhFIJdSTI',
          'timestamp' => '2136127316',
          'hatchet_man_qq' => '',
          'hatchet_man_phone' => '13343450907111',
          'hatchet_man_name' => 'DD373打手'
        ));

        $request->setHeaders(array(
          'postman-token' => '11e65c70-58c6-e396-fdae-333bfc74253b',
          'cache-control' => 'no-cache'
        ));

        try {
          $response = $request->send();

          echo $response->getBody();
        } catch (HttpException $ex) {
          echo $ex;
        }
    }

    public function testAsset()
    {
        Asset::handle(new Recharge(5000.1234, Recharge::TRADE_SUBTYPE_AUTO, '2017101' . rand(1000, 9999), '自动充值', Auth::user()->id, 888));
        Asset::handle(new Freeze(1850.4312, Freeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '提现冻结', Auth::user()->id, 888));
        Asset::handle(new Withdraw(550.4565, Withdraw::TRADE_SUBTYPE_MANUAL, '2017101' . rand(1000, 9999), '提现成功', Auth::user()->id, 888));
        Asset::handle(new Unfreeze(310.2342, Unfreeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '解冻成功', Auth::user()->id, 888));
        Asset::handle(new Consume(220.4903, Consume::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '消费手续费', Auth::user()->id, 888));
        Asset::handle(new Refund(150.2348, Refund::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '手续费退款', Auth::user()->id, 888));
        Asset::handle(new Expend(70.2329, Expend::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '下订单', Auth::user()->id, 888));
        Asset::handle(new Income(40.0928, Income::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '接单发货', Auth::user()->id, 888));

        // 对账
        $platformAsset = PlatformAsset::first();
        $external = "$platformAsset->total_recharge - $platformAsset->total_withdraw = ";
        $external .= $platformAsset->total_recharge - $platformAsset->total_withdraw;

        $interior = "$platformAsset->amount + $platformAsset->managed + $platformAsset->balance + $platformAsset->frozen = ";
        $interior .= $platformAsset->amount + $platformAsset->managed + $platformAsset->balance + $platformAsset->frozen;
        dump($external, $interior);
    }

    public function testDaily(PlatformAssetDailyRepository $platformAssetDailyRepository)
    {
        $platformAssetDailyRepository->scriptrun('2017-10-18', '2017-10-21');
    }

    public function testOrder()
    {
        // Order::handle(new Create(1, 'taobao-123', 1, 4, 0, 111, ['account' => 'buer2202@163.com', 'version' => '1.0', 'region' => '微信71区']));
        // Order::handle(new Payment('2017111015303300000002', 1));
        // Order::handle(new GrabClose('2017111017452700000007', 1));
        // Order::handle(new Receiving('2017111017452700000007', 1));
        // Order::handle(new Delivery('2017111015492400000005', 1));
        Order::handle(new DeliveryFailure('2017111017452700000007', 1, "我去你妹"));
        // Order::handle(new AskForAfterService('2017111015492400000005', 1, '啦啦啦'));
        // Order::handle(new AfterServiceComplete('2017111015492400000005', 888, 5, '退5块钱'));
        // Order::handle(new TurnBack('2017111015492400000005', 2));
        // Order::handle(new Complete('2017111015492400000005', 28));
        // Order::handle(new Cancel('2017111017411200000006', 1));

        // $arr = \App\Models\OrderHistory::orderBy('id', 'desc')->first();
        // dump(unserialize($arr->before), unserialize($arr->after));
    }

    public function command()
    {
        $exitCode = Artisan::call('migrate');
    }

    public function encrypt()
    {
        $private_key = '-----BEGIN PRIVATE KEY-----
MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAL5CB0BHCL81Ov31
0aZzFv9e6vmzFsvOhdywog57gnJ+QC1lj8ILQ+iBaeseQYD5C9XG1jfVb2k5gpqy
UoIV00ySLri3+V8xY8isGgKISXdyq9+P1aRNq2RS3t49wf4xyQewbgKr0HoH09eV
FgTf6rQeH2MFB326QYFcUnEvKCfBAgMBAAECgYEAuiinKaiXkWfHMgjduwzvmq3I
Isyt6HtKFZcq6hrFl7ualhDC6e3V42EFP04ab9S/VMw6fOU1HvNrrGwBOVGbraeS
K45csy30KEMl6ZOm7rBdqHm3M1xjStWHrfQcvrd8ZM6lJr+8bGveWRwUoTC2kOJY
wg0pYa6hR2VAuHIteLUCQQDeKuH6hBgoE9Z8UvaQVYdn0cpgEZn1eqgAH5YkhhBZ
7x/CIlmASizMiWjgvuA5PShCgdcpbPx64meWIdvhVRQnAkEA2zspikwJLrzjbiOX
UndPzFUlpBV6H7K2f9M5iS05+kBmjzKMXNwMsUb4pjmakUG491OkHWGe36aNkunY
uYfN1wJAWuQ4Z4E7UMos6dgXP51+NB9EKGGLFz8DFGnXx0GB1wlZeNcMvsuZ4GQn
ICt3GHPI0MzF9hC8ipmtv2JCzsE76QJBAJDKnkDsvxPTRRI1B3g7vMRjaBza4nGV
Atuhkdp7uFMDvbjN1c5utyNOkGKYoPFWyubuovGUy+1CfzaMo8rFWrkCQQC9ZNaT
ziowtzttdpQ12IhLcdcfeS1gLtvQ3QIokwb3wHgdhB5knDSTYz/upgr9GRddCv8W
Iuli3G2IJNYc9Cwu
-----END PRIVATE KEY-----';
        $str = json_encode(['order_id' => 2017121917434400000002]); // 长度12,要加密的串
        // 十六进制
        $hexIv = '00000000000000000000000000000000';

        $key = 'PhtVnNtqe4a5R1W5vhwnzBfZ'; // PhtVnNtqe4a5R1W5vhwnzBfZ

        // ******
        $a = openssl_private_encrypt($key, $encrypted, $private_key) ? bin2hex($encrypted) : null;

// 42efbe94121f7583365f5bfc2cad4466f40163a6a9ff89880ef4f0d2f2217a950459c3b9972d5a1c3654fdb684b6b5b1608935c07420fbe5caf1c743c134308c379742c6c50e0c330dace4a4ef42ea84c04c392d3582248d6d9f19db396630e0da8d750f618db63d2e8c30c832ab92a3ff2bdf8df67a57925c9a9ffea040f8e0
// $encrypted = b"B´¥ö\x12\x1Fuâ6_[³,¡Df¶\x01cª® ëê\x0E¶­Ê‗!zò\x04Y├╣ù-Z\x1C6T²ÂäÂÁ▒`ë5└t ¹Õ╩±ÃC┴40î7ùBã┼\x0E\f3\r¼õñ´BÛä└L9-5é$ìmƒ\x19█9f0Ó┌ìu\x0FaìÂ=.î0╚2½Æú +▀ì÷zWÆ\Üƒ■á@°Ó"

        $hash = hash('sha256', $key, true); // b"7¿T\x1F þd\x04éàt;Yã█xùØ\x04IUHåój²¦ë╩¿¯Z" 十六进制

        //打开算法和模式对应的模块
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, ''); //mcrypt resource @472
        // 初始化加密所需的缓冲区
        // $this->hexToStr($hexIv) = \x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00
        $int = mcrypt_generic_init($td, $hash, $this->hexToStr($hexIv)); // 0
        //获得加密算法的分组大小
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); // 16
        // 16 - 12 = 4
        $pad = $block - (strlen($str) % $block); // 4
        // 重复一个字符串（字符， 次数）
        $str .= str_repeat(chr($pad), $pad); // name:zhouhang\x03\x03\x03
        //加密数据
        $encrypted = mcrypt_generic($td, $str); //b"àþ<$1\x18\x19W\x06cH¼ä‘w\x05"
        //对加密模块进行清理工作
        mcrypt_generic_deinit($td);
        //关闭加密模块
        mcrypt_module_close($td);
        // 函数把包含数据的二进制字符串转换为十六进制值
        return bin2hex($encrypted); // f38377f61cdad196d12f4a236f24210ba035e742057459d99aeb08686483aec1db2e11636fb469b0bd282629a7559600
    }

    private function hexToStr($hex)
    {
        $string = '';
        // hexdec() 十六进制转为 十进制
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            // ask码对应的字符
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    public function decrypt()
    {
        $private_key = '-----BEGIN PRIVATE KEY-----
MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAL5CB0BHCL81Ov31
0aZzFv9e6vmzFsvOhdywog57gnJ+QC1lj8ILQ+iBaeseQYD5C9XG1jfVb2k5gpqy
UoIV00ySLri3+V8xY8isGgKISXdyq9+P1aRNq2RS3t49wf4xyQewbgKr0HoH09eV
FgTf6rQeH2MFB326QYFcUnEvKCfBAgMBAAECgYEAuiinKaiXkWfHMgjduwzvmq3I
Isyt6HtKFZcq6hrFl7ualhDC6e3V42EFP04ab9S/VMw6fOU1HvNrrGwBOVGbraeS
K45csy30KEMl6ZOm7rBdqHm3M1xjStWHrfQcvrd8ZM6lJr+8bGveWRwUoTC2kOJY
wg0pYa6hR2VAuHIteLUCQQDeKuH6hBgoE9Z8UvaQVYdn0cpgEZn1eqgAH5YkhhBZ
7x/CIlmASizMiWjgvuA5PShCgdcpbPx64meWIdvhVRQnAkEA2zspikwJLrzjbiOX
UndPzFUlpBV6H7K2f9M5iS05+kBmjzKMXNwMsUb4pjmakUG491OkHWGe36aNkunY
uYfN1wJAWuQ4Z4E7UMos6dgXP51+NB9EKGGLFz8DFGnXx0GB1wlZeNcMvsuZ4GQn
ICt3GHPI0MzF9hC8ipmtv2JCzsE76QJBAJDKnkDsvxPTRRI1B3g7vMRjaBza4nGV
Atuhkdp7uFMDvbjN1c5utyNOkGKYoPFWyubuovGUy+1CfzaMo8rFWrkCQQC9ZNaT
ziowtzttdpQ12IhLcdcfeS1gLtvQ3QIokwb3wHgdhB5knDSTYz/upgr9GRddCv8W
Iuli3G2IJNYc9Cwu
-----END PRIVATE KEY-----';
        $code = "5c992c11a96d8da0d51564cc9f26d74951f99066f4c0a60abd43df093860f751db5a3c189056f62906ab2ffd8c4a4e27";
        $hash = '42efbe94121f7583365f5bfc2cad4466f40163a6a9ff89880ef4f0d2f2217a950459c3b9972d5a1c3654fdb684b6b5b1608935c07420fbe5caf1c743c134308c379742c6c50e0c330dace4a4ef42ea84c04c392d3582248d6d9f19db396630e0da8d750f618db63d2e8c30c832ab92a3ff2bdf8df67a57925c9a9ffea040f8e0'; // 加密得到的值
        // dd($hash);
        // $decryptKey = (openssl_private_decrypt(pack("H*", $hash), $decrypted, $private_key)) ? $decrypted : null;//
        // dd($decryptKey);

        // 十六进制
        $hexIv = '00000000000000000000000000000000';
        // 打开算法和模式对应的模块
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, ''); // mcrypt resource @473
        // 初始化加密所需的缓冲区
        $int = mcrypt_generic_init($td, 'PhtVnNtqe4a5R1W5vhwnzBfZ', $this->hexToStr($hexIv)); // 0
        //解密数据 pack 打包成二级制
        $str = mdecrypt_generic($td, pack("H*", $code)); // b"ô¤ÝtQw5WYŽh´C\x00Ò`"
        //获得加密算法的分组大小
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        //对加密模块进行清理工作
        mcrypt_generic_deinit($td);
        //关闭加密模块
        mcrypt_module_close($td);
        // FfMzOFYV4RNItDbkLiqHDnQ4
        return $this->strIppAdding($str);
    }

    /**
     * @param $string
     * @return bool|string
     */
    private function strIppAdding($string)
    {
        dd(str_random(24));
        // 返回字符的哥字符的ascii 码
        $sLast = ord(substr($string, -1)); // 96

        $slastc = chr($sLast); // "`"
        $pCheck = substr($string, -$sLast); // b"ô¤ÝtQw5WYŽh´C\x00Ò`"
        if (preg_match("/$slastc{" . $sLast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $sLast);
            return $string;
        } else {
            return false;
        }
    }

    public function testClone()
    {
        $order = Order::find(12);

        $cloneOrder = clone $order;
        // $order->no = 1231;
        $this->copyClone($cloneOrder);
        dd($cloneOrder);

    }

    public function copyClone($order)
    {
        // $order->no = 000;
        dd($order->no);
    }

    public function testSwitch($name, $age)
    {
        switch ($name) {
            case 'zhou':
                echo 'my name is zhou';
                break;
            case 'hang':
                switch ($age) {
                    case 10:
                        $age = 'ten';
                        break;
                    case 20:
                        $age = 'twity';
                        break;
                }
                echo 'my name is '. $name .' and my age is '. $age;
                break;
            default:
                echo 'i have no name';
                break;
        }
    }

    // 下载代练妈妈接口游戏区服信息
    public function getDailianmamaInfo()
    {
        $options = '';
        $url = 'static.dailianmama.com/tool/dlmm/gameinfo.html';
        $method = 'GET';
        $client = new Client;
        $response = $client->request($method, $url, [
            'query' => $options,
        ]);

        $res =  $response->getBody()->getContents();

        $arr = json_decode($res, true);

        // 英雄联盟 1 绝地8, 7枪战, 6球球, 5决战, 4刺激, 3全军, 2QQ手游9, 守望, 0王者
        dd($arr[0]);
        // dd($arr[1]['list']);

        // 区
        $thirdAreas = [];
        foreach ($arr[1]['list'] as $k => $area) {
            if ($k == 0) {
                continue;
            }
            foreach ($area['list'] as $key => $server) {
                if ($key == 0) {
                    continue;
                }
                $thirdAreas[$area['name']][$server['id']] = $server['name']; 
            }
            // $thirdAreas[$area['name']] = $area['list'];
        }
dd($thirdAreas);
        $thirdAreas = [];
        $thirdServers = [];
        $thirdGames = [];

        foreach ($arr[1]['list'] as $key => $data) {
            $thirdAreas['third_area_name'][$key] = $data['name'];
            $thirdAreas['third_area_id'][$key] = $data['id'];
            $thirdAreas['third_game_id'][$key] = $data['gameid'];

            foreach ($arr[6]['list'][$key]['list'] as $key => $server) {
                $thirdServers[$data['name']][$key] = $server['name'];
            }

        }
        // dd($thirdServers);
    }


    public function addPrice()
    {
        $orderData = "bbuz/0T/HzAQ+ZFWscOApvltQfvUMpoL7wSrMFrMLjJiNXFvNuj6cllqppyUDNXGe0lKegpao60brOgElD2Ipcosvhn98h/WlLpUjMp3FgMlxDCsiQk65iC3PjHnrOA99JdsQLCFTS4jvgdc47bMLaO+KkMbCiBle/WNbHe9DX64Ta4vpowcdDKESaf2LlDh8Y5cRUJldcKMbcukhjtmVVC/WOb9LPXVDgJB3doLdFJGa+dO32VUgtuETFsBcPgLwkcN+AWQDa/LH7RBNsQDQhRfOng+MQ36NOuzSu2mxtuNcgOJ/Nk11wKteYM2Lg52nO2m3dsJGolNgwbCfP+dAu0Gsq5WoXExqVE0hXfzBqbbdCPcj+ivaFwWZYy+DiUlQQmajkzoVtxMALOIaILIvu98aq8ADYgAlaOWn9/WU4njwpGJbzyveCQQvcneDvR0aOO4dn79TnSLC3mIEcUGLBcbtdcknBufPj1squoL9qO31h7Y31OyKA8CJ4eVFOn/jqodGXHYH54JOgJP4lTumd1oB1c3o1D6UsmePyL4SjNp6oXpFaSMQSrqgfhi63MestueZVxK2rXu7+PcsS8O9LGqjGLF3HpC+U9wF13j7auTZoOOZByVgy62wJoTZD3kd2x1k7BFhyl6ZpupB908EF0cp7RibpK6PyyJfW0IItpKw03dCSeElbzOnhAX+c/LzGppuhC+772F1HJODMNy0arworr40zz2CQ1JRiouXo8EmoMUdLhWDjXhmtQzAC+wPu6aGztiz9yv/g/D6M8gC98QZdWXEtGK26tbdRkGy1wxh/PBXdU/0NqIXZ1ItbBiCoz7bTvUCBuXbglUYzakavp/Bx3NH0s69az6BQRCdKRborhA52/hzkxW/FZ5FUXrpu8yDwnS4dlPhDogx4gDLhKChOZtRgdbnj9iXXBm/kc=";

        $orderData = "bbuz/0T/HzAQ+ZFWscOAptxkzmRpi09H+diCrtfMwYXkYY/HH7W9Qn6rjkch9HJpqg+HSC2Ev7+G+NLbYqtsoCXma75x9limwLicLNpazx+L/1M0FJL9fHCn4Ft//l+QlNKpdYkWlm1WBBOBWUHfaueYB7qUpBhG7STpWt8YxeuiELLtO+396/6eevSc2VkjFCCOVHPyRN+sZ8cwS0gWiLj+4jrAPReohdrezUEc13pti0xCL0WI9sbwRQgYYTkigFG4J8LAWJJypqJ953pm3GywMN7S4XyZtBM5RReOJQEWxQ5MMUB6vlBE8JiWXADKQBCiz8jclAdWpACecJPc4izrkGmOOJCU3JaoA7ySc9vkqZZYC7mUop9T3rjEyu3SVvS44OSPpkf7JmBaG3nLRXhRg5h2pWh5/yO+CJTUzqPomqG6i9ChSi0fX3q7KtPZFsP9vUPYMn3ouyKbDLARz3Q3FU2WBkdEMEay0VeltE2fD9KgYS6ob33bqqfFB6lvw5CGYJY9SxVLcghRGvlw6BCiyX2dcQmCYOWGgoj+rZ++Oxapq0AsWywrjmOdtyFa4E8TJIIxkMTbXzJU2GGuv/Ax9HGZReNrItRNGhJyqbUBQWsIGpI6B/uK2IMfusbbUy+F1m9L8jhqORP3q0l7KOXFCOkFhcsaD2Q1DbS6pVcXFUVo6itkBDnS5RH6ecDKcaK3WlCwNZcbuJx3QoyttqQYJ4q4YSl9SXL2GjUWooubQ6mPcuVpukA+3I2tm4+tlMXgnZqm+2RRbpXAhPswjBU1k1Hd6l1RsQXJrxmVJQrddyK5cFzTKER3wozQ0Qx/S+kNMgz+HkV7gyi2B60slayZWY7c7nz9UOqxBjWhQQ4n8cPpvhaSAKHNnemi5cZN";

        $decriptData = openssl_decrypt($orderData, 'aes-128-cbc', '45584685d8e4f5e8e4e2685', false, '1234567891111152');

        dd(json_decode($decriptData, true));
        // $orderNo = '2018041616101400000580';
        // $orderNo = '2018041616344300000016';
        // $order = OrderModel::where('no', $orderNo)->first();

        // $afterAddAmount = 5.13; // 增加之后的金额
        // $differPrice = 0.03; //差值

        // Asset::handle(new Expend($differPrice, 7, $orderNo, '代练改价支出', 8317));

        // $order->price = $afterAddAmount;
        // $order->amount = $afterAddAmount;
        // $order->save();

        // OrderDetail::where('order_no', $orderNo)->where('field_name', 'game_leveling_amount')->update([
        //     'field_value' => $afterAddAmount
        // ]);
    }
}
