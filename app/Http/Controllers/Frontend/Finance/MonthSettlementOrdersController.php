<?php

namespace App\Http\Controllers\Frontend\Finance;


use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Facades\Asset;
use App\Extensions\Asset\Income;
use App\Http\Controllers\Controller;
use App\Models\MonthSettlementOrders;
use App\Repositories\Frontend\GameRepository;
use Illuminate\Http\Request;

/**
 * 月结订单
 * Class MonthSettlementOrdersController
 * @package App\Http\Controllers\Frontend\Finance
 */
class MonthSettlementOrdersController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return @view
     */
    public function index(Request $request, GameRepository $gameRepository)
    {
        $accountType =  auth()->user()->leveling_type;
        $currentPrimaryUserId =  auth()->user()->getPrimaryUserId();

        $creatorUser = [];
        $gainerUser = [];
        if ($accountType == 1) {
            // 所有发单人
            $creatorUser  = MonthSettlementOrders::where('gainer_primary_user_id', $currentPrimaryUserId)
                ->groupBy('creator_primary_user_id')
                ->pluck('creator_primary_user_id', 'creator_primary_user_name');
        } else {
            // 所有接单人
            $gainerUser = MonthSettlementOrders::where('creator_primary_user_id', $currentPrimaryUserId)
                ->groupBy('gainer_primary_user_id')
                ->pluck('gainer_primary_user_id', 'creator_primary_user_name');
        }

        $orders = MonthSettlementOrders::where(function ($query) use ($accountType, $currentPrimaryUserId){
            if ($accountType == 1) { // 接单方
                $query->where('gainer_primary_user_id', $currentPrimaryUserId);
            } else {
                $query->where('creator_primary_user_id', $currentPrimaryUserId);
            }
        })->filter([
            'no' => $request->no,
            'gameId' => $request->game_id,
            'finishTimeStart' => $request->time_start,
            'finishTimeEnd' => $request->time_end,
        ])->with(['game','order'])->paginate(30);

        return view('frontend.v1.finance.month-settlement-orders.index')->with([
            'accountType' => $accountType,
            'orders' => $orders,
            'game' => $gameRepository->availableByServiceId(4),
            'gainerUser' => $gainerUser,
            'creatorUser' => $creatorUser,
        ]);
    }

    /**
     * 数据导出
     * @param Request $request
     */
    public function export(Request $request )
    {
        $accountType =  auth()->user()->leveling_type;
        $currentPrimaryUserId =  auth()->user()->getPrimaryUserId();

        $orders = MonthSettlementOrders::where(function ($query) use ($accountType, $currentPrimaryUserId){
            if ($accountType == 1) { // 接单方
                $query->where('gainer_primary_user_id', $currentPrimaryUserId);
            } else {
                $query->where('creator_primary_user_id', $currentPrimaryUserId);
            }
        })->filter([
            'no' => $request->no,
            'gameId' => $request->game_id,
            'finishTimeStart' => $request->time_start,
            'finishTimeEnd' => $request->time_end,
        ]);

        export([
            '天猫订单号',
            '内部订单号',
            '游戏',
            '订单状态',
            '结账状态',
            $accountType == 1 ? '发单方' : '接单方',
            '最终支付金额',
            '代练结算时间',
            '结账时间',
        ], '财务订单导出', $orders, function ($orders, $out){
            $orders->chunk(100, function ($chunkOrders) use ($out) {
                $accountType =  auth()->user()->leveling_type;
                foreach ($chunkOrders as $item) {
                    $data = [
                        $item->foreign_order_no . "\t",
                        $item->order_no . "\t",
                        optional($item->game)->name,
                        config('order.status_leveling')[$item->order->status],
                        $item->statusText[$item->status],
                        $accountType == 1 ? $item->creator_primary_user_name . ' ID:' . $item->creator_primary_user_id
                            : $item->gainer_primary_user_name . ' ID:' . $item->gainer_primary_user_id,
                        $item->amount,
                        $item->finish_time,
                        $item->settlement_time,
                    ];
                    fputcsv($out, $data);
                }
            });
        });
    }

    /**
     * 结算
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function settlement(Request $request)
    {
        $accountType =  auth()->user()->leveling_type;
        $currentPrimaryUserId =  auth()->user()->getPrimaryUserId();

        if ($request->type == 1) {
            $orders = MonthSettlementOrders::select(\DB::raw('count(1) as count, sum(amount) as total'))->where(function ($query) use ($accountType, $currentPrimaryUserId){
                if ($accountType == 1) { // 接单方
                    $query->where('gainer_primary_user_id', $currentPrimaryUserId);
                } else {
                    $query->where('creator_primary_user_id', $currentPrimaryUserId);
                }
            })->where('status', 1)->filter([
                'no' => $request->no,
                'gameId' => $request->game_id,
                'finishTimeStart' => $request->time_start,
                'finishTimeEnd' => $request->time_end,
            ])->get()->toArray();

            return response()->ajax($orders[0]['count'] == 0 ? 0 : 1, 'success', ['count' =>  (int)$orders[0]['count'], 'total' => (int)$orders[0]['total']]);
        } else {
            \DB::beginTransaction();
            $orders = MonthSettlementOrders::where(function ($query) use ($accountType, $currentPrimaryUserId){
                if ($accountType == 1) { // 接单方
                    $query->where('gainer_primary_user_id', $currentPrimaryUserId);
                } else {
                    $query->where('creator_primary_user_id', $currentPrimaryUserId);
                }
            })->where('status', 1)->filter([
                'no' => $request->no,
                'gameId' => $request->game_id,
                'finishTimeStart' => $request->time_start,
                'finishTimeEnd' => $request->time_end,
            ])->lockForUpdate()->get();

            foreach ($orders as $order) {
                try {
                    Asset::handle(new Expend($order->amount, 6, $order->order_no, '代练支出', $order->creator_primary_user_id));
                    Asset::handle(new Income($order->amount, 12, $order->order_no, '代练订单完成收入', $order->gainer_primary_user_id));

                    $order->status = 2;
                    $order->settlement_time = date('Y-m-d H:i:s');
                    $order->save();
                } catch (\Exception $exception) {
                    return response()->ajax(0, $exception->getMessage());
                }
            }
            \DB::commit();
            return response()->ajax(1, '已结账');
        }
    }
}