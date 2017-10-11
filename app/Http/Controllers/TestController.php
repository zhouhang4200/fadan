<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Asset;
use App\Extensions\Asset\Recharge;
use App\Extensions\Asset\Withdraw;
use App\Extensions\Asset\Freeze;
use App\Extensions\Asset\Unfreeze;
use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Refund;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;

class TestController extends Controller
{
    public function index()
    {
        // Asset::handle(new Recharge(100, Recharge::TRADE_SUBTYPE_AUTO, '2017101' . rand(1000, 9999), '自动充值'));
        // Asset::handle(new Freeze(80, Freeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '提现冻结'));
        // Asset::handle(new Withdraw(50, Withdraw::TRADE_SUBTYPE_MANUAL, '2017101' . rand(1000, 9999), '提现成功'));
        // Asset::handle(new Unfreeze(10, Unfreeze::TRADE_SUBTYPE_WITHDRAW, '2017101' . rand(1000, 9999), '解冻成功'));
        // Asset::handle(new Consume(10, Consume::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '消费手续费'));
        // Asset::handle(new Refund(10, Refund::TRADE_SUBTYPE_BROKERAGE, '2017101' . rand(1000, 9999), '手续费退款'));
        // Asset::handle(new Expend(10, Expend::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '下订单'));
        // Asset::handle(new Income(10, Income::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '接单发货'));

        dump('ok');
    }
}
