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
}
