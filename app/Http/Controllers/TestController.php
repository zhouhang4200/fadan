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
        $order = Order::find(88);

        // $orderDetail = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
        // dd($orderDetail);
        // dd(AUth::user());
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
        dd($a);
        // dd($order);

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
}
