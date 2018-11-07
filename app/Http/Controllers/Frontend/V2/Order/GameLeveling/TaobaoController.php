<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use DB;
use Auth;
use App\Models\TaobaoTrade;
use App\Services\RedisConnect;
use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;

/**
 * 游戏代练淘宝（待发）订单控制器
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class TaobaoController extends Controller
{
    /**
     * 获取待发订单数据
     * @return array
     */
    public function index()
    {
        $orderRedis = RedisConnect::order();
        $sort = $orderRedis->get('wait:sort:' . auth()->user()->id) ?? 'asc';

        $tid = request()->tid;
        $status = request()->input('status', 0);
        $buyerNick = request()->buyer_nick;
        $startDate = request()->start_date;
        $endDate = request()->end_date;
        $gameId = request()->game_id;
        $type = request()->type;

        $orders = TaobaoTrade::filter(compact('tid', 'buyerNick', 'startDate', 'endDate', 'status', 'gameId', 'type'))
            ->where('user_id', auth()->user()->getPrimaryUserId())
            ->where('service_id', 4)
            ->where('trade_status', '!=', 2)
            ->orderBy('id', $sort)
            ->with([
                'order' => function($query){
                    $query->groupBy('id');
                }
            ])
            ->paginate(30);


        $orderList = [];
        foreach ($orders->items() as $key => $item) {
            $orderList[] = [
                'id' => $item->id,
                'tid' => $item->tid,
                'seller_nick' => $item->seller_nick,
                'handle_status' => $item->handle_status,
                'trade_status' => $item->getTradeStatusText(),
                'order_status' => $item->getOrderStatusText(),
                'game_name' => $item->game_name,
                'game_id' => $item->game_id,
                'buyer_nick' => $item->buyer_nick,
                'price' => $item->price,
                'num' => $item->num,
                'payment' => $item->payment,
                'created' => $item->created,
                'remark' => $item->remark,
                'time' => $orderRedis->get('wait:time:' . $item->tid),
            ];
        }

        return [
            'total' => $orders->total(),
            'data' => $orderList,
        ];
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show()
    {
        // 获取淘宝订单数据
        $taobaoOrder = TaobaoTrade::select([
            'tid',
            'seller_nick',
            'num',
            'buyer_nick',
            'buyer_message',
            'price',
            'payment',
            'trade_status',
            'created'
        ])
            ->where('tid', request('tid'))->first();
        return response($taobaoOrder);
    }

    /**
     * 对应订单状态的数量
     */
    public function statusQuantity()
    {
        return TaobaoTrade::select(\DB::raw('handle_status, count(1) as quantity'))
            ->where('user_id', auth()->user()->getPrimaryUserId())
            ->where('trade_status', '!=', 2)
            ->groupBy('handle_status')
            ->pluck('quantity', 'handle_status')
            ->toArray();
    }

    /**
     * 修改待发订单状态
     */
    public function update()
    {
        $status = request()->status;

        if (in_array($status, [0, 2])) {
            TaobaoTrade::where('id', request()->id)
                ->where('user_id', auth()->user()->getPrimaryUserId())
                ->update(['handle_status' => $status]);
        }
    }

    /**
     * 修改待发单备注
     */
    public function remark()
    {
        try {
            TaobaoTrade::where('id', request()->id)
                ->where('user_id', auth()->user()->getPrimaryUserId())
                ->update(['remark' => request()->value]);
        } catch (\Exception $exception) {

        }
    }

    /**
     * 待发单加上处理时间
     */
    public function time()
    {
        $order = RedisConnect::order();
        $order->setex('wait:time:' . request()->tid, 60, date('Y-m-d H:i:s'));

        event(new NotificationEvent('waitOrderChange', [
            'user_id' => auth()->user()->getPrimaryUserId(),
        ]));
    }

    /**
     * 排序方式
     */
    public function sort()
    {
        $order = RedisConnect::order();
        $order->set('wait:sort:' . auth()->user()->id, request()->type);
    }
}