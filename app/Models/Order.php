<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Revisionable\RevisionableTrait;

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
        if (isset($filters['no'])) {
            $query->where('no', $filters['no']);
        } elseif (isset($filters['foreignOrderNo'])) {
            $query->where('foreign_order_no', $filters['foreignOrderNo']);
        } else {
            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['creatorPrimaryUserId'])) {
                $query->where('creator_primary_user_id', $filters['creatorPrimaryUserId']);
            }

            if (isset($filters['gainerPrimaryUserId'])) {
                $query->where('gainer_primary_user_id', $filters['gainerPrimaryUserId']);
            }

            if ($filters['serviceId']) {
                $query->where('service_id', $filters['serviceId']);
            }

            if ($filters['gameId']) {
                $query->where('game_id', $filters['gameId']);
            }

            if (isset($filters['source'])) {
                $query->where('status', $filters['source']);
            }

            if (isset($filters['startDate']) &&  empty($filters['startDate'])) {
                $query->where('created_at', '>=', $filters['startDate']);
            }

            if (isset($filters['endDate']) && empty($filters['endDate'])) {
                $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
            }

            if (isset($filters['endDate']) && isset($filters['startDate'])) {
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

    /**
     * 发单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creatorUser()
    {
        return $this->hasOne(User::class, 'id', 'creator_primary_user_id');
    }

    /**
     * 接单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gainerUser()
    {
        return $this->hasOne(User::class, 'id', 'gainer_primary_user_id');
    }
}
