<?php
namespace App\Repositories\Backend;

use App\Models\PunishOrReward;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\CustomException;
use DB;
use App\Events\NotificationEvent;

/**
 * 奖惩
 * Class ForeignOrderRepository
 * @package App\Repositories\Backend
 */
class PunishOrRewardRepository
{
    // 创建订单售后单
    public static function createOrderAfterService($orderNo, $amount, $remark)
    {
        $order = Order::where('no', $orderNo)->first();
        if (empty($order)) {
            throw new CustomException('订单不存在');
        }

        DB::beginTransaction();

        if (PunishOrReward::where('order_no', $orderNo)->where('type', 6)->lockForUpdate()->first()) {
            throw new CustomException('该订单退过款');
        }

        $punishOrReward = new PunishOrReward;
        $punishOrReward->no                  = generateOrderNo();
        $punishOrReward->order_no            = $orderNo;
        $punishOrReward->user_id             = $order->gainer_primary_user_id;
        $punishOrReward->type                = 6;
        $punishOrReward->status              = 1;
        $punishOrReward->sub_money           = $amount;
        $punishOrReward->deadline            = Carbon::now()->addDays(config('punish.order_refund_max_days'));
        $punishOrReward->remark              = $remark;
        $punishOrReward->confirm             = 0;

        if (!$punishOrReward->save()) {
            DB::rollback();
            throw new CustomException('创建失败');
        }

        DB::commit();

        // 创建订单售后
        event(new NotificationEvent('orderRefund', ['user_id' => $order->gainer_primary_user_id, 'amount' => $amount]));

        return true;
    }
}
