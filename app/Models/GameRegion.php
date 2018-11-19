<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRegion extends Model
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
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * 区下面的服
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gameServers()
    {
        return $this->hasMany(GameServer::class);
    }

    /**
     * 后台条件查询
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
