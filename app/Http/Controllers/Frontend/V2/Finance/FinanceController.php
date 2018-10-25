<?php

namespace App\Http\Controllers\Frontend\V2\Finance;

use App\Models\UserAsset;
use Illuminate\Http\Request;
use App\Models\UserAmountFlow;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    /**
     * 资金流水
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function amountFlow()
    {
        return view('frontend.v2.finance.amount-flow');
    }

    /**
     * 新资金流水接口数据
     * @param Request $request
     * @return mixed
     */
    public function amountFlowDataList(Request $request)
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

    /**
     * 我的资产
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myAsset()
    {
        return view('frontend.v2.finance.my-asset');
    }

    /**
     * 我的资产接口数据
     * @return mixed
     */
    public function myAssetDataList()
    {
        $userAsset = UserAsset::where('user_id', Auth::user()->getPrimaryInfo()->id)->first();

        return json_encode([
            [
                'name' => '账户余额',
                'amount' => $userAsset->balance,
            ],
            [
                'name' => '冻结金额',
                'amount' => $userAsset->frozen,
            ],
            [
                'name' => '累计加款',
                'amount' => $userAsset->total_recharge,
            ],
            [
                'name' => '累计提现',
                'amount' => $userAsset->total_withdraw,
            ],
            [
                'name' => '累计收入',
                'amount' => $userAsset->total_income,
            ],
            [
                'name' => '累计支出',
                'amount' => $userAsset->total_expend,
            ],
        ]);
    }
}
