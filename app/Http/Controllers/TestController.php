<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Asset;
use App\Extensions\Asset\Recharge;

class TestController extends Controller
{
    public function index()
    {
        $amount = 100;
        $subtype = Recharge::TRADE_SUBTYPE_AUTO;
        $number = 111;
        $remark = '备注';

        $recharge = new Recharge($amount, $subtype, $number, $remark);

        Asset::handle($recharge);

        dump('ok');
    }
}
