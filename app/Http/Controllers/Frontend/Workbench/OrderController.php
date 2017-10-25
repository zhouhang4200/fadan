<?php

namespace App\Http\Controllers\Frontend\Workbench;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Order;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Extensions\Order\Operations\Create;

class OrderController extends Controller
{

    /**
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ServiceRepository $serviceRepository, GameRepository $gameRepository)
    {
        $services = $serviceRepository->available();
        $games = $gameRepository->available();
        return view('frontend.workbench.index', compact('services', 'games'));
    }

    /**
     * 创建订单
     * @param Request $request
     */
    public function create(Request $request)
    {
         Order::handle(new Create(1, 'taobao-123', 1, 1, 5.8, 12, []));
    }

    /**
     * 获取当前用户可下单的商品
     * @param Request $request
     */
    public function goods(Request $request)
    {

    }
}
