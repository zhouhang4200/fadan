<?php
namespace App\Repositories\Backend;

use DB, Auth;
use App\Models\GoodsTemplateWidgetValue;

/**
 * Class GoodsTemplateWidgetValueRepository
 * @package App\Repositories\Backend
 */
class GoodsTemplateWidgetValueRepository
{
    /**
     * @param $widgetId
     * @param $widgetDisplayName
     * @return array
     */
    public function getValue($widgetId, $widgetDisplayName = false)
    {
        $valueGroup = [];

        $values = GoodsTemplateWidgetValue::where('goods_template_widget_id', $widgetId)->get();

        if ($widgetDisplayName != false) {
            // 拼成 | 线分隔
            $v = '';
            foreach ($values as $i) {
                $v .= $i->field_value . '|';
            }
            // 拼装输出数据
            $valueGroup[] = [
                'parent_id' => 0,
                'parent_name' => $widgetDisplayName,
                'value' =>  $v,
            ];
        } else {

            foreach ($values as $item) {
                // 拼装输出数据
                $valueGroup[] = [
                    'parent_id' => $item->id,
                    'parent_name' => $item->field_value,
                    'value' =>  '',
                ];
            }
        }

        return $valueGroup;
    }

    /**
     * @param $fieldParentId
     * @return array
     */
    public function getValueGroup($fieldParentId)
    {
        $valueGroup = [];

        $values = GoodsTemplateWidgetValue::where('goods_template_widget_id', $fieldParentId)->get();

        foreach ($values as $item) {
            // 找到所有值
            $value = GoodsTemplateWidgetValue::where('parent_id', $item->id)->get();

            // 拼成 | 线分隔
            $v = '';
            foreach ($value as $i) {
                $v .= $i->field_value . '|';
            }
            // 拼装输出数据
            $valueGroup[] = [
                'parent_id' => $item->id,
                'parent_name' => $item->field_value,
                'value' =>  $v,
            ];
        }
        return $valueGroup;
    }
}
