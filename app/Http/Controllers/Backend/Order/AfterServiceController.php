<?php

namespace App\Http\Controllers\Backend\Order;

use Auth, View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\PunishOrRewardRepository;

/**
 * 订单售后处理
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class AfterServiceController extends Controller
{
    /**
     * 订单退款
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $orderNo = $request->order_no;
        $status = $request->status;
        $orderCreatorUserId = $request->order_creator_user_id;

        $orders = PunishOrRewardRepository::orderRefundList();

        return view('backend.order.after-service.index')->with([
            'orders' => $orders,
            'orderNo' => $orderNo,
            'status' => $status,
            'orderCreatorUserId' => $orderCreatorUserId,
        ]);
    }
}
