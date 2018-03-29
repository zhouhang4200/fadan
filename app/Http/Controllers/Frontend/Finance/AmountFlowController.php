<?php
namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Frontend\UserAmountFlowRepository;
use App\Extensions\Excel\ExportFrontendUserAmountFlow;

/**
 * Class AmountFlowController
 * @package App\Http\Controllers\Frontend\Finance
 */
class AmountFlowController extends Controller
{
    /**
     * @param Request $request
     * @param UserAmountFlowRepository $userAmountFlowRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, UserAmountFlowRepository $userAmountFlowRepository)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $tradeSubType = $request->trade_sub_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $tradeSubType,$timeStart, $timeEnd);
        return view('frontend.finance.amount-flow.index', compact('dataList', 'tradeNo', 'tradeType', 'tradeSubType', 'timeStart', 'timeEnd'));
    }

    public function export(Request $request, UserAmountFlowRepository $userAmountFlowRepository, ExportFrontendUserAmountFlow $excel)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $tradeSubType = $request->trade_sub_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $tradeSubType, $timeStart, $timeEnd, 0);

        $excel->export($dataList);
    }
}
