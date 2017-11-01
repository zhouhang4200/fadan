<?php

namespace App\Http\Controllers\Frontend\Goods;

use App\Models\GoodsTemplate;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class GoodsAuthController extends Controller
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
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $foreignGoodsId = $request->foreign_goods_id;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();
        $goods  = $userGoodsRepository->getList($serviceId, $gameId, $foreignGoodsId);

        return view('frontend.goods.auth.index', compact('goods', 'services', 'serviceId', 'games', 'gameId', 'foreignGoodsId'));
    }
}
