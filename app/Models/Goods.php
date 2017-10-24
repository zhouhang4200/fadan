<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    public $fillable = [
        'name',
        'display',
        'price',
        'foreign_goods_id',
        'service_id',
        'game_id',
        'goods_template_id',
    ];
}
