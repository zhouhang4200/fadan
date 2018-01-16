<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatistic extends Model
{
    protected $fillable = [
    	'date', 'user_id', 'parent_id', 'send_order_count', 'receive_order_count', 'complete_order_count', 'complete_order_rate', 'revoke_order_count', 'arbitrate_order_count', 'three_status_original_amount', 'complete_order_amount', 'two_status_payment', 'two_status_income', 'poundage', 'profit'
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['startDate']) && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if (isset($filters['endDate']) && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $query->whereBetween('date', [$filters['startDate'], $filters['endDate']]);
        }
        return $query;
    }
}
