<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderBasicData extends Model
{
    protected $fillable = [
    	'tm_status', 'tm_income', 'revoke_creator', 'arbitration_creator', 'order_finished_at',
		'consult_amount', 'consult_deposit', 'consult_poundage', 'creator_judge_income', 'creator_judge_payment',
		'order_no', 'status', 'client_wang_wang', 'customer_service_name', 'game_id',
		'game_name', 'creator_user_id', 'creator_primary_user_id', 'gainer_user_id', 'gainer_primary_user_id',
		'price', 'security_deposit', 'efficiency_deposit', 'original_price', 'order_created_at', 'is_repeat',
		'third', 'date', 'foreign_order_no', 'pay_amount'
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
            $query->whereIn('creator_user_id', $filters['userIds']);
        }

        if ($filters['third']) {
            $query->where('third', $filters['third']);
        }

        if ($filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $addDate = Carbon::parse($filters['endDate'])->addDays(1)->toDateString();
            
            $query->where('date', '>=', $filters['startDate'])->where('date', '<', $addDate);
        }
        return $query;
    }

    /**
     * 平台统计信息筛选
     * @param  [type] $query   [description]
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public static function scopeFilterBaby($query, $filters = [])
    {
        if ($filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $addDate = Carbon::parse($filters['endDate'])->addDays(1)->toDateString();
            
            $query->where('date', '>=', $filters['startDate'])->where('date', '<', $addDate);
        }
        return $query;
    }
}
