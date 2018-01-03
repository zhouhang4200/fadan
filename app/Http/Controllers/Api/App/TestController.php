<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Api\App\OrderRepository;
use App\Models\Order;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $order = Order::where('no', '2017122917385300000011')->first();

        dump($order->orderCharge->orderChargeRecords);
    }
}
