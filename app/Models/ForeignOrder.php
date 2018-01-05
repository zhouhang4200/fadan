<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForeignOrder extends Model
{
    protected $fillable = ['channel',
        'channel_name',
        'order_time',
        'kamen_order_no',
        'foreign_order_no',
        'foreign_goods_id',
        'single_price',
        'total_price',
        'wang_wang',
        'tel',
        'details',
        'qq',
    ];

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['no'])) {
            $query->where('foreign_order_no', $filters['no']);
        }  else {

            if ($filters['wangWang']) {
                $query->where('wang_wang', $filters['wangWang']);
            }

            if (isset($filters['startDate']) &&  !empty($filters['startDate'])) {
                $query->where('created_at', '>=', $filters['startDate']);
            }

            if (isset($filters['endDate']) && !empty($filters['endDate'])) {
                $query->where('created_at', '<=', $filters['endDate']." 23:59:59");
            }

            if (isset($filters['endDate']) && isset($filters['startDate'])) {
                $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']." 23:59:59"]);
            }
        }
    }

    public function getDetailsAttribute($value)
    {
        return json_decode($value);
    }

    public function setDetailsAttribute($value)
    {
        return $this->attributes['details'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'foreign_order_no', 'foreign_order_no');
    }
}
