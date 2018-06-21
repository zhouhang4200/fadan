<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\ProcessOrderRepository;

class ProcessOrderController extends Controller
{
    public function index(Request $request)
    {
        $dataList = ProcessOrderRepository::getList($request->order_no, $request->user_id);

        return view('backend.finance.process-order.index', compact('dataList'));
    }
}
