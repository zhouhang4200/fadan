<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PunishOrRewardRevision extends Model
{
    public $timestamps = true;

	protected $fillable = ['punish_or_reward_id', 'punish_or_reward_no', 'order_no', 'user_name', 'operate_style', 'before_value', 'after_value', 'detail'];

	public static function scopeFilter($query, $filters = [])
	{
		if ($filters['startDate'] && empty($filters['endDate'])) {

            $query->where('created_at', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {

            $query->where('created_at', '<=', $filters['endDate'] . " 23:59:59");
        }

        if ($filters['endDate'] && $filters['startDate']) {

            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate'] . " 23:59:59"]); 
        }

        if ($filters['orderNo']) {

            $query->where('order_no', $filters['orderNo']);
        }

        return $query->latest('created_at');
	}
}
