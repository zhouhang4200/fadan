<?php

namespace App\Http\Controllers\Backend;

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

    	$filters = compact('startDate', 'endDate', 'source', 'status', 'serviceId', 'gameId', 'creatorPrimaryUserId',
            'gainerPrimaryUserId', 'no', 'foreignOrderNo');

        $orders = Order::filter($filters)->latest('created_at')->paginate(config('backend.page'));

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
        return response()->json(View::make('backend.order', [
            'content' => Order::with('detail')->find($request->id),
        ])->render());
    }

    /**
     * 订单操作记录
     * @param Request $request
     */
    public function record(Request $request)
    {

    }
}
