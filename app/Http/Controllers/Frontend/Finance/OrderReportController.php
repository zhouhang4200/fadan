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
}