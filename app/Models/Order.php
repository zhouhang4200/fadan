<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public function goodsTemplateValue()
    {
        return $this->hasMany(GoodsTemplateValue::class);
    }
}
