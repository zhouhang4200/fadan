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
    public function game() {
        return $this->belongsTo(Game::class);
    }
}
