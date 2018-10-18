<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingChannelOrder extends Model
{
    public $fillable = [
        'trade_no',
        'channel_user_id',
        'amount',
        'discount_amount',
        'payment_amount',
        'refund_amount',
        'status',
    ];
}
