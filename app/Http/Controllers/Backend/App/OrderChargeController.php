<?php

namespace App\Http\Controllers\Backend\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\OrderChargeRepository;

class OrderChargeController extends Controller
{
    public function index(Request $request)
    {
        $orderNo = $request->order_no;
        $status = $request->status;
        $dataList = OrderChargeRepository::dataList($orderNo, $status);

        $orderRechargeStatus = config('order.order_recharge_status');

        return view('backend.app.order-charge.index', compact('dataList', 'orderNo', 'status', 'orderRechargeStatus'));
    }

    public function detail($id)
    {
        $dataList = OrderChargeRepository::detail($id);

        return view('backend.app.order-charge.detail', compact('dataList'));
    }
}
