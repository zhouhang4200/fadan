<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use App\Exceptions\GameLevelingOrderOperateException;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingPlatform;
use App\Services\OrderOperateController;
use Exception;
use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\GameLevelingChannelGame;
use App\Models\GameLevelingChannelRefund;
use App\Models\GameLevelingChannelOrder;

/**
 * 游戏代练渠道订单控制器
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class ChannelController extends Controller
{
    /**
     * 渠道表单数据
     *
     * @return mixed
     */
    public function index()
    {
        return GameLevelingChannelOrder::filter(request()->all())
            ->where('user_id', request()->user()->getPrimaryUserId())
            ->with(['gameLevelingOrders' => function ($query) {
                return $query->latest('game_leveling_orders.id')->first();
            }])
            ->paginate(15);
    }

    /**
     * 同意退款
     *
     * @return mixed
     */
    public function agreeRefund()
    {
        DB::beginTransaction();
        try {
            $gameLevelingChannelRefund = GameLevelingChannelRefund::where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->where('status', 1)
                ->first();

            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('user_id', request()->user()->getPrimaryUserId())
                ->where('status', 5)
                ->first();

            // 两个表的支付方式是否一致
            if ($gameLevelingChannelRefund->payment_type != $gameLevelingChannelOrder->payment_type) {
                throw new Exception( '操作失败：服务器支付参数不匹配!');
            }

            // 支付宝
            if ($gameLevelingChannelOrder->payment_type === 1) {
                $orderConfig = [
                    'out_trade_no' => $gameLevelingChannelOrder->trade_no,
                    'refund_amount' => $gameLevelingChannelOrder->payment_amount,
                ];

                $basicConfig = config('alipay.base_config');

                $result = Pay::alipay($basicConfig)->refund($orderConfig);

                if (!$result || $result['code'] != 10000) {
                    throw new Exception('支付失败!');
                }
            }

            // 微信
            if ($gameLevelingChannelOrder->payment_type === 2) {
                $orderConfig = [
                    'out_trade_no' => $gameLevelingChannelOrder->trade_no,
                    'out_refund_no' => time(),
                    'total_fee' => $gameLevelingChannelRefund->payment_amount,
                    'refund_fee' => $gameLevelingChannelRefund->refund_amount,
                    'refund_desc' => '渠道退款',
                ];

                $basicConfig = config('wechat.base_config');

                $result = Pay::wechat($basicConfig)->refund($orderConfig);

                if (!$result || $result['return_code'] !== 'SUCCESS') {
                    throw new Exception('支付失败!');
                }
            }
            // 退款成功改变渠道退款单状态
            $gameLevelingChannelRefund->status = 2;
            $gameLevelingChannelOrder->save();
            // 退款成功改变渠道订单状态
            $gameLevelingChannelOrder->status = 6;
            $gameLevelingChannelOrder->save();

            // 同意成功之后撤单第三方平台的订单
            if ($order = GameLevelingOrder::where('channel_order_trade_no', $gameLevelingChannelOrder->trade_no)->where('status', 22)->latest('id')->first()) {
                OrderOperateController::init(User::find($order->user_id), $order)->delete();

                // 该订单下单成功的接单平台
                $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                    ->get();

                // 下单成功的接单平台
                if ($gameLevelingPlatforms->count() > 0) {
                    foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                        call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['delete']], [$order]);
                    }
                }
            }
        } catch (GameLevelingOrderOperateException $e) {
            myLog('channel-agree-refund-error', ['trade_no' => $gameLevelingChannelOrder->trade_no ?? '', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('channel-agree-refund-error', ['trade_no' => $gameLevelingChannelOrder->trade_no ?? '', 'message' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->ajax(0, '操作失败：服务器错误!');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 拒绝退款
     *
     * @return mixed
     */
    public function refuseRefund()
    {
        DB::beginTransaction();
        try {
            // 申请退款问单据改为拒绝
            GameLevelingChannelRefund::where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->where('status', 1)
                ->update([
                    'refuse_refund_reason' => request('refuse_refund_reason'),
                    'status' => 3
                ]);

            // 渠道订单状态改回进行中
            GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('user_id', request()->user()->getPrimaryUserId())
                ->where('status', 5)
                ->update(['status' => 2]);

            // 平台订单的渠道订单状态改为进行中
            GameLevelingOrder::where('channel_order_trade_no', request('trade_no'))
                ->where('channel_order_status', 5)
                ->update(['channel_order_status' => 2]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '操作失败：服务器错误!');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 渠道游戏
     *
     * @return mixed
     */
    public function game()
    {
        try {
            return GameLevelingChannelGame::pluck('game_name', 'game_id')->unique();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 渠道订单状态数量
     *
     * @return mixed
     */
    public function status()
    {
        try {
            return GameLevelingChannelOrder::filter(request()->except('status'))
                ->where('user_id', request()->user()->getPrimaryUserId())
                ->selectRaw('status, count(1) as statusCount')
                ->groupBy('status')
                ->pluck('statusCount', 'status')
                ->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 申请退款记录
     *
     * @return mixed
     */
    public function refund()
    {
        try {
            return GameLevelingChannelRefund::where('status', 1)
                ->where('user_id', request()->user()->getPrimaryUserId())
                ->where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->first();
        } catch (Exception $e) {
            return [];
        }
    }
}