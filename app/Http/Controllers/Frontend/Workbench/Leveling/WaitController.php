<?php
namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use DB, Order, Exception, Asset, Redis, Excel;
use App\Models\TaobaoTrade;
use App\Events\NotificationEvent;
use Illuminate\Http\Request;
use App\Services\RedisConnect;
use App\Http\Controllers\Controller;
use App\Repositories\Frontend\GameRepository;


/**
 * 代练待发订单
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class WaitController extends Controller
{
    /**
     * 待发单
     * @param GameRepository $gameRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(GameRepository $gameRepository)
    {
        $games = $gameRepository->availableByServiceId(4);
        $orderRedis = RedisConnect::order();
        $sort = $orderRedis->get('wait:sort:' . auth()->user()->id) ?? 'asc';

        return view('frontend.v1.workbench.leveling.wait', compact('games', 'sort'));
    }

    /**
     * 获取待发订单数据
     * @param Request $request
     * @return array
     */
    public function orderList(Request $request)
    {
        $orderRedis = RedisConnect::order();
        $sort = $orderRedis->get('wait:sort:' . auth()->user()->id) ?? 'asc';

        $tid = $request->tid;
        $status = $request->input('status', 0);
        $buyerNick = $request->buyer_nick;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $gameId = $request->game_id;
        $type = $request->type;

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

        $orderCount =  TaobaoTrade::select(\DB::raw('handle_status, count(1) as count'))
            ->where('user_id', auth()->user()->getPrimaryUserId())
            ->where('trade_status', '!=', 2)->groupBy('handle_status')->pluck('count', 'handle_status');

        $a1 = [0 => 0, 1=> 0, 2 => 0];
        $a2 = $orderCount->toArray();

        foreach ($a1 as $key => $item) {
            if (!isset($a2[$key])) {
                $a2[$key] = 0;
            }
        }


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
            'code' => 0,
            'msg' => '',
            'count' => $orders->total(),
            'data' => $orderList,
            'status_count' =>  $a2
        ];
    }

    /**
     * 修改待发订单状态
     * @param Request $request
     */
    public function update(Request $request)
    {
        $status = $request->status;

        if (in_array($status, [0, 2])) {
            TaobaoTrade::where('id', $request->id)
                ->where('user_id', auth()->user()->getPrimaryUserId())
                ->update(['handle_status' => $status]);
        }
    }

    /**
     * 修改待发单备注
     * @param Request $request
     */
    public function remark(Request $request)
    {
        try {
            TaobaoTrade::where('id', $request->id)
                ->where('user_id', auth()->user()->getPrimaryUserId())
                ->update(['remark' => $request->value]);
        } catch (\Exception $exception) {

        }
    }

    /**
     * 待发单加上处理时间
     * @param Request $request
     */
    public function time(Request $request)
    {
        $order = RedisConnect::order();
        $order->setex('wait:time:' . $request->tid, 60, date('Y-m-d H:i:s'));

        event(new NotificationEvent('waitOrderChange', [
            'user_id' => auth()->user()->getPrimaryUserId(),
        ]));
    }

    /**
     * 排序方式
     * @param Request $request
     */
    public function sort(Request $request)
    {
        $order = RedisConnect::order();
        $order->set('wait:sort:' . auth()->user()->id, $request->type);
    }
}