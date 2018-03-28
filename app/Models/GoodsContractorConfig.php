<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsContractorConfig extends Model
{
    public $fillable = [
      'user_id',
      'km_goods_id',
      'created_admin_user_id',
    ];
    /**
     * @param $query
     * @param $filter
     */
    public static function scopeFilter($query, $filter)
    {
        if (isset($filter['kmGoodsId']) && $filter['kmGoodsId']) {
            $query->where('km_goods_id', $filter['kmGoodsId']);
        }
        return $query;
    }
}
