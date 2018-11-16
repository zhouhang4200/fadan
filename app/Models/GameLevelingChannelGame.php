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
        'game_leveling_type_name',
        'rebate',
        'explain',
        'requirement',
        'user_qq',
    ];

    /**
     * 游戏对应的价格配置
     */
    public function gameLevelingChannelPrices()
    {
        return $this->hasMany(GameLevelingChannelPrice::class, 'game_leveling_channel_game_id', 'id');
    }

    /**
     * 游戏对应的扣
     */
    public function gameLevelingChannelDiscounts()
    {
        return $this->hasMany(GameLevelingChannelDiscount::class, 'game_leveling_channel_game_id', 'id');
    }

    /**
     * @param $query
     * @param $conditions
     * @return
     */
    public static function scopeFilter($query, $conditions)
    {
        if (isset($conditions['game_id']) && $conditions['game_id']) {
            $query->where('game_id', $conditions['game_id']);
        }
        return $query;
    }
}
