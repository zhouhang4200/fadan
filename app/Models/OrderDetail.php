<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderDetail extends Model
{
    public $fillable = [
        'order_no',
        'creator_primary_user_id',
        'field_name',
        'field_display_name',
        'field_value',
    ];

    /**
     * 通过字段名与字段值找到订单号
     * @param string $fieldName 字段名
     * @param string $fieldValue 字段值
     * @param integer $type 类型 1 接单产账号 2 发单主账号
     * @return array 订单号
     */
    public static function findOrdersBy($fieldName, $fieldValue, $type = 2)
    {
        if ($type == 2) {
            return OrderDetail::where('creator_primary_user_id', Auth::user()->getPrimaryUserId())
            ->where('field_name_alias', $fieldName)
                ->where('field_value', $fieldValue)
                ->pluck('order_no')
                ->toArray();
        } else {
//            return OrderDetail::where('gainer_primary_user_id', Auth::user()->getPrimaryUserId())
            return OrderDetail::where('field_name', $fieldName)
                ->where('field_value', $fieldValue)
                ->pluck('order_no')
                ->toArray();
        }

    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'no');
    }
}
