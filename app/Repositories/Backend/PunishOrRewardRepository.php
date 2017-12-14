<?php
namespace App\Repositories\Backend;

use App\Models\PunishOrReward;

/**
 * 奖惩
 * Class ForeignOrderRepository
 * @package App\Repositories\Backend
 */
class PunishOrRewardRepository
{
    // 订单退款单列表
    public static function orderRefundList()
    {
        $dataList = PunishOrReward::where('type', 6)->paginate(30);

        return $dataList;
    }
}
