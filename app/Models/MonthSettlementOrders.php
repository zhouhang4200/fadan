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
    public $statusText = [
        1 => '未结账',
        2 => '已结算',
    ];

    public $timestamps = false;

    public $fillable = [
      'order_no',
      'game_id',
      'status',
      'status',
      'amount',
      'finish_time',
      'foreign_order_no',
      'creator_primary_user_id',
      'creator_primary_user_name',
      'gainer_primary_user_id',
      'gainer_primary_user_name',
    ];

    /**
     * @param $query
     * @param $condition
     */
    public static function scopeFilter($query, $condition)
    {
        if (isset($condition['no']) && $condition['no']) {
            $query->where('foreign_order_no', $condition['no'])->orWhere('order_no', $condition['no']);
        }
        if (isset($condition['gameId']) && $condition['gameId']) {
            $query->where('game_id', $condition['gameId']);
        }
        if (isset($condition['creatorPrimaryUserId']) && $condition['creatorPrimaryUserId']) {
            $query->where('creator_primary_user_id', $condition['creatorPrimaryUserId']);
        }
        if (isset($condition['gainerPrimaryUserId']) && $condition['gainerPrimaryUserId']) {
            $query->where('gainer_primary_user_id', $condition['gainerPrimaryUserId']);
        }
        if (isset($condition['finishTimeStart']) && $condition['finishTimeStart']) {
            $query->where('finish_time', '>=', $condition['finishTimeStart']);
        }
        if (isset($condition['finishTimeEnd']) && $condition['finishTimeEnd']) {
            $query->where('finish_time', '<=',  $condition['finishTimeEnd']);
        }
        return $query;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'no', 'order_no');
    }
}
