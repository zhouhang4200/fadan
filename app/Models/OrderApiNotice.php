<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderApiNotice extends Model
{
	use SoftDeletes;

	protected $datas = ['deleted_at'];

	protected $fillable = [
		'order_no', 'status', 'source_order_no', 'operate', 'third', 'reason', 'order_created_at', 'function_name', 'created_at', 'updated_at'
	];

    public function order()
    {
    	return $this->belongsTo(Order::class, 'no', 'order_no');
    }

    /**
     * 后台查找
     * @param  [type] $query   [description]
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function scopeFilter($query, $filters = [])
    {
    	if (isset($filters['orderNo'])) {
    		$query->where('order_no', $filters['orderNo']);
    	}

    	if (isset($filters['status'])) {
    		$query->where('status', $filters['status']);
    	}

    	if (isset($filters['startDate'])) {
    		$query->where('order_created_at', '>=', $filters['startDate']);
    	}

    	if (isset($filters['endDate'])) {
    		$query->where('order_created_at', '<=', $filters['endDate']);
    	}
    	return $query;
    }
}
