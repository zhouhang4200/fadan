<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 游戏代练 渠道开通的游戏
 * Class GameLevelingChannelGame
 * @package App\Models
 */
class GameLevelingChannelGame extends Model
{
    /**
     * @var array
     */
    public $fillable = [
        'user_id',
        'game_id',
        'game_name',
        'game_leveling_type_id',
        'rebate',
        'instructions',
        'requirements',
        'user_qq',
    ];

    /**
     * 游戏对应的价格配置
     */
    public function gameLevelingChannelPrices()
    {
        $this->hasMany(GameLevelingChannelPrice::class);
    }

    /**
     * 游戏对应的扣
     */
    public function gameLevelingChannelDiscount()
    {
        $this->hasMany(GameLevelingChannelPrice::class);
    }

}
