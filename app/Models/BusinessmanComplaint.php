<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessmanComplaint extends Model
{
    public $fillable = [
      'complaint_primary_user_id',
      'be_complaint_primary_user_id',
      'order_no',
      'amount',
      'remark',
    ];

    /**
     * 订单过滤
     * @param $query
     * @param array $filters
     */
    public static function scopeFilter($query, $filters = [])
    {
        if (isset($filters['orderNo']) && $filters['orderNo']) {
            $query->where('order_no', $filters['orderNo']);
        }
    }
}
