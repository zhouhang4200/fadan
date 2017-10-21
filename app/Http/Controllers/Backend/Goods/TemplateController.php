<?php

namespace App\Http\Controllers\Backend\Goods;

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
     * 商品模版
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $name = $request->name;

        $goodsTemplates = GoodsTemplate::with(['createdAdmin', 'updatedAdmin', 'service', 'game'])
            ->orderBy('id', 'desc')
            ->paginate(30);

        $services = $this->service->available();
        $games = $this->game->available();

        return view('backend.goods.template.index', compact('goodsTemplates', 'name', 'services', 'games'));
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
        $filedName = Config::get('goodsTemplate.field_name');
        $filedType = Config::get('goodsTemplate.field_type');

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
     * @param Request $request
     * @param $Id
     */
    public function destroy(Request $request, $Id)
    {

    }
}