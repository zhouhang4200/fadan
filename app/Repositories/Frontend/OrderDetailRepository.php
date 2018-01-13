<?php
namespace App\Repositories\Frontend;

use DB;
use Auth;
use App\Models\OrderDetail;

/**
 * Class GoodsRepository
 * @package App\Repositories\Frontend
 */
class OrderDetailRepository
{
    /**
     * 根据订单号与字段名字更新相关的值
     * @param $orderNo
     * @param $fieldName
     * @param $fieldValue
     */
    public static function updateByOrderNo($orderNo, $fieldName, $fieldValue)
    {
        return OrderDetail::where('order_no', $orderNo)->where('field_name', $fieldName)->update(['field_name' => $fieldValue]);
    }
}
