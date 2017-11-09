<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_no', 'no');
    }

    /**
     * 外部订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function foreignOrder()
    {
        return $this->hasOne(ForeignOrder::class, 'foreign_order_id', 'foreign_order_no');

    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class, 'order_no', 'no');
    }

    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }

    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['status']) {

            $query->where('status', $filters['status']);
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

    public function punish()
    {
        return $this->hasOne(Punish::class, 'order_id', 'no');
    }

    public function foreignOrder()
    {
        return $this->belongsTo(ForeignOrder::class, 'foreign_order_no', 'foreign_order_id');
    }
}
