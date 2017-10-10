<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Asset;
use App\Extensions\Asset\Recharge;
use App\Extensions\Asset\Withdraw;

class TestController extends Controller
{
    public function index()
    {
        Asset::handle(new Recharge(100, Recharge::TRADE_SUBTYPE_AUTO, '2017101' . rand(1000, 9999), '备注' . rand(1000, 9999)));
        // Asset::handle(new Withdraw(-50, Recharge::TRADE_SUBTYPE_AUTO, '2017101' . rand(1000, 9999), '备注' . rand(1000, 9999)));

        dump('ok');
    }
}
