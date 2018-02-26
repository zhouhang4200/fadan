<?php
namespace App\Repositories\Frontend;

use Auth;
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

    /**
     * 获取指定模版的所有组件
     * @param $templateId
     * @return mixed
     */
    public function getTemplateAllWidgetByTemplateId($templateId)
    {
        return GoodsTemplateWidget::where('goods_template_id', $templateId)->with('values')->orderBy('field_sortord', 'ASC')->get();
    }

    /**
     * @param $widgetId
     * @return mixed
     */
    public function getTemplateWidgetById($widgetId)
    {
        return GoodsTemplateWidget::find($widgetId);
    }

    /**
     * 获取所有组件通过模版ID
     * @param  integer $templateId 模版ID
     */
    public function getWidgetBy($templateId)
    {
        return GoodsTemplateWidget::select('id', 'field_type', 'field_display_name', 'field_parent_id', 'field_name',
            'display_form', 'field_required', 'help_text', 'verify_rule', 'display', 'field_default_value')
            ->where('goods_template_id', $templateId)
            ->where('display', 1)
            ->orderBy('field_sortord')
            ->with([
                'values' => function($query){
                    $query->select('goods_template_widget_id', 'field_value', 'id')
                        ->where('user_id', 0);
                },
                'userValues' => function($query) {
                    $query->select('goods_template_widget_id', 'field_value', 'id')
                        ->where('user_id', Auth::user()->getPrimaryUserId());
                }
            ])
            ->get();
    }
}
