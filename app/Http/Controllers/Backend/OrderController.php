<?php

namespace App\Http\Controllers\Backend;

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
class OrderController extends Controller
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
     * @param Request $request
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
    	$startDate = $request->input('start_date', date('Y-m-d'));
    	$endDate = $request->input('end_date');
    	$source = $request->input('source');
    	$status = $request->input('status');
    	$serviceId = $request->input('service_id');
    	$gameId = $request->input('game_id');
        $creatorPrimaryUserId = $request->input('creator_primary_user_id');
    	$gainerPrimaryUserId= $request->input('gainer_primary_user_id');
        $no = $request->input('no');
        $foreignOrderNo = $request->input('foreign_order_no');
        $export = $request->input('export', 0);

    	$filters = compact('startDate', 'endDate', 'source', 'status', 'serviceId', 'gameId', 'creatorPrimaryUserId',
            'gainerPrimaryUserId', 'no', 'foreignOrderNo');

        // 订单导出
        if ($export) {
            return $this->order->export($filters);
        }
        // 订单列表
        $orders = $this->order->dataList($filters);

        return view('backend.order.index')->with([
            'orders' => $orders,
            'services' => $serviceRepository->available(),
            'games' => $gameRepository->available(),

            'startDate' => $startDate,
            'endDate' => $endDate,
            'source' => $source,
            'status' => $status,
            'serviceId' => $serviceId,
            'gameId' => $gameId,
            'creatorPrimaryUserId' => $creatorPrimaryUserId,
            'gainerPrimaryUserId' => $gainerPrimaryUserId,
            'no' => $no,
            'foreignOrderNo' => $foreignOrderNo,
            'fullUrl' => $request->fullUrl()
        ]);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Order $order)
    {
        return view('backend.order.show');
    }

    /**
     * 查看订单内容
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function content(Request $request)
    {
        return response()->json(View::make('backend.order.partials.order-content', [
            'content' => Order::with('detail')->find($request->id),
        ])->render());
    }

    /**
     * 订单操作记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function record(Request $request)
    {
        return response()->json(View::make('backend.order.partials.order-record', [
            'record' => Order::with(['history', 'creatorUser', 'gainerUser'])->find($request->id),
        ])->render());
    }

    /**
     * 外部订单
     * @param Request $request
     * @param ForeignOrderRepository $foreignOrderRepository
     * @return mixed
     */
    public function foreign(Request $request, ForeignOrderRepository $foreignOrderRepository)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));

        $orders = $foreignOrderRepository->dataList(
            $startDate,
            $request->end_date,
            $request->source_id,
            $request->channel_name,
            $request->kamen_order_no,
            $request->foreign_goods_id,
            $request->foreign_order_no
            );

        return view('backend.order.foreign')->with([
            'orders' => $orders,
            'channel' => \DB::table('site_info')->pluck('name', 'id')->toArray(),
            'startDate' => $startDate,
            'endDate' => $request->end_date,
            'sourceId' => $request->source_id,
            'channelName' => $request->channel_name,
            'kamenOrderNo' => $request->kamen_order_no,
            'foreignGoodsId' => $request->foreign_goods_id,
            'foreignOrderNo' => $request->foreign_order_no,
        ]);
    }
}
