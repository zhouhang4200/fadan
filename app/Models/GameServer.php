<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameServer extends Model
{
    public $fillable = [
        'name',
        'game_region_id',
        'initials',
    ];

    /**
     * 多对一，区
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gameRegion() {
        return $this->belongsTo(GameRegion::class);
    }

    /**
     * 游戏服条件搜索
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public static function scopeFilter($query, $filter = [])
    {
        if ($filter['name']) {
            $query->where('name', $filter['name']);
        }
        return $query;
    }
}
