<?php

namespace App\Http\Controllers\Backend\Order;

use Auth, View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\AfterService;
use App\Models\Order as OrderModel;
use App\Repositories\Backend\AfterServiceRepository;

/**
 * 订单售后处理
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class AfterServiceController extends Controller
{
    /**
     * @var AfterServiceRepository
     */
    protected  $afterServiceRepository;

    /**
     * AfterServiceController constructor.
     * @param AfterServiceRepository $afterServiceRepository
     */
    public function __construct(AfterServiceRepository $afterServiceRepository)
    {
        $this->afterServiceRepository = $afterServiceRepository;
    }

    /**
     * 售后订单
     * @param Request $request
     * @return mixed
     * @internal param AfterServiceRepository $afterServiceRepository
     */
    public function index(Request $request)
    {
        $orderNo = $request->order_no;
        $status = $request->status;
        $orderCreatorUserId = $request->order_creator_user_id;

        $orders = $this->afterServiceRepository->dataList(compact('orderNo', 'status', 'orderCreatorUserId'));

        return view('backend.order.after-service.index')->with([
            'orders' => $orders,
            'orderNo' => $orderNo,
            'status' => $status,
            'orderCreatorUserId' => $orderCreatorUserId,
        ]);
    }


}
