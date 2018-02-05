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
    ];

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['startDate']) && $filter['startDate']) {
            $query->where('date', '>=', $filter['startDate']);
        }
        if (isset($filter['endDate']) && $filter['endDate']) {
            $query->where('date', '<=', $filter['endDate']);
        }
        if (isset($filter['startDate']) && isset($filter['endDate']) && $filter['startDate'] && $filter['endDate']) {
            $query->whereBetween('date', [$filter['startDate'], $filter['startDate']]);
        }
        return $query;
    }

}
