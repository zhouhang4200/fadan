<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 月结算订单
 * Class MonthSettlementOrders
 * @package App\Models
 */
class MonthSettlementOrders extends Model
{
    public $timestamps = false;

    public $fillable = [
      'order_no',
      'game_id',
      'status',
      'status',
      'finish_time',
      'foreign_order_no',
      'creator_primary_user_id',
      'gainer_primary_user_id',
      'gainer_primary_user_id',
    ];

    /**
     * @param $query
     * @param $condition
     */
    public static function scopeFilter($query, $condition)
    {
        if (isset($condition['foreignOrderNo']) && $condition['foreignOrderNo']) {
            $query->where('foreign_order_no', $condition['foreignOrderNo']);
        }
        if (isset($condition['foreignOrderNo']) && $condition['foreignOrderNo']) {
            $query->where('creator_primary_user_id', $condition['foreignOrderNo']);
        }
        if (isset($condition['foreignOrderNo']) && $condition['foreignOrderNo']) {
            $query->where('gainer_primary_user_id', $condition['foreignOrderNo']);
        }
        if (isset($condition['finishTimeStart']) && $condition['finishTimeStart']) {
            $query->where('finish_time', '>=', $condition['finishTimeStart']);
        }
        if (isset($condition['finishTimeEnd']) && $condition['finishTimeEnd']) {
            $query->where('finish_time', '<=',  $condition['finishTimeEnd']);
        }
        return $query;
    }
}
