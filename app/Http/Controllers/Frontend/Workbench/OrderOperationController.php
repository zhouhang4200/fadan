<?php
namespace App\Http\Controllers\Frontend\Workbench;

use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Complete;
use App\Extensions\Order\Operations\TurnBack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Frontend\OrderRepository;

/**
 * Class OrderOperationController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class OrderOperationController extends Controller
{
    /**
     * 接单
     * @param Request $request
     * @return mixed
     */
    public function receiving(Request $request)
    {
        $orderNo = $request->no;
        // 获取当前用户ID
        $currentUserId = Auth::user()->id;
        // 获取主账号
        $primaryUserId = Auth::user()->getPrimaryUserId();
        // 检测是否已接单
        if (receivingRecordExist($primaryUserId, $orderNo)) {
            // 提示用户：您已经接过该单
            return response()->ajax(0, '您已经接过该单');
        }
        // 接单后，将当前接单用户的ID写入相关的订单号的队列中
        receiving($currentUserId, $orderNo);
        // 接单成功，将主账号ID与订单关联写入redis 防止用户多次接单
        receivingRecord($primaryUserId, $orderNo);
        // 加入待分配订单
        waitReceivingAdd($orderNo);
        // 提示用户：接单成功等待系统分配
        return response()->ajax();
    }

    /**
     * 订单操作
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, OrderRepository $orderRepository)
    {
        $detail = $orderRepository->detail($request->no);
        if ($detail) {
            return view('frontend.workbench.order-detail', compact('detail'));
        }
    }

    /**
     * 订单发货
     * @param Request $request
     */
    public function delivery(Request $request)
    {
        // 调用发货

        // 向卡门发送通知
    }

    /**
     * 失败订单
     * @param Request $request
     */
    public function failure(Request $request)
    {
        // 调用失败订单

        // 向卡门发通知

        // 退款
    }

    /**
     * 取消订单
     * @param Request $request
     */
    public function cancel(Request $request)
    {
        // 调用取消
        try {
            // 调用收货
            Order::handle(new Complete($request->no, Auth::user()->id));
            // 调用打款，删除自动打款哈希表中订单号
            return response()->ajax(0, '');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
        // 退款
    }

    /**
     * 确认收货
     * @param Request $request
     */
    public function confirm(Request $request)
    {
        try {
            // 调用收货
            Order::handle(new Complete($request->no, Auth::user()->id));
            // 调用打款，删除自动打款哈希表中订单号
            return response()->ajax(0, '');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 退回订单集市
     * @param Request $request
     * @return
     */
    public function return(Request $request)
    {
        try {
            // 调用退回
            Order::handle(new TurnBack($request->no, Auth::user()->id));
            // 给所有用户推送新订单消息
            event(new NotificationEvent('NewOrderNotification', Order::get()->toArray()));
            // 待接单数量加1
            waitReceivingQuantityAdd();
            // 待接单数量
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
            return response()->ajax(0, '');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }
}

