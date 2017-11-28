<?php

namespace App\Http\Controllers\Backend\Order;

use App\Repositories\Backend\ForeignOrderRepository;
use App\Repositories\Backend\OrderRepository;
use Auth, View;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Backend\GameRepository;
use App\Repositories\Backend\ServiceRepository;

/**
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class ForeignController extends Controller
{
    protected  $order;

    /**
     * OrderController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->order = $orderRepository;
    }

    /**
     * 外部订单
     * @param Request $request
     * @param ForeignOrderRepository $foreignOrderRepository
     * @return mixed
     */
    public function index(Request $request, ForeignOrderRepository $foreignOrderRepository)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));

        $orders = $foreignOrderRepository->dataList(
            $startDate,
            $request->end_date,
            $request->source_id,
            $request->channel_name,
            $request->kamen_order_no,
            $request->foreign_goods_id,
            $request->foreign_order_no,
            $request->wang_wang
            );

        return view('backend.order.foreign.index')->with([
            'orders' => $orders,
            'channel' => \DB::table('site_info')->pluck('name', 'id')->toArray(),
            'startDate' => $startDate,
            'endDate' => $request->end_date,
            'sourceId' => $request->source_id,
            'channelName' => $request->channel_name,
            'kamenOrderNo' => $request->kamen_order_no,
            'foreignGoodsId' => $request->foreign_goods_id,
            'foreignOrderNo' => $request->foreign_order_no,
            'wangWang' => $request->wang_wang,
        ]);
    }
}
