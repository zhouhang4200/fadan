<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaobaoTrade extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['tid'])) {
            $query->where('tid', $filters['tid']);
        }  else {

            if ($filters['buyerNick']) {
                $query->where('buyer_nick', $filters['buyerNick']);
            }

            if (isset($filters['startDate']) &&  !empty($filters['startDate'])) {
                $query->where('created', '>=', $filters['startDate']);
            }
            if (isset($filters['status'])  && $filters['status'] != 99) {
                $query->where('handle_status', $filters['status']);
            }
            if (isset($filters['endDate']) && !empty($filters['endDate'])) {
                $query->where('created', '<=', $filters['endDate']." 23:59:59");
            }
        }
    }

}
