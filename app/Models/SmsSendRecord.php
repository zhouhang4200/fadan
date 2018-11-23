<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 短信发送记录
 * Class SmsSendRecord
 * @package App\Models
 */
class SmsSendRecord extends Model
{
    public $fillable = [
      'user_id',
      'order_no',
      'client_phone',
      'contents',
      'date',
      'foreign_order_no',
    ];

    public static function scopeFilter($query, $filter = [])
    {
        if (isset($filter['startDate']) && $filter['startDate']) {
            $query->where('created_at', '>=', $filter['startDate']);
        }

        if (isset($filter['endDate']) && $filter['endDate']) {
            $query->where('created_at', '<=', $filter['endDate'] . ' 23:59:59');
        }

        if (isset($filter['clientPhone']) && $filter['clientPhone']) {
            $query->where('client_phone', $filter['clientPhone']);
        }

        if (isset($filter['orderNo']) && $filter['orderNo']) {
            $query->where('foreign_order_no', $filter['orderNo'])->orWhere('third_order_no', $filter['orderNo']);
        }

        return $query;
    }

}
