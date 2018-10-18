<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingOrderDetail extends Model
{
    public $fillable = [
        'game_leveling_order_trade_no',
        'game_leveling_region_name',
        'game_leveling_server_name',
        'game_leveling_type_name',
        'game_name',
        'username',
        'parent_username',
        'take_username',
        'take_parent_username',
        'user_phone',
        'user_qq',
        'player_name',
        'player_phone',
        'player_qq',
        'parent_user_phone',
        'parent_user_qq',
        'take_user_qq',
        'take_user_phone',
        'take_parent_phone',
        'take_parent_qq',
        'explain',
        'requirement',
    ];

    /**
     * 一对一，订单
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameLevelingOrder()
    {
        return $this->belongsTo(GameLevelingOrder::class, 'trade_no', 'trade_no');
    }
}
