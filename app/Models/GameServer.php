<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameServer extends Model
{
    public $fillable = [
        'name',
        'region_id',
        'initials',
    ];

    /**
     * 多对一，区
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameRegion() {
        return $this->belongsTo(GameRegion::class);
    }
}
