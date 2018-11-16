<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 渠道c端用户表
 * Class GameLevelingChannelUser
 * @package App\Models
 */
class GameLevelingChannelUser extends Model
{
    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'uuid',
    ];

    /**
     * 渠道c端用户的订单
     */
    public function gameLevelingChannelOrders()
    {
        $this->hasMany(GameLevelingChannelOrder::class);
    }
}
