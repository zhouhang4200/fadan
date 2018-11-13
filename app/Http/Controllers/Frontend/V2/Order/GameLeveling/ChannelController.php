<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

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
     * @return mixed
     */
    public function index()
    {
        $tradeNo = request('trade_no');
        $gameName = request('game_name');
        $status = request('status');
        $startDate = request('date')[0];
        $endDate = request('date')[1];
        $filter = compact('tradeNo', 'gameName', 'status', 'startDate', 'endDate');

        return GameLevelingChannelOrder::filter($filter)
            ->with(['gameLevelingOrders' => function ($query) {
                return $query->latest('game_leveling_orders.id')->first();
            }])
            ->paginate(15);
    }

    /**
     * 同意退款
     * @return mixed
     */
    public function agreeRefund()
    {
        DB::beginTransaction();
        try {
            $gameLevelingChannelRefund = GameLevelingChannelRefund::where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->where('status', 6)
                ->first();

            $gameLevelingChannelOrder = GameLevelingChannelOrder::where('trade_no', request('trade_no'))
                ->where('user_id', Auth::user()->getPrimaryUserId())
                ->where('status', 6)
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
//                return $result;
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
//                return $result;
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '操作失败：服务器错误!');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 拒绝退款
     * @return mixed
     */
    public function refuseRefund()
    {
        try {
            $gameLevelingChannelRefund = GameLevelingChannelRefund::where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->where('status', 6)
                ->first();

            $gameLevelingChannelRefund->refuse_refund_reason = request('refuse_refund_reason');
            $gameLevelingChannelRefund->status = 2;
            $gameLevelingChannelRefund->save();
        } catch (Exception $e) {
            return response()->ajax(0, '操作失败：服务器错误!');
        }
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 渠道游戏
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
     * @return mixed
     */
    public function status()
    {
        try {
            $tradeNo = request('trade_no');
            $gameName = request('game_name');
            $startDate = request('date')[0];
            $endDate = request('date')[1];
            $filter = compact('tradeNo', 'gameName', 'startDate', 'endDate');

            return GameLevelingChannelOrder::filter($filter)
                ->where('user_id', Auth::user()->getPrimaryUserId())
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
     * @return mixed
     */
    public function refund()
    {
        try {
            return GameLevelingChannelRefund::where('status', 6)
                ->where('game_leveling_channel_order_trade_no', request('trade_no'))
                ->first();
        } catch (Exception $e) {

        }
    }
}