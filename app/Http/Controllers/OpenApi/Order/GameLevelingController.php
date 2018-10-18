<?php

namespace App\Http\Controllers\OpenApi\Order;

use App\Models\GameLevelingChannelOrder;
use App\Models\GameLevelingOrder;
use App\Http\Controllers\Controller;

class GameLevelingController extends Controller
{
    /**
     * 下单
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $amount = 0;

        // 创建订单
        $order = GameLevelingChannelOrder::create([
            'trade_no' => generateOrderNo(),
            'channel_user_id' => request('channel_user_id'),
            'amount' => $amount,
        ]);

        // 临时记录其它订单信息
        return response(['trade_no' => $order->trade_no], 200);
    }
}
