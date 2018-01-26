<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WangWangBlacklist extends Model
{
    public $fillable = [
        'wang_wang',
        'admin_user_id',
    ];

    /**
     * @param $query
     * @param $condition
     */
    public static function scopeFilter($query, $condition)
    {
        if (isset($condition['wangWang']) && $condition['wangWang']) {
            $query->where('wang_wang', 'like', '%' . $condition['wangWang'] . '%');
        }
        return $query;
    }
}
