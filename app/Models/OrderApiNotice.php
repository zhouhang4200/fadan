<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * 前台各个平台操作失败生成报警
     * @return [type] [description]
     */
    public static function createNotice($third, $reason, $functionName, $datas)
    {
    	if (isset($datas) && count($datas) > 0 && ! empty($third) && ! empty($functionName) && ! empty($reason)) {
			$arr                     = [];
			$arr['order_no']         = $datas['order_no'] ?? 0;
			$arr['source_order_no']  = $datas['source_order_no'] ?? 0;
			$arr['status']           = $datas['order_status'] ?? 0;
			$arr['operate']          = config('leveling.operate')[$functionName] ?? 0;
			$arr['third']            = $third ?? 0;
			$arr['reason']           = $reason ?? 0;
			$arr['order_created_at'] = $datas['order_created_at'] ?? 0;
			$arr['function_name']    = $functionName ?? 0;
			$arr['created_at']       = Carbon::now()->toDateTimeString();
			$arr['updated_at']       = Carbon::now()->toDateTimeString();

	    	$res = static::updateOrCreate(['order_no' => $datas['order_no'], 'third' => $third, 'function_name' => $functionName], $arr);
    	}
    }
}
