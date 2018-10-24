<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingOrderPreviousStatus extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameLevelingOrder()
    {
        return $this->belongsTo(GameLevelingOrder::class, 'trade_no', 'game_leveling_order_trade_no');
    }
}
