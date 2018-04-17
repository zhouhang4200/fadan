<?php

namespace App\Http\Controllers\Backend\Goods;

use App\Models\GoodsTemplateWidget;
use App\Models\WidgetType;
use App\Repositories\Backend\GameRepository;
use App\Repositories\Backend\ServiceRepository;
use Auth, Config, \Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Game;
use App\Models\Service;
use App\Models\GoodsTemplate;

/**
 * Class TemplateController
 * @package App\Http\Controllers\Backend
 */
class TemplateController extends Controller
{
    private $game;

    private $service;

    /**
     * TemplateController constructor.
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     */
    public function __construct(ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $this->game = $gameRepository;
        $this->service = $serviceRepository;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->name;
        $serviceId = $request->service_id;
        $gameId = $request->game_id;

        $goodsTemplates = GoodsTemplate::filter(compact('serviceId', 'gameId'))
            ->with(['createdAdmin', 'updatedAdmin', 'service', 'game'])
            ->orderBy('id', 'desc')
            ->paginate(30);

        $services = $this->service->available();
        $games = $this->game->available();

        return view('backend.goods.template.index', compact('goodsTemplates', 'name', 'services', 'games', 'serviceId', 'gameId'));
    }

    /**
     * @param $id
     */
    public function show($id)
    {
        $template = GoodsTemplate::find($id)->toArray();
        $template['services'] = $this->service->available();
        $template['games'] = $this->game->available();
        return $template;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config(Request $request, $id)
    {
        $filedName = WidgetType::all();
        $filedType = Config::get('goods.template.field_type');

        return view('backend.goods.template.show', compact('filedName', 'filedType'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->data;

            if (GoodsTemplate::exist($data['service_id'], $data['game_id'])) {
                return response()->json(['code' => 1, 'message' => '服务与游戏的组合已经创建了模版']);
            }
            $data['created_admin_user_id'] = Auth::user()->id;
            $data['updated_admin_user_id'] = Auth::user()->id;
            GoodsTemplate::create($data);
            return response()->json(['code' => 1, 'message' => '添加成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '添加失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $data = $request->data;

            if (GoodsTemplate::exist($data['service_id'], $data['game_id'], $id)) {
                return response()->json(['code' => 1, 'message' => '服务与游戏的组合已经创建了模版']);
            }

            $data['updated_admin_user_id'] = Auth::user()->id;
            GoodsTemplate::where('id', $id)->update($data);
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } catch (Exception $exception) {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $goodsTemplate = GoodsTemplate::find($request->id);
        if ($goodsTemplate) {
            $goodsTemplate->status = $request->status;
            $goodsTemplate->created_admin_user_id = Auth::user()->id;
            $goodsTemplate->save();
            return response()->json(['code' => 1, 'message' => '修改成功']);
        } else {
            return response()->json(['code' => 0, 'message' => '修改失败']);
        }
    }


    /**
     * 复制模版
     * @param Request $request
     */
    public function copyTemplate(Request $request)
    {
        $template = GoodsTemplate::find($request->id);
        // 复制模板数据
        $copyTemplate = $template->replicate()->toArray();
        // 创建模板
        $newGoodsTemplate = GoodsTemplate::create($copyTemplate);
        // 获取原模板的字段值
        $goodsTemplateWidgets = GoodsTemplateWidget::where('goods_template_id', $template->id)->get();

        $datas = [];
        foreach ($goodsTemplateWidgets as $k => $goodsTemplateWidget) {
            $datas[$k] = $goodsTemplateWidget->replicate()->toArray();
            $datas[$k]['goods_template_id'] = $newGoodsTemplate['id'];
        }
        GoodsTemplateWidget::insert($datas);
        // 找到区id
        $reasonId = GoodsTemplateWidget::where('goods_template_id', $newGoodsTemplate['id'])
        ->where('field_name', 'region')->value('id');
        // 更新服的父id为区id
        GoodsTemplateWidget::where('goods_template_id', $newGoodsTemplate['id'])
        ->where('field_name', 'serve')->update(['field_parent_id' => $reasonId]);

        return response()->ajax(1, '复制成功!');
    }
}