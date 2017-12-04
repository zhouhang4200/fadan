<?php

namespace App\Http\Controllers\Frontend\Order;

use App\Models\GoodsTemplate;
use App\Repositories\Frontend\OrderRepository;
use \Exception;
use App\Models\Goods;
use App\Repositories\Frontend\GameRepository;
use App\Repositories\Frontend\ServiceRepository;
use App\Repositories\Frontend\UserGoodsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Goods
 */
class OrderController extends Controller
{
    /**
     * 接的订单
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receive(Request $request,
        OrderRepository $orderRepository,
        ServiceRepository $serviceRepository,
        GameRepository $gameRepository
    )
    {
        $status = $request->status;
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();

        $orders  = $orderRepository->search(compact('serviceId', 'gameId', 'status', 'startDate', 'endDate'), 1);

        return view('frontend.order.receive', compact('status', 'orders', 'services', 'games', 'serviceId', 'gameId', 'startDate', 'endDate'));
    }

    /**
     * 发出的订单
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param ServiceRepository $serviceRepository
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function send(Request $request,
                            OrderRepository $orderRepository,
                            ServiceRepository $serviceRepository,
                            GameRepository $gameRepository
    )
    {
        $status= $request->status;
        $serviceId = $request->service_id;
        $gameId = $request->game_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $services = $serviceRepository->available();
        $games  = $gameRepository->available();
        $orders  = $orderRepository->search(compact('serviceId', 'gameId', 'status', 'startDate', 'endDate'), 2);

        return view('frontend.order.send', compact('status', 'orders', 'services', 'games', 'serviceId', 'gameId', 'startDate', 'endDate'));
    }
}
