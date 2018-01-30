<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformStatistic extends Model
{
    protected $fillable = [
    	'date',
        'user_id',
        'parent_id',
        'third',
        'game_id',
        'order_count',
        'client_wang_wang_count',
        'distinct_client_wang_wang_count',
        'done_order_use_time',
        'receive_order_count',
        'complete_order_count',
        'complete_order_amount',
        'revoke_order_count',
        'arbitrate_order_count',
        'done_order_count',
        'done_order_security_deposit',
        'done_order_efficiency_deposit',
        'done_order_original_amount',
        'done_order_amount',
        'revoke_payment',
        'arbitrate_payment',
        'revoke_income',
        'arbitrate_income',
        'poundage',
        'user_profit',
        'platform_profit',
    ];

    /**
     * 平台统计信息筛选
     * @param  [type] $query   [description]
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['userIds']) {
            $query->whereIn('user_id', $filters['userIds']);
        }

        if ($filters['third']) {
            $query->where('third', $filters['third']);
        }

        if ($filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }

        if (isset($filters['startDate']) && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if (isset($filters['endDate']) && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if (isset($filters['endDate']) && $filters['startDate']) {
            $query->whereBetween('date', [$filters['startDate'], $filters['endDate']]);
        }
        return $query;
    }
}
