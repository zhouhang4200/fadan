<?php
namespace App\Repositories\Frontend;

use App\Exceptions\CustomException;
use App\Models\GoodsTemplateWidget;
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

    /**
     * 根据订单号获取对的值
     * @param $orderNo
     * @return mixed
     */
    public static function getByOrderNo($orderNo)
    {
        return OrderDetail::where('order_no', $orderNo)->pluck('field_value', 'field_name');
    }

    /**
     * 创建订单详情
     * @param $templateId
     * @param $orderNo
     * @param $values
     * @throws CustomException
     */
    public static function create($templateId, $orderNo, $values)
    {
        $widget = GoodsTemplateWidget::where('goods_template_id', $templateId)->pluck('field_display_name', 'field_name');

        foreach ($widget as $k => $v) {

            $orderDetail = new OrderDetail;
            $orderDetail->order_no = $orderNo;
            $orderDetail->field_name = $k;
            $orderDetail->field_display_name = $v;
            $orderDetail->field_value = $values[$k] ?? '';
            $orderDetail->creator_primary_user_id = Auth::user()->getPrimaryUserId();

            if (!$orderDetail->save()) {
                throw new CustomException('详情记录失败');
            }
        }
    }
}
