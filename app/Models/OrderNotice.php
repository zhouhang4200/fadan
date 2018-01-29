<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderNotice extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
    	'creator_user_id', 'creator_primary_user_id', 'gainer_user_id', 'creator_user_name', 'order_no', 'complete', 'child_third_status',
    	'third_order_no', 'third', 'status', 'third_status', 'create_order_time', 'created_at', 'updated_at', 'amount', 'security_deposit', 'efficiency_deposit',
    ];

    protected $dates = ['deleted_at'];

    public static function scopeFilter($query, $filters = [])
    {
    	if ($filters['third']) {
    		$query->where('third', $filters['third']);
    	}

    	if (isset($filters['startDate']) && empty($filters['endDate'])) {
            $query->where('created_at', '>=', $filters['startDate']);
        }

        if (isset($filters['endDate']) && empty($filters['startDate'])) {
            $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
        }

        if (isset($filters['endDate']) && $filters['startDate']) {
            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
        }
        return $query;
    }

    public function order()
    {
    	return $this->belongsTo(Order::class, 'order_no', 'no');
    }
}
