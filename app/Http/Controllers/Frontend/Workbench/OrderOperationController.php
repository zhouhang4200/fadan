<?php
namespace App\Http\Controllers\Frontend\Workbench;

use Order;
use Carbon\Carbon;
use App\Models\Punish;
use App\Models\SiteInfo;
use Illuminate\Http\Request;
use App\Services\KamenOrderApi;
use App\Events\NotificationEvent;
use App\Models\Order as OrderModel;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Frontend\OrderRepository;
use App\Extensions\Order\Operations\Payment;
use App\Extensions\Order\Operations\Cancel;
use App\Extensions\Order\Operations\Complete;
use App\Extensions\Order\Operations\Delivery;
use App\Extensions\Order\Operations\TurnBack;
use App\Extensions\Order\Operations\DeliveryFailure;
use App\Extensions\Order\Operations\AskForAfterService;

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
        $deadline = Punish::where('user_id', Auth::id())->where('type', 0)->oldest('deadline')->value('deadline');

        if ($deadline) {

            $time = Carbon::parse($deadline);

            $int = (new Carbon)->diffInSeconds($time, false);

            if ($int < 0) {
                return response()->ajax(0, '您已超过违规罚款截止日期，请先交违规罚款');
            }
        }

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
        // 提示用户：接单成功等待系统分配
        return response()->ajax(1, '抢单成功,等待系统分配');
    }

    /**
     * 订单操作
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request, OrderRepository $orderRepository)
    {
        $order = $orderRepository->detail($request->no);
        if ($order) {
            return view('frontend.workbench.order-detail', compact('order'));
        }
    }

    /**
     * 订单发货
     * @param Request $request
     */
    public function delivery(Request $request)
    {
        // 调用发货
        try {
            // 调用收货
            Order::handle(new Delivery($request->no, Auth::user()->id));
            // 向卡门发送通知
            $order = OrderModel::where('no', $request->no)->first();

            $has = SiteInfo::where('user_id', $order->creator_primary_user_id)->first();

            if ($order->foreignOrder && $has) {

                KamenOrderApi::share()->success($order->foreignOrder->kamen_order_no);
            }
            return response()->ajax(1, '操作成功');

        } catch (CustomException $exception) {

            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 失败订单
     * @param Request $request
     */
    public function fail(Request $request)
    {
        try {
            // 调用失败订单
            Order::handle(new DeliveryFailure($request->no, Auth::user()->id, $request->remark));
            // 调用打款，删除自动打款哈希表中订单号
            $order = OrderModel::where('no', $request->no)->first();

            $has = SiteInfo::where('user_id', $order->creator_primary_user_id)->first();

            if ($order->foreignOrder && $has) {
                KamenOrderApi::share()->fail($order->foreignOrder->kamen_order_no);
            }
            return response()->ajax(1, '操作成功');

        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 取消订单
     * @param Request $request
     */
    public function cancel(Request $request)
    {
        try {
            // 调用取消
            Order::handle(new Cancel($request->no, Auth::user()->id));
            // 待接单数量加1
            waitReceivingQuantitySub();
            // 待接单数量
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
            // 调用打款，删除自动打款哈希表中订单号
            return response()->ajax(1, '操作成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
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
            return response()->ajax(1, '操作成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 退回订单集市
     * @param Request $request
     * @return
     */
    public function turnBack(Request $request)
    {
        try {
            // 调用退回
            Order::handle(new TurnBack($request->no, Auth::user()->id, $request->remark));

            $carbon = new Carbon;
            $minutes = $carbon->diffInMinutes(Order::get()->created_at);

            if ($minutes >= 40) {
                // 超过40分钟失败
                Order::handle(new Cancel($request->no, 0));
                $has = SiteInfo::where('user_id', Order::get()->creator_primary_user_id)->first();

                if (Order::get()->foreignOrder && $has) {
                    KamenOrderApi::share()->fail(Order::get()->foreignOrder->kamen_order_no);
                }
                waitReceivingQuantitySub();
            } else {
                // 待接单数量加1
                waitReceivingQuantityAdd();
                // 待接单数量刷新
                event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
                // 给所有用户推送新订单消息
                event(new NotificationEvent('NewOrderNotification', Order::get()->toArray()));
                // 重写放入订单集市
                waitReceivingAdd(Order::get()->no, json_encode(['receiving_date' => Carbon::now('Asia/Shanghai')->addMinutes(1)->toDateTimeString(), 'created_date' => Order::get()->created_at->toDateTimeString()]));
            }
            // 返回操作成功
            return response()->ajax(0, '操作成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 订单售后
     * @param Request $request
     * @internal param array $middleware
     */
    public function afterSales(Request $request)
    {
        try {
            // 调用退回
            Order::handle(new AskForAfterService($request->no, Auth::user()->id));
            // 返回操作成功
            return response()->ajax(0, '操作成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

    /**
     * 支付订单
     * @param Request $request
     */
    public function payment(Request $request)
    {
        try {
            // 调用退回
            Order::handle(new Payment($request->no, Auth::user()->id));
            // 给所有用户推送新订单消息
            event(new NotificationEvent('NewOrderNotification', Order::get()->toArray()));
            // 待接单数量加1
            waitReceivingQuantityAdd();
            // 待接单数量
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
            // 返回操作成功
            return response()->ajax(0, '操作成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, $exception->getMessage());
        }
    }

}

