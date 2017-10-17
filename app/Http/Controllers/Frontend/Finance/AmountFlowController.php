<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Frontend\UserAmountFlowRepository;
use App\Extensions\Excel\ExportFrontendUserAmountFlow;

class AmountFlowController extends Controller
{
    public function index(Request $request, UserAmountFlowRepository $userAmountFlowRepository)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $timeStart, $timeEnd);
        return view('frontend.finance.amount-flow.index', compact('dataList', 'tradeNo', 'tradeType', 'timeStart', 'timeEnd'));
    }

    public function export(Request $request, UserAmountFlowRepository $userAmountFlowRepository, ExportFrontendUserAmountFlow $excel)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $timeStart, $timeEnd, 0);

        $excel->export($dataList);
    }
}
