<?php

namespace App\Http\Controllers;

use App\Services\KamenOrderApi;
use App\Services\TmallOrderApi;
use Illuminate\Http\Request;

use Auth;
use Asset;
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

use App\Events\NotificationEvent;

use App\Extensions\Dailian\Controllers\DailianFactory;

class TestController extends Controller
{
    public function index(UserRechargeOrderRepository $repository)
    {
        $data = 'zhouhang';
        // 测试代练接口加密
        return $this->testEncrypt('abc', $data);

        // 测试代练接口解密
        return $this->testDecrypt();


        // 测试代练订单状态
        // $this->testDailianStatus();

        // $yestodayStart = Carbon::now()->subDays(1)->startOfDay()->toDateTimeString();
        // $yestodayEnd = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

        // $money = Order::select(\DB::raw('sum(amount) as total'))
        //         ->whereBetween('updated_at', [$yestodayStart, $yestodayEnd])
        //         ->where('status', 8)
        //         ->value('total');
        // dd($money);
        
        //  dd(TmallOrderApi::getOrder(105794,103747613994411922));
        // $time = '2017-11-09 16:32:50';
        // $carbon = Carbon::parse($time);
        // $bool = (new Carbon)->gte($carbon);
        // $bool2 = (new Carbon)->lte($carbon);
        // dd($bool2);
        // 
        // $bool = whoCanReceiveOrder(23, 1223, 1, 12);

        // if ($bool) {
        //     dd('yes');
        // } else {
        //     dd('no');
        // }
        // $order = OrderModel::find(2);

        // dd($order->foreignOrder);
        //
        // $foreignOrder = ForeignOrder::find(20);

        // dd($foreignOrder->order);

        // $order = OrderModel::find(19524);


        // dd($order->created_at);
        // $carbon = new Carbon;

        // $a = $carbon->diffInMinutes($order->created_at);
        // dd($a);
        // $this->testAsset();
        // $this->testDaily();
        // $this->testOrder();
        // $this->command();
        //
        // $app = new AppController;

        // $data = $app->run('version', ['game_id' => 151]);

        // dd($data);

        // $repository->store(1000, 28, '加款1000快', 'taobao-123', 'wangwang-123');

        // event(new NotificationEvent('orderRefund', ['amount' => '500.00', 'user_id' => 3]));
        // exit("1234");

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

    public function testDailianStatus()
    {
        // $order = Order::find(88);
        // $orderDetail = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
        // 完成: 订单号 操作人
        // $a = DailianFactory::choose('complete')->run('2017121818232200000003', '27'); //bool
        // 删除: 订单号 操作人
        // $a = DailianFactory::choose('delete')->run('2017121818232200000003', '27'); //bool
        // 上架：订单号 操作人
        // $a = DailianFactory::choose('onSale')->run('2017121818232200000003', '27'); //bool
        // 下架：订单号 操作人
        // $a = DailianFactory::choose('offSale')->run('2017121818232200000003', '27'); //bool
        // 锁定： 订单号 操作人
        // $a = DailianFactory::choose('lock')->run('2017121818232200000003', '27'); //bool
        // 取消锁定: 订单号 操作人
        // $a = DailianFactory::choose('cancelLock')->run('2017121818232200000003', '27'); //bool
        // 撤销：订单号 操作人
        // $a = DailianFactory::choose('revoke')->run('2017121818232200000003', '27'); //bool
        // 同意撤销 订单号 操作人 协商代练费 回传双金费 回传手续费 协商双金
        // $a = DailianFactory::choose('agreeRevoke')->run('2017121818232200000003', '27', 20, 2, 2, 4); //bool
        // 取消撤销:订单号 操作人
        // $a = DailianFactory::choose('cancelRevoke')->run('2017121818232200000003', '27'); //bool
        // 强制撤销：订单号 操作人
        // $a = DailianFactory::choose('forceRevoke')->run('2017121818232200000003', '27'); //bool
        // 申请仲裁 ：订单号 操作人
        // $a = DailianFactory::choose('applayArbitration')->run('2017121818232200000003', '27'); //bool
        // 取消仲裁：订单号 操作人
        // $a = DailianFactory::choose('cancelArbitration')->run('2017121818232200000003', '27'); //bool
        // 已仲裁 ：订单号 操作人 回传代练费 回传双金费 回传手续费
        // $a = DailianFactory::choose('arbitration')->run('2017121818232200000003', '27', 18, 10, 2); //bool
        // dd($order);
    }

    public function testEncrypt($key, $data)
    {
        $key = hash('sha256', $key, true); // 十六进制
        $iv = $this->hexToStr('00000000000000000000000000000000'); // \x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00
        $privateKey = '-----BEGIN PRIVATE KEY-----
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
-----END PRIVATE KEY-----'; // 私钥
        // 0984752eb763b64cbc3c9d1707dbbb83f13af4d010e9028f8047e1a833304ac10d4c7f7405586b60206d29ebc20491ed844d0553043be530117cf8c13625c25ab1910cedc33018b70064a8c59b9cd8b73c87b37e1abe54d22072afe871c5e8540e74e89f03588b186e812af63d58f92d150ba1ba49b4e5019463a550412c3310
        $sendKey =  openssl_private_encrypt($key, $encrypted, $privateKey) ? bin2hex($encrypted) : null;

        //  函数把包含数据的二进制字符串转换为十六进制值
        $data = bin2hex(openssl_encrypt($data, 'aes-256-cbc', $sendKey, OPENSSL_RAW_DATA, $iv)); // 2935d78c2993d1af3e449451265ddc09
        // $endKey = base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv)); // xzBjhhtT4KLQEg8vAAPc3A==
        // $endKey = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv); // Ç0c†\eSà¢Ð\x12\x0F/\x00\x03ÜÜ
        return ['key' => $sendKey, 'data' => $data];   
    }

     /**
     * @param $hex
     * @return string
     */
    private function hexToStr($hex)
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    public function testDecrypt($sendKey, $data)
    {
        $sendKey = '0984752eb763b64cbc3c9d1707dbbb83f13af4d010e9028f8047e1a833304ac10d4c7f7405586b60206d29ebc20491ed844d0553043be530117cf8c13625c25ab1910cedc33018b70064a8c59b9cd8b73c87b37e1abe54d22072afe871c5e8540e74e89f03588b186e812af63d58f92d150ba1ba49b4e5019463a550412c3310';
        $data = '2935d78c2993d1af3e449451265ddc09';

        
    }
}
