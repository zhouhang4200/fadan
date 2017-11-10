<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
    protected $dates = [
        'order_time',
        'order_in_time',
        'order_out_time',
        'order_end_time',
        'order_use_time',
    ];
}
