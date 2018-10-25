<?php

namespace App\Http\Controllers\Frontend\V2\Finance;

use Illuminate\Http\Request;
use App\Models\UserAmountFlow;
use App\Http\Controllers\Controller;

class AmountFlowController extends Controller
{
    /**
     * 资金流水
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('frontend.v2.finance.amount-flow.index');
    }

    /**
     * 新资金流水
     * @param Request $request
     * @return mixed
     */
    public function dataList(Request $request)
    {
        $tradeNo = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $tradeSubType = $request->trade_sub_type;
        $startDate = $request->date[0];
        $endDate = $request->date[1];
        $foreignOrderNo = $request->channel_order_trade_no;
        $filter = compact('tradeNo', 'tradeType', 'tradeSubType', 'startDate', 'endDate', 'foreignOrderNo');

        $dataList = UserAmountFlow::filter($filter)->with('order')->paginate(15);

        return $dataList;
    }
}
