<?php

namespace App\Http\Controllers\Frontend\Finance;


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
        $orders = MonthSettlementOrders::paginate(30);

        return view('frontend.v1.finance.month-settlement-orders.index')->with([
            'accountType' => auth()->user()->leveling_type,
            'orders' => $orders,
            'game' => $gameRepository->availableByServiceId(4),
        ]);
    }

    /**
     * 数据导出
     * @param Request $request
     */
    public function export(Request $request )
    {

    }
}