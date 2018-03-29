<?php

namespace App\Http\Controllers\Backend\Goods;

use App\Exceptions\CustomException;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\WidgetType;
use App\Repositories\Backend\GoodsTemplateWidgetRepository;
use App\Repositories\Backend\GoodsTemplateWidgetValueRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use Auth, Config, \Exception, DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;

/**
 * 模版组件
 * Class TemplateController
 * @package App\Http\Controllers\Backend
 */
class TemplateWidgetController extends Controller
{
    /**
     * @var GoodsTemplateWidgetRepository
     */
    private $goodsTemplateWidget;

    /**
     * TemplateWidgetController constructor.
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     */
    public function __construct(GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        $this->goodsTemplateWidget = $goodsTemplateWidgetRepository;
    }

    /**
     * @param Request $request
     * @param GoodsTemplateWidgetValueRepository $goodsTemplateWidgetValueRepository
     * @return mixed
     */
    public function show(Request $request, GoodsTemplateWidgetValueRepository $goodsTemplateWidgetValueRepository)
    {
        $widget = GoodsTemplateWidget::find($request->id);
        $widget['select'] = $this->goodsTemplateWidget->getSelectWidgetByGoodsTemplateId($request->template_id);

        // 如果父级不是0则获取到所有父级
        if ($widget->field_parent_id != 0) {
            $valueGroup = $goodsTemplateWidgetValueRepository->getValueGroup($widget->field_parent_id);
        } else {
            $valueGroup = $goodsTemplateWidgetValueRepository->getValue($widget->id, $widget->field_display_name);
        }
        // 所的组件类型
        $widget['type'] = WidgetType::all();
        $widget['value_group'] = $valueGroup;
        return $widget;
    }

    /**
     * 获取指定模版的所有组件
     * @param  int $templateId 模版ID
     * @return mixed
     */
    public function showAll($templateId)
    {
        return $this->goodsTemplateWidget->getTemplateAllWidgetByTemplateId($templateId);
    }

    /**
     * 新增组件
     * @param Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $data = $request->data;
        $tpeAndName = explode('-', $data['field_type_and_name']);
        $data['field_name'] = $tpeAndName[0];
        $data['field_type'] = $tpeAndName[1];
        $data['created_admin_user_id'] = Auth::user()->id;
        $data['display'] = !isset($data['display']) ? 2 : 1;

        try {
            DB::beginTransaction();

            $widget = GoodsTemplateWidget::create($data);

            $widgetValue = [];
            if ($data['field_type'] == 2) { // 只有下拉选项时才需对数据进行处理
                if (isset($data['field_value']) && !empty($data['field_value'])) { // 如果有下标field_value 则说明没有父级组件
                    $valueArr = array_filter(explode('|', $data['field_value']));
                    foreach ($valueArr as $v) {
                        $widgetValue[] = [
                            'goods_template_widget_id' => $widget->id,
                            'parent_id' => 0,
                            'user_id' => 0,
                            'field_name' => $data['field_name'],
                            'field_value' => $v,
                        ];
                    }
                } else { // 如果是有父级件则值的字段名为动态的 field_value_父级ID 的形式
                    // 查找出父级的值，组装值写入组件对应的值表中
                    $parent = GoodsTemplateWidgetValue::where('goods_template_widget_id', $data['field_parent_id'])->sharedLock()->get();
                    // 拼装数据
                    foreach ($parent as $item) {
                        $key = 'field_value_' . $item->id;
                        // 判断是否有对应的key 与 值
                        if (isset($data[$key]) && !empty($data[$key])) {
                            $tempValue = $data[$key];
                            $valueArr = array_filter(explode('|', $tempValue));
                            foreach ($valueArr as $v) {
                                $widgetValue[] = [
                                    'goods_template_widget_id' => $widget->id,
                                    'parent_id' => $item->id,
                                    'user_id' => 0,
                                    'field_name' => $data['field_name'],
                                    'field_value' => $v,
                                    'level' => ++$item->level,
                                ];
                            }
                        } else {
                            DB::rollBack();
                            return response()->ajax(0, '添加失败，选择了父级后需要为所有父级的项设置对应的值');
                        }
                    }
                }
                // 写入数据
                DB::table('goods_template_widget_values')->insert($widgetValue);
            } else if (in_array($data['field_type'], [3, 5])) {
                $valueArr = array_filter(explode('|', $data['field_value']));
                foreach ($valueArr as $v) {
                    $widgetValue[] = [
                        'goods_template_widget_id' => $widget->id,
                        'parent_id' => 0,
                        'user_id' => 0,
                        'field_name' => $data['field_name'],
                        'field_value' => $v,
                    ];
                }
                // 写入数据
                DB::table('goods_template_widget_values')->insert($widgetValue);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->ajax(0, '添加失败-' . $exception->getMessage());
        }
        DB::commit();
        return response()->ajax(1, '添加成功');
    }

    /**
     * 保存修改
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $data = $request->data;
        $data['field_required'] = !isset($data['field_required']) ? 2 : 1;
        $data['display'] = !isset($data['display']) ? 2 : 1;

        try {
            GoodsTemplateWidget::where('id', $data['id'])->update($data);
            return response()->ajax(1, '修改成功');
        } catch (CustomException $exception) {
            return response()->ajax(0, '修改失败');
        }

//        $tpeAndName = explode('-', $data['field_type_and_name']);
//
//        $updateData = [];
//        $updateData['id'] = $data['id'];
//        $updateData['field_name'] = $tpeAndName[0];
//        $updateData['field_type'] = $tpeAndName[1];
//        $updateData['field_value'] = $data['field_value'] ?? '';
//        $updateData['field_required'] = !isset($data['field_required']) ? 2 : 1;
//        $updateData['field_parent_id'] = !isset($data['field_parent_id']) ? 0 : $data['field_parent_id'];

        // 如果父级ID为空则删除当前ID所有值，将值重新写入
//        if ($updateData['field_parent_id'] == 0) {
//            try {
//                DB::beginTransaction();
//
//                GoodsTemplateWidget::where('id', $data['id'])->update($updateData);
//                // 找到所有关联到该ID的值
//                $widgets = GoodsTemplateWidgetValue::where('goods_template_widget_id', $updateData['id'])->orderBy('id')->get()->toArray();
//                // 将新值重新写入
//                if (in_array($updateData['field_type'], [2, 3])) { // 只有下拉选项时才需对数据进行处理
//
//                    // 遍历删除与更新关联的值
//                    $valueArr = array_filter(explode('|', $data['field_value']));
//                    $num = count($valueArr) - count($widgets);
//                    if ($num < 0) {
//                        for ($i = abs($num) + 1; $i< count($widgets); $i++) {
//                            GoodsTemplateWidgetValue::where('id', $widgets[$i]['id'])
//                                ->orWhere('parent_id', $widgets[$i]['id'])
//                                ->delete();
////                            GoodsTemplateWidgetValue::where('parent_id', $widgets[$i]['id'])->delete();
//                        }
//                    }
//                    foreach ($valueArr as $k => $v) {
//                        $widgetValue = [
//                            'goods_template_widget_id' => $updateData['id'],
//                            'parent_id' => 0,
//                            'user_id' => 0,
//                            'field_name' => $updateData['field_name'],
//                            'field_value' => $v,
//                        ];
//                        $insertResult = GoodsTemplateWidgetValue::create($widgetValue);
//
//                        if (isset($widgets[$k])) {
//                            // 删除等于当前ID的数据
//                            GoodsTemplateWidgetValue::where('id', $widgets[$k]['id'])->delete();
//                            // 更新关联当前ID的数值
//                            GoodsTemplateWidgetValue::where('parent_id', $widgets[$k]['id'])->update([
//                                'parent_id' => $insertResult->id
//                            ]);
//                        }
//                    }
//                } else {
//                    $widgetValue[] = [
//                        'goods_template_widget_id' => $updateData['id'],
//                        'parent_id' => 0,
//                        'user_id' => 0,
//                        'field_name' => $updateData['field_name'],
//                        'field_value' => $updateData['field_value'],
//                    ];
//                    DB::table('goods_template_widget_values')->insert($widgetValue);
//                }
//
//            } catch (\Exception $exception) {
//                DB::rollBack();
//                return jsonMessages(0, '修改失败');
//            }
//            DB::commit();
//
//        } else {
//            try {
//                DB::beginTransaction();
//                // 查找出父级的值，组装值写入组件对应的值表中
//                $parent = GoodsTemplateWidgetValue::where('goods_template_widget_id', $updateData['field_parent_id'])
//                    ->sharedLock()->get();
//                // 删除所有旧数据
//                GoodsTemplateWidgetValue::whereIn('parent_id', $parent->pluck('id')->toArray())->delete();
//                // 拼装数据
//                $widgetValue = [];
//                foreach ($parent as $item) {
//                    $key = 'field_value_' . $item->id;
//                    // 判断是否有对应的key 与 值
//                    if (isset($data[$key]) && !empty($data[$key])) {
//                        $tempValue = $data[$key];
//                        $valueArr = array_filter(explode('|', $tempValue));
//                        foreach ($valueArr as $v) {
//                            $widgetValue[] = [
//                                'goods_template_widget_id' => $updateData['id'],
//                                'parent_id' => $item->id,
//                                'user_id' => 0,
//                                'field_name' => $updateData['field_name'],
//                                'field_value' => $v,
//                            ];
//                        }
//                    } else {
//                        // 删除没有设置值的选择并删除关联
////                        GoodsTemplateWidgetValue::where('id', $item->id)
////                            ->orWhere('parent_id', $item->id)
////                            ->delete();
////                        DB::rollBack();
////                        return response()->ajax(0, '添加失败，选择了父级后需要为所有父级的项设置对应的值');
//                    }
//                }
//                DB::table('goods_template_widget_values')->insert($widgetValue);
//            } catch (\Exception $exception) {
//                DB::rollBack();
//                return jsonMessages(0, '修改失败');
//            }
//            DB::commit();
//        }
    }

    /**
     * 修改组件内容
     * @param Request $request
     */
    public function editOption(Request $request)
    {
        $widget = GoodsTemplateWidget::where('id', $request->id)->first();

        $options = [];
        $top = 1; // 是否只有一级
        // 如果没有父级ID则不需要去查子级
        if ($widget->field_parent_id == 0) {
            $widgetOption = GoodsTemplateWidgetValue::where('goods_template_widget_id', $request->id)
                ->get();

            foreach ($widgetOption as $item) {
                $options[] = [
                    'parent_id' => $item->id,
                    'field_value' => $item->field_value,
                    'child' => [],
                ];
            }
        } else { // 如果有父级，先找到父级，然后找对应的子项
            $top = 0; // 是否只有一级
            $widgetOptionParent = GoodsTemplateWidgetValue::where('goods_template_widget_id', $widget->field_parent_id)
                ->get();

            foreach ($widgetOptionParent as $item) {
                $child = GoodsTemplateWidgetValue::where('parent_id', $item->id)->get()->toArray();
                $options[$item->id] = [
                    'parent_id' => $item->id,
                    'field_value' => $item->field_value,
                    'child' => $child,
                ];
            }
        }
        return response()->ajax(1, 'success', ['options' => $options, 'top' => $top, 'widget_id' => $request->id]);
    }

    /**
     *  组件选项添加
     * @param Request $request
     * @return array
     */
    public function addOption(Request $request)
    {
        $value = GoodsTemplateWidget::where('id', $request->id)->first();

        $newValue = explode('|', $request->value);

        $newValueArr = [];
        foreach ($newValue as $item) {
            $newValueArr[] = [
              'goods_template_widget_id' => $request->id,
              'parent_id' => $request->parent_id,
              'field_name' => $value->field_name,
              'field_value' => $item,
              'level' => $value->level,
            ];
        }
        DB::table('goods_template_widget_values')->insert($newValueArr);

        return response()->ajax(1, '添加成功');
    }

    /**
     * 删除选项
     * @param Request $request
     */
    public function delOption(Request $request)
    {
        GoodsTemplateWidgetValue::destroy($request->id);
        return response()->ajax(1, '删除成功');
    }

    /**
     * 删除组件
     * @param Request $request
     * @return string
     */
    public function destroy(Request $request)
    {
        try {
            $child = GoodsTemplateWidget::where('field_parent_id', $request->id)->first();
            if ($child) {
                return response()->ajax(0, '有组件关联该组件，如要删除请先删除关联组件');
            }
            GoodsTemplateWidget::destroy($request->id);
            GoodsTemplateWidgetValue::where('goods_template_widget_id', $request->id)->delete();
        } catch (Exception $exception) {
            return jsonMessages(0, '删除失败');
        }
        return jsonMessages(1, '删除成功');
    }

    /**
     * 获取指定模版ID的所有 select 组件
     * @param Request $request
     * @return mixed
     */
    public function showSelectWidgetByGoodsTemplateId(Request $request)
    {
        return $this->goodsTemplateWidget->getSelectWidgetByGoodsTemplateId($request->id);
    }

    /**
     * 获取指定父级件的选中值
     * @param Request $request
     * @param GoodsTemplateWidgetValueRepository $goodsTemplateWidgetValueRepository
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidget
     * @return mixed
     */
    public function showSelectValueByParentId(Request $request, GoodsTemplateWidgetValueRepository $goodsTemplateWidgetValueRepository, GoodsTemplateWidgetRepository $goodsTemplateWidget)
    {
        $isEdit = $request->input('edit', 0);

        $widgetValue = [];

        if ($request->id != 0) {
            // 获取组件 用父级ID
            $widget = $goodsTemplateWidget->getTemplateWidgetById($request->id);
            $count = GoodsTemplateWidgetValue::where('goods_template_widget_id', $request->id)->groupBy('parent_id')->count();
            if ($count == 1) {
                $widgetValue = $goodsTemplateWidgetValueRepository->getValueGroup($request->id);
            } else if ($isEdit == 1) {
                $widgetValue = $goodsTemplateWidgetValueRepository->getValue($widget->id);
            } else {
                // 获取组件值
                $widgetValue = $goodsTemplateWidgetValueRepository->getValue($widget->id, $widget->field_dispay_name);
            }
        }
        return response()->ajax(1, '获取成功', $widgetValue);
    }

    /**
     * 获取下拉项的子项
     * @param Request $request
     */
    public function showSelectChild(Request $request)
    {
        return GoodsTemplateWidgetValue::where('parent_id', $request->parent_id)->get();
    }

    /**
     * 组件类型
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function type(Request $request)
    {
        $name = $request->name;
        $widgetType = WidgetType::paginate(10);

        return view('backend.goods.template.widget.index', compact('widgetType', 'name'));
    }

    /**
     * 添加组件类型
     * @param Request $request
     * @return string
     */
    public function add(Request $request)
    {
        try {
            WidgetType::create([
                'name' => $request->name,
                'type' => $request->type,
                'display_name' => $request->display_name,
            ]);
        } catch (Exception $exception) {
            return jsonMessages(0, '添加失败');
        }
        return jsonMessages(1, '添加成功');
    }

    /**
     * 预览 模版
     * @param integer $templateId
     * @param GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository
     * @return mixed
     */
    public function previewTemplate($templateId, GoodsTemplateWidgetRepository $goodsTemplateWidgetRepository)
    {
        // 获取对应的模版组件
        $template = $goodsTemplateWidgetRepository->getWidgetBy($templateId);
        return response()->ajax(1, 'success', ['template' => $template->toArray()]);
    }
}