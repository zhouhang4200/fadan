<?php

namespace App\Http\Controllers\Frontend\Workbench;

use App\Models\Game;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Order;
use App\Extensions\Order\Operations\Create;

class OrderController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $services = Service::where('status', 1)->pluck('name', 'id');
        $games = Game::where('status', 1)->pluck('name', 'id');
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
