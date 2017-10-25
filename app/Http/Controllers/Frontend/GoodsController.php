<?php

namespace App\Http\Controllers\Frontend;

use App\Models\GoodsTemplate;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class GoodsController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class GoodsController extends Controller
{
    /**
     * @param Request $request
     * @param UserGoodsRepository $userGoodsRepository
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(
        Request $request,
        UserGoodsRepository $userGoodsRepository,
        ServiceRepository $serviceRepository,
        GameRepository $gameRepository
    )
    {
        $serviceId = $request->get('service_id');
        $gameId = $request->game_id;
        $foreignGoodsId = $request->foreign_goods_id;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();
        $goods  = $userGoodsRepository->getList($serviceId, $gameId, $foreignGoodsId);

        return view('frontend.goods.index', compact('goods', 'services', 'serviceId', 'games', 'gameId', 'foreignGoodsId'));
    }

    /**
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $services = $serviceRepository->available();
        $games = $gameRepository->available();

        return view('frontend.goods.create', compact('services', 'games'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $goodsData = $request->data;

        try {
            $goodsData['user_id'] = Auth::user()->getPrimaryUserId();
            $goodsData['goods_template_id'] = GoodsTemplate::getTemplateId($goodsData['service_id'], $goodsData['game_id']);
            $goodsData['display'] = (isset($goodsData['display']) && $goodsData['display'] == 'on') ? 1 : 0;
            Goods::create($goodsData);
            return response()->ajax('1', '添加成功');
        } catch (Exception $exception) {
            return response()->ajax(0, '添加失败');
        }
    }

    /**
     *
     */
    public function destroy()
    {

    }

}