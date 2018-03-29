<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCharge extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function orderChargeRecords()
    {
        return $this->hasMany(OrderChargeRecord::class, 'order_no', 'order_no');
    }
}
