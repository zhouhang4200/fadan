<?php
namespace App\Http\Controllers\Frontend\Finance;

use App\Models\UserAmountFlow;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $foreignOrderNo = $request->foreign_order_no;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $tradeSubType,$timeStart, $timeEnd, $foreignOrderNo);

        return view('frontend.v1.finance.amount-flow.index', compact('dataList', 'tradeNo', 'tradeType', 'tradeSubType', 'timeStart', 'timeEnd', 'foreignOrderNo'));
    }

    /**
     * @param Request $request
     * @param UserAmountFlowRepository $userAmountFlowRepository
     * @param ExportFrontendUserAmountFlow $excel
     */
    public function export(Request $request, UserAmountFlowRepository $userAmountFlowRepository, ExportFrontendUserAmountFlow $excel)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $tradeSubType = $request->trade_sub_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;
        $foreignOrderNo = $request->foreign_order_no;


        $query = UserAmountFlow::where('user_id', Auth::user()->getPrimaryUserId())
            ->when(!empty($tradeNo), function ($query) use ($tradeNo) {
                return $query->where('trade_no', $tradeNo);
            })
            ->when(!empty($tradeType), function ($query) use ($tradeType) {
                if ($tradeType == 7) {
                    return $query->whereIn('trade_type', [5, 7]);
                } else if ($tradeType == 8) {
                    return $query->whereIn('trade_type', [6, 8]);
                } else {
                    return $query->where('trade_type', $tradeType);
                }
            })
            ->when(!empty($tradeSubType), function ($query) use ($tradeSubType) {
                return $query->where('trade_subtype', $tradeSubType);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd . ' 23:59:59');
            })
            ->when(!empty($foreignOrderNo), function ($query) use ($foreignOrderNo) {
                $orderNo = Order::where('foreign_order_no', $foreignOrderNo)->value('no') ?? '';
                return $query->where('trade_no', $orderNo);
            })
            ->with('order')
            ->orderBy('id', 'desc');

        export([
            '流水号',
            '说明',
            '类型',
            '变动金额',
            '账户余额',
            '订单号',
            '天猫单号',
            '时间',
        ], '财务订单导出', $query, function ($query, $out){
            $query->chunk(100, function ($chunkOrders) use ($out) {
                foreach ($chunkOrders as $item) {
                    $data = [
                        $item->id,
                        config('tradetype.user_sub')[$item->trade_subtype],
                        config('tradetype.user')[$item->trade_type],
                        $item->fee + 0,
                        $item->balance + 0,
                        $item->trade_no . "\t",
                        optional($item->order)->foreign_order_no . "\t",
                        $item->created_at
                    ];
                    fputcsv($out, $data);
                }
            });
        });
    }
}
