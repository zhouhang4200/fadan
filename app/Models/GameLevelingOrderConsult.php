<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingOrderConsult extends Model
{
    public $fillable = [
        'user_id',
        'parent_user_id',
        'game_leveling_order_trade_no',
        'amount',
        'security_deposit',
        'efficiency_deposit',
        'poundage',
        'reason',
        'status',
        'initiator',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameLevelingOrder()
    {
        return $this->belongsTo(GameLevelingOrder::class, 'trade_no', 'game_leveling_order_trade_no');
    }
}
