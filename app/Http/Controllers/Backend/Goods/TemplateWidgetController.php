<?php

namespace App\Http\Controllers\Backend\Goods;

use Auth, Config, \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;

/**
 * Class TemplateController
 * @package App\Http\Controllers\Backend
 */
class TemplateWidgetController extends Controller
{
    /**
     * 获取指定组件
     * @param Request $request
     */
    public function show(Request $request)
    {
        return GoodsTemplateWidget::where('id', $request->id)->first();
    }

    /**
     * 获取指定模版的所有组件
     * @param Request $request
     * @param  int $templateId 模版ID
     * @return mixed
     */
    public function showAll(Request $request, $templateId)
    {
        return GoodsTemplateWidget::where('goods_template_id', $templateId)->orderBy('field_sort', 'ASC')->get();
    }

    /**
     * 新增组件
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $data = $request->data;
        $data['admin_user_id'] = Auth::user()->id;
        try {
            GoodsTemplateWidget::create($data);
        } catch (Exception $exception) {
            return jsonMessages(0, '添加失败');
        }
        return jsonMessages(1, '添加成功');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        try {
            GoodsTemplateWidget::where('id', $request->id)->update($request->data);
        } catch (Exception $exception) {
            return jsonMessages(0, '添加失败');
        }
        return jsonMessages(1, '添加成功');
    }

    /**
     * 删除组件
     * @param Request $request
     * @return string
     */
    public function destroy(Request $request)
    {
        try {
            GoodsTemplateWidget::destroy($request->id);
        } catch (Exception $exception) {
            return jsonMessages(0, '删除失败');
        }
        return jsonMessages(1, '删除成功');
    }

    /**
     * 获取指定模版ID的所有 select 组件
     * @param Request $request
     */
    public function showSelectWidgetByGoodsTemplateId(Request $request)
    {
        return GoodsTemplateWidget::where(['goods_template_id' => $request->id, 'field_type' => 2])->get();
    }

    /**
     * 获取指定父级件的选中值
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showSelectValueByParentId(Request $request)
    {
        $widgetValue = GoodsTemplateWidget::where(['field_parent_id' => $request->parent_id])->first();

        $valueArr = explode(',', $widgetValue->field_value);

        return response()->json(explode('|', $valueArr[$request->id]));
    }
}