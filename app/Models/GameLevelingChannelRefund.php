<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingChannelRefund extends Model
{
    public $fillable = [
        'game_leveling_channel_order_trade_no', 'payment_type', 'status', 'amount', 'payment_amount', 'refund_amount', 'refund_reason', 'refuse_refund_reason',
        'game_leveling_type_id', 'game_leveling_type_name', 'day', 'hour', 'type', 'pic1', 'pic2', 'pic3'
    ];

    public function gameLevelingChannelOrder()
    {
        return $this->belongsTo(GameLevelingChannelOrder::class, 'trade_no', 'game_leveling_channel_order_trade_no');
    }
}
