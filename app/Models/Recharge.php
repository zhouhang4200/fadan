<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    public $fillable = [
        'amount',
        'user_id',
        'foreign_order_no',
    ];
}
