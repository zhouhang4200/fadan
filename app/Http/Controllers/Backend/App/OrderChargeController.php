<?php

namespace App\Http\Controllers\Backend\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderChargeController extends Controller
{
    public function index(Request $request)
    {
        $orderNo = $request->order_no;
        $dataList = [];

        return view('backend.app.order-charge.index', compact('dataList', 'orderNo'));
    }

    public function detail(id)
    {
        # code...
    }
}
