<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

/**
 * Class OrderReportController
 * @package App\Http\Controllers\Frontend\Finance
 */
class OrderReportController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $tradeSubType = $request->trade_sub_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $tradeSubType,$timeStart, $timeEnd);
        return view('frontend.v1.finance.amount-flow.index', compact('dataList', 'tradeNo', 'tradeType', 'tradeSubType', 'timeStart', 'timeEnd'));
    }
}