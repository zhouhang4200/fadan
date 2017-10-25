<?php
namespace App\Repositories\Backend;

use App\Models\GoodsTemplateWidget;

class GoodsTemplateWidgetRepository
{
    /**
     * 获取指定模版的所有下拉组件
     * @param $templateId
     * @return mixed
     */
    public function getSelectWidgetByGoodsTemplateId($templateId)
    {
        return GoodsTemplateWidget::where(['goods_template_id' => $templateId, 'field_type' => 2])->get();
    }

    public function getSelectValueByParentId($parentId)
    {
        return GoodsTemplateWidget::where(['field_parent_id' => $parentId])->first();
    }

    public function getTemplateAllWidgetByTemplateId($templateId)
    {
        return GoodsTemplateWidget::where('goods_template_id', $templateId)->orderBy('field_sortord', 'ASC')->get();
    }
}
