<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Goods;
use App\Models\Service;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class GoodsController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class GoodsController extends Controller
{
    public function index(Request $request, UserGoodsRepository $userGoodsRepository, ServiceRepository $serviceRepository)
    {
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $foreignGoodsId = $request->foreign_goods_id;

        $services = $serviceRepository->available();
        $goods  = $userGoodsRepository->getList($serviceId, $gameId, $foreignGoodsId);

        return view('frontend.goods.index', compact('services', 'serviceId', 'goods'));
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
     */
    public function store(Request $request)
    {

    }

    /**
     *
     */
    public function destroy()
    {

    }

}
