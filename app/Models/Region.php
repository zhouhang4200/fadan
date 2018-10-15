<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $fillable = [
        'name',
        'game_id',
        'initials',
    ];

    /**
     * 游戏，多对一关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game() {
        return $this->belongsTo(Game::class);
    }
}
