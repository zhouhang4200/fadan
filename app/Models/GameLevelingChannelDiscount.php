<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingChannelDiscount extends Model
{
    public $fillable = [
        'game_leveling_channel_game_id',
        'level',
        'discount',
    ];
}
