<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 游戏代练 渠道价格
 * Class GameLevelingChannelPrice
 * @package App\Models
 */
class GameLevelingChannelPrice extends Model
{
    public $fillable = [
        'game_leveling_channel_game_id',
        'sort',
        'level',
        'price',
        'hour',
        'security_deposit',
        'efficiency_deposit',
    ];

    /**
     * @param $query
     * @param $conditions
     * @return
     */
    public static function scopeFilter($query, $conditions)
    {
        if (isset($conditions['game_leveling_channel_game_id']) && $conditions['game_leveling_channel_game_id']) {
            $query->where('game_leveling_channel_game_id', $conditions['game_leveling_channel_game_id']);
        }
        return $query;
    }

    public function gameLevelingChannelGame()
    {
        return $this->belongsTo(GameLevelingChannelGame::class);
    }
}
