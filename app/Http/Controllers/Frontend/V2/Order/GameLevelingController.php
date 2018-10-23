<?php

namespace App\Http\Controllers\Frontend\V2\Order;

use App\Models\GameLevelingOrder;
use App\Http\Controllers\Controller;

/**
 * 游戏代练订单控制器
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class GameLevelingController extends Controller
{
    /**
     * 代练订单视图
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('frontend.v2.order.game-leveling.index');
    }

    /**
     * 获取代练订单集合
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function dataList()
    {
        return response(GameLevelingOrder::paginate(20));
    }

    /**
     * 查看订单详情
     */
    public function show()
    {

    }

    public function take()
    {

    }

    public function applyComplete()
    {

    }

    public function cancelComplete()
    {

    }

    public function complete()
    {

    }

    public function onSale()
    {

    }

    public function offSale()
    {

    }

    public function lock()
    {

    }

    public function cancelLock()
    {

    }

    public function anomaly()
    {

    }

    public function cancelAnomaly()
    {

    }

    public function applyConsult()
    {

    }

    public function cancelConsult()
    {

    }

    public function agreeConsult()
    {

    }

    public function rejectConsult()
    {

    }

    public function applyComplain()
    {

    }

    public function cancelComplain()
    {

    }

    public function applyCompleteImage()
    {

    }

    public function log()
    {

    }

    public function complainInfo()
    {

    }

    public function sendComplainMessage()
    {

    }

    public function message()
    {

    }

    public function sendMessage()
    {

    }

    public function messageList()
    {

    }

    public function deleteMessage()
    {

    }

    public function deleteAllMessage()
    {

    }
}
