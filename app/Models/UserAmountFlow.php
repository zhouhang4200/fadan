<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAmountFlow extends Model
{
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    public function tradeOrder()
    {
        return $this->morphTo();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'trade_no', 'no');
    }


    /**
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public static function scopeFilter($query, $filter = [])
    {
        if ($filter['tradeNo']) {
            $query->where('trade_no', $filter['tradeNo']);
        }

        if ($filter['tradeType'] && $filter['tradeType'] == 7) {
            $query->whereIn('trade_type', [5, 7]);
        } elseif ($filter['tradeType'] && $filter['tradeType'] == 8) {
            $query->whereIn('trade_type', [6, 8]);
        } elseif ($filter['tradeType']) {
            $query->where('trade_type', $filter['tradeType']);
        }

        if ($filter['tradeSubType']) {
            $query->where('trade_sub_type', $filter['tradeSubType']);
        }

        if ($filter['startDate']) {
            $query->where('created_at', '>=', $filter['startDate']);
        }

        if ($filter['endDate']) {
            $query->where('created_at', '<=', $filter['endDate']." 23:59:59");
        }

        if ($filter['foreignOrderNo']) {
            $orderNos = GameLevelingOrder::where('channel_order_trade_no', $filter['foreignOrderNo'])->pluck('trade_no');
            $query->whereIn('trade_no', $orderNos);
        }
        return $query;
    }
}
