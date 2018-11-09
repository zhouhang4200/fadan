<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingChannelDiscount extends Model
{
    public $fillable = [
        'game_leveling_channel_game_id',
        'level',
        'discount',
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
