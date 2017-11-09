<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForeignOrder extends Model
{
    protected $fillable = ['channel',
        'channel_name',
        'order_time',
        'kamen_order_id',
        'foreign_order_id',
        'foreign_goods_id',
        'single_price',
        'total_price',
        'wang_wang',
        'tel',
        'details',
        'qq'
    ];

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function setDetailsAttribute($value)
    {
        return $this->attributes['details'] = json_encode($value);
    }
}
