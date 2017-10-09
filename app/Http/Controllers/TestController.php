<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Amount;
use App\Extensions\Amount\Recharge;

class TestController extends Controller
{
    public function index()
    {
        $amount = 100;
        $type = 1;
        $subtype = 1;
        $number = 111;
        $remark = '备注';
        $userId = 2841;

        $recharge = new Recharge($amount, $type, $subtype, $number, $remark, $userId);

        Amount::handle($recharge);
    }
}
