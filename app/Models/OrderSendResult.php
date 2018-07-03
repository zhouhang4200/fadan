<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSendResult extends Model
{
    public $fillable = [
        'order_no',
        'third_name',
        'third_order_no',
        'status',
        'result',
    ];
}
