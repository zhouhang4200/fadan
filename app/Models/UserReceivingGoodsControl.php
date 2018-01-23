<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReceivingGoodsControl extends Model
{
    public $fillable = [
        'user_id',
        'other_user_id',
        'goods_id',
        'remark',
        'type',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}
