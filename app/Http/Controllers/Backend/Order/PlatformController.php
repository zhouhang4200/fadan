<?php

namespace App\Http\Controllers\Backend\Order;

use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Cancel;
use App\Repositories\Backend\ForeignOrderRepository;
use App\Repositories\Backend\OrderRepository;
use Auth, View, Order;
use App\Models\Order as OrderModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Backend\GameRepository;
use App\Repositories\Backend\ServiceRepository;
use League\Flysystem\Exception;

/**
 * Class OrderController
 * @package App\Http\Controllers\Backend
 */
class PlatformController extends Controller
{
    /**
     * @var OrderRepository
     */
    protected $order;

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
        $gainerPrimaryUserId = $request->input('gainer_primary_user_id');
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

        return view('backend.order.platform.index')->with([
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
     * 查看订单内容
     *
     * @param Request $orderNo
     * @return \Illuminate\Http\JsonResponse
     */
    public function content($orderNo)
    {
        return view('backend.order.platform.content')->with([
            'content' => OrderModel::with(['detail'])->find($orderNo)
        ]);
    }

    /**
     * 订单操作记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function record(Request $request)
    {
        return view('backend.order.platform.record')->with([
            'record' => OrderModel::with(['history', 'creatorUser', 'gainerUser'])->find($request->id),
        ]);
    }

    /**
     * 申请退款
     * @param Request $request
     */
    public function refundApplication(Request $request)
    {

    }

    /**
     * 修改订单状态
     * @param Request $request
     */
    public function changeStatus(Request $request)
    {
        switch ($request->data['type']) {
            case 'cancel' :
                try {
                    // 调用取消
                    Order::handle(new Cancel($request->data['no'], 0, Auth::user()->id));
                    return response()->ajax(1, '操作成功');
                } catch (CustomException $exception) {
                    return response()->ajax(1, $exception->getMessage());
                }
                break;
        }

    }
}
