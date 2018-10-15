<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameLevelingForeignOrder extends Model
{
    public $fillable = ['trade_no', 'taobao_trade_no', 'payment'];

    /**
     *  多对一，订单表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameLevelingOrder() {
        return $this->belongsTo(GameLevelingOrder::class, 'trade_no', 'trade_no');
    }

}
