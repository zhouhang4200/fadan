<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCharge extends Model
{
    protected $primaryKey = 'order_no';
    protected $guarded = ['created_at', 'updated_at'];
}
