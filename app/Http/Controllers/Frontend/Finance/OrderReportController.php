<?php

namespace App\Http\Controllers\Frontend\Finance;

use App\Repositories\Frontend\OrderRepository;
use App\Repositories\Frontend\GameRepository;
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
     * @param OrderRepository $orderRepository
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, OrderRepository $orderRepository, GameRepository $gameRepository)
    {
        $no = $request->input('no');
        $customerServiceName = $request->input('customer_service_name', 0);
        $gameId = $request->input('game_id', 0);
        $status = $request->input('status', 0);
        $wangWang = $request->input('wang_wang');
        $urgentOrder = $request->input('urgent_order', 0);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $pageSize = $request->input('limit', 15);

        $taobaoStatus = $request->input('taobao_status', 0);
        $platform = $request->input('platform', 0);
        $sellerNick = $request->input('seller_nick', '');

        $game = $gameRepository->availableByServiceId(4);

        if ($request->export) {

            $options = compact('no', 'foreignOrderNo', 'gameId', 'status', 'wangWang', 'urgentOrder', 'startDate', 'endDate');

            return redirect(route('frontend.workbench.leveling.excel'))->with(['options' => $options]);
        }

        $orders = $orderRepository->levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $sellerNick, $pageSize);

        if (!in_array($status, array_flip(config('order.status_leveling')))) {
            return response()->ajax(0, '不存在的类型');
        }

        return view('frontend.v1.finance.order-report.index')->with([
            'orders' => $orders,
            'game' => $game,
            'no' => $no,
            'customerServiceName' => $customerServiceName,
            'gameId' => $gameId,
            'status' => $status,
            'taobaoStatus' => $taobaoStatus,
            'wangWang' => $wangWang,
            'platform' => $platform,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sellerNick' => $sellerNick,
            'fullUrl' => $request->fullUrl(),
        ]);
    }

    /**
     * 财务订单列表导出
     * @param Request $request
     */
    public function export(Request $request, OrderRepository $orderRepository, GameRepository $gameRepository)
    {
        $no = $request->input('no');
        $customerServiceName = $request->input('customer_service_name', 0);
        $gameId = $request->input('game_id', 0);
        $status = $request->input('status', 0);
        $wangWang = $request->input('wang_wang');
        $urgentOrder = $request->input('urgent_order', 0);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $pageSize = $request->input('limit', 15);

        $taobaoStatus = $request->input('taobao_status', 0);
        $platform = $request->input('platform', 0);
        $sellerNick = $request->input('seller_nick', '');

        $game = $gameRepository->availableByServiceId(4);

        $orders = $orderRepository->levelingDataListExport($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $sellerNick, $pageSize);

        export([
            '内部单号',
            '淘宝单号',
            '游戏',
            '订单状态',
            '店铺名称',
            '接单平台',
            '淘宝金额',
            '接单价格',
            '最终利润',
            '淘宝下单时间',
            '结算时间',
        ], '财务订单导出', $orders, function ($orders, $out){
            $orders->chunk(1000, function ($chunkOrders) use ($out) {
                foreach ($chunkOrders as $order) {
                    $detail = $order->detail->pluck('field_value', 'field_name')->toArray();
                    $paymentAmount = '';
                    $getAmount = '';
                    $poundage = '';
                    $profit = '';
                    $amount = 0;
                    if (in_array($order->status, [19, 20, 21])){
                       // 支付金额
                        if (in_array($order->status, [21, 19])) {
                            $amount = $detail['leveling_consult']['api_amount'];
                        }
                        // 支付金额
                        $paymentAmount = $amount !=0 ?  $amount + 0:  $order->amount + 0;
                        $paymentAmount = (float)$paymentAmount + 0;
                        $getAmount = (float)$getAmount + 0;
                        $poundage = (float)$poundage + 0;
                        // 利润
                        $profit = ((float)$detail['source_price'] - $paymentAmount + $getAmount - $poundage) + 0;
                    }
                    $sourceNo = '';
                    $sourceNo1 = '';
                    $sourceNo2 = '';
                    if (!empty($detail['source_order_no'])) {
                        $sourceNo = '单号1：'.$detail['source_order_no'];
                    }
                    if (!empty($detail['source_order_no_1'])) {
                        $sourceNo1 = "\n单号2：".$detail['source_order_no_1'];
                    }
                    if (!empty($detail['source_order_no_2'])) {
                        $sourceNo2 = "\n单号3：".$detail['source_order_no_2'];
                    }
                    $data = [
                        $order->no . "\t",
                        $sourceNo.$sourceNo1.$sourceNo2,
                        $order->game_name,
                        isset(config('order.status_leveling')[$order->status]) ? config('order.status_leveling')[$order->status] : '',
                        $detail['seller_nick'] ?? '',
                        isset($detail['third']) && $detail['third'] ? config('partner.platform')[(int)$detail['third']]['name'] .'/'.$detail['third_order_no']: '',
                        (string)$detail['source_price'] ?? '',
                        (string)$paymentAmount,
                        (string)$profit,
                        $order->created_at,
                        $order->updated_at,
                    ];
                    fputcsv($out, $data);
                }
            });
        });
    }
}