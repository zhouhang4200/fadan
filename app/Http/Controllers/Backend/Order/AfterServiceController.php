<?php

namespace App\Http\Controllers\Backend\Order;

use App\Extensions\Order\Operations\AskForAfterService;
use App\Models\AfterService;
use App\Repositories\Backend\AfterServiceRepository;
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
class AfterServiceController extends Controller
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
     * 售后订单
     * @param Request $request
     * @param AfterServiceRepository $afterServiceRepository
     * @return mixed
     */
    public function index(Request $request, AfterServiceRepository $afterServiceRepository)
    {

        $orders = $afterServiceRepository->dataList($request->start_date, $request->end_date, $request->no);

        return view('backend.order.after-service.index')->with([
            'orders' => $orders,
            'no' => $request->no,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
        ]);
    }

    /**
     * 审核
     */
    public function auditing()
    {

    }
}
