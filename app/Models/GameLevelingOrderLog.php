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
     *
     * @param GameLevelingOrder $order
     * @param User $user
     * @param $type
     * @param string $description
     * @param int $adminUserId
     */
    public static function createOrderHistory(GameLevelingOrder $order, User $user, $type, $description = '', $adminUserId = 0)
    {
        static::create([
            'game_leveling_order_trade_no' => $order->trade_no,
            'user_id' => $user->id,
            'username' => $user->username,
            'parent_user_id' => $user->parent_id,
            'admin_user_id' => $adminUserId ?? 0,
            'type' => $type,
            'name' => config('order.operation_type')[$type],
            'description' => $description,
        ]);
    }
}
