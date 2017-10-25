<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    public $fillable = [
        'name',
        'user_id',
        'display',
        'price',
        'foreign_goods_id',
        'service_id',
        'game_id',
        'goods_template_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function goodsTemplate()
    {
        return $this->belongsTo(GoodsTemplate::class);
    }
}
