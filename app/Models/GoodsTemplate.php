<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsTemplate extends Model
{
    public $fillable = [
        'name',
        'admin_user_id',
    ];

    /**
     * 按模版名字搜索
     * @param $query
     * @param $name
     */
    public  function scopeName($query, $name)
    {
        if ($name) {
            return $query->where('name', $name);
        }
        return $query;
    }

}
