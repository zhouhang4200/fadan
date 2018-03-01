<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Cancel;
use App\Extensions\Order\Operations\Delivery;
use App\Extensions\Order\Operations\DeliveryFailure;
use App\Http\Controllers\Controller;
use App\Services\RedisConnect;
use Illuminate\Http\Request;
use Order;
use App\Models\Order as OrderModel;

/**
 * 房卡充值
 * Class RoomCardRecharge
 * @package App\Http\Controllers\Api
 */
class RoomCardRecharge extends Controller
{
    /**
     * 获取订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $gameId = $request->game_id;
        $redis = RedisConnect::order();
        $result = $redis->lpop(config('redis.order.roomCardRecharge') . $gameId);
        if ($result) {
            return response()->json(['status' => 1, 'message' => '获取成功', 'data' => $result]);
        }
        return response()->json(['status' => 0, 'message' => '暂时没有订单', 'data' => '']);
    }

    /**
     * 更新状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // 1 成功 2 失败
        if(in_array($request->status, [1, 2]) && strlen($request->no) == 22) {
            $orderInfo = OrderModel::where('no', $request->no)->first();
            if ($request->status == 1) {
                try {
                    // 调用发货
                    Order::handle(new Delivery($request->no, $orderInfo->creator_primary_user_id));
                    // 写入自动确认队列
                    waitConfirmAdd($request->no, time());
                    return response()->json(['status' => 1, 'message' => '操作成功', 'data' => '']);
                } catch (CustomException $exception) {
                    return response()->json(['status' => 0, 'message' => $exception->getMessage(), 'data' => '']);
                }
            } elseif ($request->status == 2) {
                try {
                    // 调用失败订单
                    Order::handle(new DeliveryFailure($request->no, 8017, '充值失败'));
                    // 商家失败后直接取消订单
                    Order::handle(new Cancel($request->no, 0, 0));
                    return response()->json(['status' => 1, 'message' => '操作成功', 'data' => '']);
                } catch (CustomException $exception) {
                    return response()->json(['status' => 0, 'message' => $exception->getMessage(), 'data' => '']);
                }
            }
        }
    }
}
