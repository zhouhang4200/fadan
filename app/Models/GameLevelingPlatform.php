<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingPlatform extends Model
{
    public $fillable = ['trade_no', 'platform_id', 'platform_no'];

    /**
     * 多对一，订单表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameLevelingOrder()
    {
        return $this->belongsTo(GameLevelingOrder::class, 'trade_no', 'trade_no');
    }
}
