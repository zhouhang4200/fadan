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
        return GoodsTemplateWidget::where('goods_template_id', $templateId)->orderBy('filed_sort', 'ASC')->get();
    }

    /**
     * 新增组件
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        try {
            GoodsTemplateWidget::create($request->data);
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
}