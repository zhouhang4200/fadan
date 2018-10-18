<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingOrderRelationChannel extends Model
{
    public $fillable = ['channel', 'game_leveling_order_trade_no', 'game_leveling_channel_order_trade_no', 'payment'];
}
