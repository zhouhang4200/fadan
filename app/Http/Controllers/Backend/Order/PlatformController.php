<?php

namespace App\Http\Controllers\Backend\Order;

use App\Extensions\Order\Operations\DeliveryFailure;
use App\Models\User;
use App\Extensions\Order\Operations\AskForAfterService;
use App\Models\AfterService;
use App\Models\PunishType;
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
use App\Extensions\Order\Operations\AfterServiceComplete;
use App\Repositories\Backend\PunishOrRewardRepository;

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

        if ($request->gainer_primary_user_id) {
            $gainerPrimaryUserId = User::where('nickname', $request->gainer_primary_user_id)->value('id') ?: $request->gainer_primary_user_id;
        }

        $filters = compact('startDate', 'endDate', 'source', 'status', 'serviceId', 'gameId', 'creatorPrimaryUserId',
            'gainerPrimaryUserId', 'no', 'foreignOrderNo');

        // 订单导出
        if ($export) {
            set_time_limit(0);

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
            'gainerPrimaryUserId' => $request->gainer_primary_user_id,
            'no' => $no,
            'foreignOrderNo' => $foreignOrderNo,
            'fullUrl' => $request->fullUrl(),
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
        $order = OrderModel::where('no', $request->data['no'])->first();
        switch ($request->data['type']) {
            case 'cancel' :
                try {
                    // 调用取消
                    Order::handle(new Cancel($request->data['no'], 0, auth('admin')->user()->id));
                    return response()->ajax(1, '操作成功');
                } catch (CustomException $exception) {
                    return response()->ajax(1, $exception->getMessage());
                }
                break;
            case 'fail' :
                try {
                    // 调用失败
                    Order::handle(new DeliveryFailure($request->data['no'], $order->gainer_primary_user_id, '后台失败'));
                    return response()->ajax(1, '操作成功');
                } catch (CustomException $exception) {
                    return response()->ajax(1, $exception->getMessage());
                }
                break;
        }

    }

    /**
     * 处理售后
     * @param Request $request
     * @return mixed
     */
    public function afterServiceComplete(Request $request)
    {
        $order = OrderModel::where('no', $request->no)->first();

        if (empty($order)) {
            return response()->ajax(0, '订单不存在');
        }

        if ($order->amount < $request->amount) {
            return response()->ajax(0, '退款金额不可大于订单总金额');
        }

        if (empty($request->remark)) {
            return response()->ajax(0, '说明不能为空');
        }

        switch ($order->status) {
            case 6:
                // 处理售后
                try {
                    Order::handle(new AfterServiceComplete($order->no, auth('admin')->user()->id, $request->amount, $request->remark));
                }
                catch (CustomException $e) {
                    return response()->ajax(0, $e->getMessage());
                }
                break;
            case 8:
                // 走奖惩流程
                try {
                    PunishOrRewardRepository::createOrderAfterService($order->no, $request->amount, $request->remark);
                }
                catch (CustomException $e) {
                    return response()->ajax(0, $e->getMessage());
                }
                break;
            default:
                return response()->ajax(0, '订单当前状态无法进行该操作');
        }


        return response()->ajax(1);
    }

    /**
     * 从后台发起售后
     * @param Request $request
     * @return mixed
     */
    public function applyAfterService(Request $request)
    {
        try {
            $orderInfo = OrderModel::where('no', $request->no)->first();
            if ($orderInfo) {
                // 调用退回
                Order::handle(new AskForAfterService($request->no, $orderInfo->creator_primary_user_id, auth('admin')->user()->name . ' 从后台发起售后'));
                // 返回操作成功
                return response()->ajax(0, '操作成功');
            } else {
                return response()->ajax(0, '订单不存在');
            }
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    // 操作记录
    public function history(Request $request)
    {
        $dataList = OrderRepository::history($request->start_date, $request->end_date, $request->type);
        $operationType = config('order.operation_type');

        return view('backend.order.platform.history', compact('dataList', 'operationType'));
    }
}
