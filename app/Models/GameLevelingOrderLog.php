<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 代练订单操作日志
 * Class GameLevelingOrderLog
 * @package App\Models
 */
class GameLevelingOrderLog extends Model
{
    public $fillable = [
      'game_leveling_order_trade_no',
      'type',
      'user_id',
      'username',
      'parent_user_id',
      'admin_user_id',
      'name',
      'description',
    ];

    /**
     * 写订单日志
     * @param GameLevelingOrder $order
     * @param $type
     * @param int $adminUserId
     * @param string $description
     */
    public static function createOrderHistory(GameLevelingOrder $order, $type, $description = '', $adminUserId = 0)
    {
        static::create([
            'game_leveling_order_trade_no' => $order->trade_no,
            'user_id' => $order->user_id,
            'parent_user_id' => $order->parent_user_id,
            'admin_user_id' => $adminUserId ?? 0,
            'type' => $type,
            'name' => config('order.operation_type')[$type],
            'description' => $description,
        ]);
    }
}
