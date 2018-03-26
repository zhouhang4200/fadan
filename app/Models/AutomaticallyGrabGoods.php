<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 自动抓单商品配置表
 * Class AutomaticallyGrabGoods
 * @package App\Models
 */
class AutomaticallyGrabGoods extends Model
{
    public $fillable = [
      'user_id',
      'service_id',
      'foreign_goods_id',
      'status',
      'remark',
    ];

    /**
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['foreignGoodsId']) && $filters['foreignGoodsId']) {

            $query->where('foreign_goods_id', $filters['foreignGoodsId']);
        }
        return $query;
    }

}
