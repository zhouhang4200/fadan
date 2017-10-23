<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForeignOrder extends Model
{
    protected $fillable = ['channel', 'foreign_order_id', 'foreign_goods_id', 'single_price', 'total_price', 'contact_way', 'details'];

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function setDetailsAttribute($value)
    {
        return $this->attributes['details'] = json_encode($value);
    }
}
