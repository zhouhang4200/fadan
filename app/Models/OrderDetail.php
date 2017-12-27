<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderDetail extends Model
{

    /**
     * 通过字段名与字段值找到订单号
     * @param string $fieldName 字段名
     * @param string $fieldValue 字段值
     * @return array 订单号
     */
    public static function findOrdersBy($fieldName, $fieldValue)
    {
        return OrderDetail::where('creator_primary_user_id', Auth::user()->getPrimaryUserId())
            ->where('field_name', $fieldName)
            ->where('field_value', $fieldValue)
            ->pluck('order_no')
            ->toArray();
    }
}
