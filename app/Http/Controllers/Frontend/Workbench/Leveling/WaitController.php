<?php

namespace App\Http\Controllers\Frontend\Workbench\Leveling;

use App\Extensions\Dailian\Controllers\Arbitrationing;
use App\Extensions\Dailian\Controllers\Complete;
use App\Models\AutomaticallyGrabGoods;
use App\Models\BusinessmanContactTemplate;
use App\Models\GameLevelingRequirementsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\UserSetting;
use App\Exceptions\AssetException;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Extensions\Order\Operations\CreateLeveling;
use App\Models\Game;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\OrderDetail;
use App\Models\Order as OrderModel;
use App\Models\OrderHistory;
use App\Models\SmsTemplate;
use App\Models\TaobaoTrade;
use App\Models\User;
use App\Repositories\Api\TaobaoTradeRepository;
use App\Repositories\Frontend\GoodsTemplateWidgetRepository;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Repositories\Frontend\OrderRepository;
use App\Repositories\Frontend\GoodsTemplateWidgetValueRepository;
use App\Repositories\Frontend\OrderHistoryRepository;
use App\Services\DailianMama;
use App\Services\Leveling\DD373Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\GoodsTemplate;

use DB, Order, Exception, Asset, Redis;
use App\Repositories\Frontend\GameRepository;
use App\Exceptions\CustomException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\LevelingConsult;
use App\Services\Show91;
use Excel;
use App\Exceptions\DailianException;
use App\Repositories\Frontend\OrderAttachmentRepository;
use App\Events\AutoRequestInterface;
use TopClient;
use TradeFullinfoGetRequest;
use App\Models\OrderAutoMarkup;

/**
 * 代练待发订单
 * Class OrderController
 * @package App\Http\Controllers\Frontend\Workbench
 */
class WaitController extends Controller
{
    /**
     * 待发单
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
    {
        $tid = $request->tid;
        $status = $request->input('status', 0);
        $buyerNick = $request->buyer_nick;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $games = GoodsTemplate::where('service_id', 4)
            ->leftJoin('games', 'goods_templates.game_id', '=', 'games.id')
            ->pluck('games.name', 'games.id');


        $orders = TaobaoTrade::filter(compact('tid', 'buyerNick', 'startDate', 'endDate', 'status'))
            ->where('user_id', auth()->user()->getPrimaryUserId())
            ->where('service_id', 4)
            ->where('trade_status', '!=', 2)
            ->orderBy('id', 'desc')
            ->paginate(30);

        $totalCount = TaobaoTrade::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('trade_status', '!=', 2)->count();
        $unDisposeCount = TaobaoTrade::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('handle_status', 0)->where('trade_status', '!=', 2)->count();
        $disposeCount = TaobaoTrade::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('handle_status', 1)->where('trade_status', '!=', 2)->count();
        $hideCount = TaobaoTrade::where('user_id', auth()->user()->getPrimaryUserId())
            ->where('handle_status', 2)->where('trade_status', '!=', 2)->count();

        return view('frontend.v1.workbench.leveling.wait')->with([
                'tid' => $tid,
                'status' => $status,
                'orders' => $orders,
                'buyerNick' => $buyerNick,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalCount' => $totalCount,
                'disposeCount' => $disposeCount,
                'unDisposeCount' => $unDisposeCount,
                'hideCount' => $hideCount,
                'games' => $games,
            ]
        );
    }

    /**
     * 获取待发订单数据
     * @param Request $request
     * @return array
     */
    public function orderList(Request $request)
    {
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
//            ->orderBy('id', 'desc')
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
                'buyer_nick' => $item->buyer_nick,
                'price' => $item->price,
                'num' => $item->num,
                'payment' => $item->payment,
                'created' => $item->created,
                'remark' => $item->remark,
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
}