<?php

namespace App\Http\Controllers\Frontend\Finance;

use App\Models\TaobaoTrade;
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
     *
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param GameRepository $gameRepository
     * @return $this|\Illuminate\Http\RedirectResponse
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


        // 处理数据
        $tid = [];
        $taobaoTradeData = [];
        foreach($orders as $item) {
            $detail = $item->detail->pluck('field_value', 'field_name')->toArray();
            $tid[] = $detail['source_order_no'];
            $tid[] = $detail['source_order_no_1'] ?? '';
            $tid[] = $detail['source_order_no_2'] ?? '';
        }

        $taobaoTrade = TaobaoTrade::select('tid', 'payment', 'trade_status')->whereIn('tid', array_unique(array_filter($tid)))->get();

        if ($taobaoTrade) {
            foreach ($taobaoTrade as $trade) {
                $taobaoTradeData[$trade->tid] = [
                    'payment' => $trade->payment,
                    'refund' => $trade->trade_status == 7 ? $trade->payment : 0,
                ];
            }
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
            'taobaoTradeData' => $taobaoTradeData,
            'fullUrl' => $request->fullUrl(),
        ]);
    }

    /**
     * 财务订单列表导出
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param GameRepository $gameRepository
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
        $pageSize = $request->input('limit', 10);

        $taobaoStatus = $request->input('taobao_status', 0);
        $platform = $request->input('platform', 0);
        $sellerNick = $request->input('seller_nick', '');

        $game = $gameRepository->availableByServiceId(4);

        $orders = $orderRepository->levelingDataListExport($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $sellerNick, $pageSize);

        export([
            '内部单号',
            '淘宝单号',
            '补款单号1',
            '补款单号2',
            '游戏',
            '订单状态',
            '店铺名称',
            '接单平台',
            '接单平台单号',
            '淘宝金额',
            '淘宝退款',
            '支付金额',
            '获得金额',
            '手续费',
            '最终支付金额',
            '发单客服',
            '淘宝下单时间',
            '结算时间',
        ], '财务订单导出', $orders, function ($orders, $out){
            $orders->chunk(100, function ($chunkOrders) use ($out) {
                foreach ($chunkOrders as $item) {
                    $detail = $item->detail->pluck('field_value', 'field_name')->toArray();

                    $taobaoAmout = 0; // 淘宝金额取值:所有淘宝订单总支付金额
                    $taobaoRefund = 0; // 淘宝退款:所有淘宝订单已退款状态的支付金额
                    $paymentAmount = 0; // 支付金额: 订单总金额 或 仲裁结果需支付的金额
                    $getAmount = 0; // 获得金额: 仲裁结果需支付的金额
                    $poundage = 0; // 手续费: 只有在已仲裁 已撤销 才有值
                    $profit = 0; // 利润

                    // 已仲裁 已撤销状态时 取接口的传值 否则取订单的支付金额
                    if (in_array($item->status, [19, 21])  && isset($item->levelingConsult->api_amount) && $item->levelingConsult->complete != 0) {
                        $paymentAmount = $item->levelingConsult->complete = 1 ? $item->levelingConsult->amount :  $item->levelingConsult->api_amount;
                        $getAmount = $item->levelingConsult->complete = 1 ? $item->levelingConsult->deposit :  $item->levelingConsult->api_deposit;
                        $poundage = $item->levelingConsult->api_service;
                    } else if ($item->status == 20) {
                        $paymentAmount = $item->amount;
                    } else if ($item->status == 23) {
                        $paymentAmount = 0;
                    }
                    if (!empty($detail['source_order_no'])) {
                        // 如果不是重新下的单则计算淘宝总金额与淘宝退款总金额与利润
                        if (!isset($detail['is_repeat'])  || (isset($detail['is_repeat']) && ! $detail['is_repeat'] )) {

                            $tid = [
                                $detail['source_order_no'],
                                $detail['source_order_no_1'] ?? '',
                                $detail['source_order_no_2'] ?? '',
                            ];
                            $taobaoTrade = \App\Models\TaobaoTrade::select('tid', 'payment', 'trade_status')->whereIn('tid', array_unique(array_filter($tid)))->get();

                            if ($taobaoTrade) {
                                foreach ($taobaoTrade as $trade) {
                                    if ($trade->trade_status == 7) {
                                        $taobaoRefund = bcadd($trade->payment, $taobaoRefund, 2);
                                    }
                                    $taobaoAmout = bcadd($trade->payment, $taobaoAmout, 2);
                                }
                            }
                        }
                    }
                    // 计算利润
                    $profit =   ($getAmount  - $paymentAmount  - $poundage) + 0;

                    $sourceNo = '';
                    $sourceNo1 = '';
                    $sourceNo2 = '';
                    if (isset($detail['source_order_no']) && !empty($detail['source_order_no'])) {
                        $sourceNo = $detail['source_order_no'];
                    }
                    if (isset($detail['source_order_no_1']) && !empty($detail['source_order_no_1'])) {
                        $sourceNo1 = $detail['source_order_no_1'];
                    }
                    if (isset($detail['source_order_no_2']) && !empty($detail['source_order_no_2'])) {
                        $sourceNo2 = $detail['source_order_no_2'];
                    }

                    $third = '-';
                    $thirdOrderNo = '-';
                    if(isset($detail['third']) && $detail['third'] && isset(config('partner.platform')[(int)$detail['third']])){
                        $third = config('partner.platform')[(int)$detail['third']]['name'];
                        $thirdOrderNo =  $detail['third_order_no'];
                    }

                    $data = [
                        $item->no . "\t",
                        $sourceNo . "\t",
                        $sourceNo1 . "\t",
                        $sourceNo2 . "\t",
                        $item->game_name,
                        isset(config('order.status_leveling')[$item->status]) ? config('order.status_leveling')[$item->status] : '',
                        $detail['seller_nick'] ?? '-',
                        $third,
                        $thirdOrderNo,
                        (string)$taobaoAmout,
                        (string)$taobaoRefund,
                        (string)$paymentAmount,
                        (string)$getAmount,
                        (string)$poundage,
                        (string)$profit,
                        $detail['customer_service_name'] ?? '-',
                        $item->taobaoTrade->created ?? '-',
                        $item->updated_at,
                    ];
                    fputcsv($out, $data);
                }
            });
        });
    }
}