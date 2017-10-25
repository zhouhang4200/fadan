<?php

namespace App\Http\Controllers;

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
use Order;
use App\Extensions\Order\Operations\Create;
use App\Extensions\Order\Operations\GrabClose;
use App\Extensions\Order\Operations\Receiving;
use App\Extensions\Order\Operations\Delivery;
use App\Extensions\Order\Operations\DeliveryFailure;
use App\Extensions\Order\Operations\AskForAfterService;
use App\Extensions\Order\Operations\AfterServiceComplete;
use App\Extensions\Order\Operations\TurnBack;

use App\Repositories\Frontend\WithdrawListRepository;

use Artisan;

class TestController extends Controller
{
    public function index(WithdrawListRepository $repository)
    {
        // $this->testAsset();
        // $this->testDaily();
        // $this->testOrder();
        // $this->command();

        $repository->apply(1234, '没钱了取点钱');
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

    public function testDaily()
    {
        $platformAssetDailyRepository->scriptrun('2017-10-18', '2017-10-21');
    }

    public function testOrder()
    {
        Order::handle(new Create(1, 'taobao-123', 1, 2, 0, 12, ['account' => 'buer2202@163.com', 'version' => '1.0', 'region' => '微信71区']));
        // Order::handle(new GrabClose('2017102414284300000014', 1));
        // Order::handle(new Receiving('2017102414284300000014', 1));
        // Order::handle(new Delivery('2017102316360000000021', 1));
        // Order::handle(new DeliveryFailure('2017102316531000000022', 1));
        // Order::handle(new AskForAfterService('2017102316360000000021', 1));
        // Order::handle(new AfterServiceComplete('2017102316360000000021', 1));
        // Order::handle(new TurnBack('2017102414284300000014', 2));

        $arr = \App\Models\OrderHistory::orderBy('id', 'desc')->first();
        dump(unserialize($arr->before), unserialize($arr->after));
    }

    public function command()
    {
        $exitCode = Artisan::call('migrate');
    }
}
