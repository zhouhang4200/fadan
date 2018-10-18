<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameLevelingServer extends Model
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
    public function region() {
        return $this->belongsTo(GameLevelingRegion::class);
    }
}
