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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }
}
