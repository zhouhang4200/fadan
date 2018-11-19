<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingType extends Model
{
    public  $fillable = [
        'name',
        'game_id',
        'poundage',
    ];

    /**
     *  多对一，游戏
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * @param $query
     * @param $conditions
     * @return mixed
     */
    public static function scopeFilter($query, $conditions)
    {
        if (isset($conditions['game_id']) && $conditions['game_id']) {
            $query->where('game_id', $conditions['game_id']);
        }

        if (isset($conditions['name']) && !empty($conditions['name'])) {
            $query->where('name', $conditions['name']);
        }
        return $query;
    }
}
