<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsRecharge extends Model
{
    public $fillable = [
        'user_id',
        'order_no',
        'before_amount',
        'amount',
        'after_amount',
    ];
}
