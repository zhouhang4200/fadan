<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * @package App\Models
 */
class Order extends Model
{
    /**
     * 订单详情
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_no', 'no');
    }

    /**
     * 订单操作历史记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_no', 'no');
    }

    /**
     * 订单资金流水
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    /**
     * 订单平台资金流水
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['no']) {
            $query->where('no', $filters['no']);
        } elseif ($filters['foreignOrderNo']) {
            $query->where('foreign_order_no', $filters['foreignOrderNo']);
        } else {
            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            if ($filters['creatorPrimaryUserId']) {
                $query->where('creator_primary_user_id', $filters['creatorPrimaryUserId']);
            }

            if ($filters['gainerPrimaryUserId']) {
                $query->where('gainer_primary_user_id', $filters['gainerPrimaryUserId']);
            }

            if ($filters['serviceId']) {
                $query->where('service_id', $filters['serviceId']);
            }

            if ($filters['gameId']) {
                $query->where('game_id', $filters['gameId']);
            }

            if ($filters['source']) {
                $query->where('status', $filters['source']);
            }

            if ($filters['startDate'] && empty($filters['endDate'])) {
                $query->where('created_at', '>=', $filters['startDate']);
            }

            if ($filters['endDate'] && empty($filters['startDate'])) {
                $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
            }

            if ($filters['endDate'] && $filters['startDate']) {
                $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
            }
        }


    }

    /**
     * 订单发罚单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function punish()
    {
        return $this->hasOne(Punish::class, 'order_id', 'no');
    }

    /**
     * 订单外部订单关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function foreignOrder()
    {
        return $this->belongsTo(ForeignOrder::class, 'foreign_order_no', 'foreign_order_no');
    }
}
