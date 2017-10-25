<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Frontend\UserWithdrawOrderRepository;

class WithdrawOrderController extends Controller
{
    public function index(Request $request, UserWithdrawOrderRepository $repository)
    {
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;
        $status    = $request->status;

        $dataList = $repository->getList($timeStart, $timeEnd, $status);

        return view('frontend.finance.withdraw.index', compact('dataList', 'timeStart', 'timeEnd', 'status'));
    }
}
