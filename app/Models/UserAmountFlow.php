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

    public static function scopeAdminFilter($query, $filters = [])
    {
        if (isset($filters['userId']) && !empty($filters['userId'])) {
            $query->where('user_id', $filters['userId']);
        };

        if (isset($filters['tradeNo']) && !empty($filters['tradeNo'])) {
            $query->where('trade_no', $filters['tradeNo']);
        };

        if (isset($filters['tradeType']) && !empty($filters['tradeType'])) {
            $query->where('trade_type', $filters['tradeType']);
        };

        if (isset($filters['tradeSubtype']) && !empty($filters['tradeSubtype'])) {
            $query->where('trade_subtype', $filters['tradeSubtype']);
        };

        if (isset($filters['timeStart']) && !empty($filters['timeStart'])) {
            $query->where('created_at', '>=', $filters['timeStart']);
        };

        if (isset($filters['timeEnd']) && !empty($filters['timeEnd'])) {
            $query->where('created_at', '<=', $filters['timeEnd']);
        };

        return $query;
    }
}
