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

class TestController extends Controller
{
    public function index()
    {
        Asset::handle(new Recharge(Auth::user()->id, 1000, Recharge::TRADE_SUBTYPE_AUTO, '2017101' . rand(1000, 9999), '自动充值'));
        Asset::handle(new Freeze(Auth::user()->id, 850, Freeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '提现冻结'));
        Asset::handle(new Withdraw(Auth::user()->id, 550, Withdraw::TRADE_SUBTYPE_MANUAL, '2017101' . rand(1000, 9999), '提现成功'));
        Asset::handle(new Unfreeze(Auth::user()->id, 310, Unfreeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '解冻成功'));
        Asset::handle(new Consume(Auth::user()->id, 220, Consume::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '消费手续费'));
        Asset::handle(new Refund(Auth::user()->id, 150, Refund::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '手续费退款'));
        Asset::handle(new Expend(Auth::user()->id, 70, Expend::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '下订单'));
        Asset::handle(new Income(Auth::user()->id, 40, Income::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '接单发货'));

        // 对账
        $platformAsset = PlatformAsset::first();
        $external = "$platformAsset->total_recharge - $platformAsset->total_withdraw = ";
        $external .= $platformAsset->total_recharge - $platformAsset->total_withdraw;

        $interior = "$platformAsset->amount + $platformAsset->managed + $platformAsset->balance + $platformAsset->frozen = ";
        $interior .= $platformAsset->amount + $platformAsset->managed + $platformAsset->balance + $platformAsset->frozen;
        dump($external, $interior);
    }
}
