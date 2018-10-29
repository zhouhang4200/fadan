<?php

namespace App\Http\Controllers\Frontend\V2\Finance;

use App\Events\Punish;
use App\Models\Game;
use App\Models\UserAsset;
use Illuminate\Http\Request;
use App\Models\UserAssetDaily;
use App\Models\UserAmountFlow;
use App\Models\UserWithdrawOrder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TaobaoTrade;
use App\Repositories\Frontend\UserWithdrawOrderRepository;

class FinanceController extends Controller
{
    /**
     * 我的资产
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myAsset()
    {
        return view('frontend.v2.finance.my-asset');
    }

    /**
     * 我的资产接口数据
     * @return mixed
     */
    public function myAssetDataList()
    {
        $userAsset = UserAsset::where('user_id', Auth::user()->getPrimaryInfo()->id)->first();

        return response()->json([
            [
                'name' => '账户余额',
                'amount' => $userAsset->balance,
            ],
            [
                'name' => '冻结金额',
                'amount' => $userAsset->frozen,
            ],
            [
                'name' => '累计充值',
                'amount' => $userAsset->total_recharge,
            ],
            [
                'name' => '累计提现',
                'amount' => $userAsset->total_withdraw,
            ],
            [
                'name' => '累计收入',
                'amount' => $userAsset->total_income,
            ],
            [
                'name' => '累计支出',
                'amount' => $userAsset->total_expend,
            ],
        ]);
    }

    /**
     * 资产日报
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dailyAsset()
    {
        return view('frontend.v2.finance.daily-asset');
    }

    /**
     * 资产日报接口
     * @return mixed
     */
    public function dailyAssetDataList()
    {
        $startDate = request('date')[0];
        $endDate = request('date')[1];
        $filter = compact('startDate', 'endDate');

        return UserAssetDaily::filter($filter)->paginate(15);
    }

    /**
     * 资金流水
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function amountFlow()
    {
        return view('frontend.v2.finance.amount-flow');
    }

    /**
     * 新资金流水接口数据
     * @param Request $request
     * @return mixed
     */
    public function amountFlowDataList(Request $request)
    {
        $tradeNo = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $tradeSubType = $request->trade_sub_type;
        $startDate = $request->date[0];
        $endDate = $request->date[1];
        $foreignOrderNo = $request->channel_order_trade_no;
        $filter = compact('tradeNo', 'tradeType', 'tradeSubType', 'startDate', 'endDate', 'foreignOrderNo');

        return UserAmountFlow::filter($filter)->where('user_id', Auth::user()->getPrimaryUserId())->with('order')->paginate(15);
    }

    /**
     * 我的提现
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function myWithdraw()
    {
        return view('frontend.v2.finance.my-withdraw');
    }

    /**
     * 我的提现接口
     * @return mixed
     */
    public function myWithdrawDataList()
    {
        $startDate = request('date')[0];
        $endDate = request('date')[1];
        $status = request('status');
        $filter = compact('startDate', 'endDate', 'status');

        return UserWithdrawOrder::filter($filter)->paginate(15);
    }

    /**
     * 可提现金额
     * @return mixed
     */
    public function canWithdraw()
    {
        return UserAsset::where('user_id', Auth::user()->getPrimaryUserId())->value('balance');
    }

    /**
     * 发送提现申请
     * @param UserWithdrawOrderRepository $repository
     * @return \Illuminate\Http\JsonResponse
     */
    public function createWithdraw(UserWithdrawOrderRepository $repository)
    {
        $bool = event(new Punish(Auth::user()->getPrimaryUserId()));

        if ($bool) {
            return response()->json(['status' => 0, 'message' => '您还有罚单没有交清，请先交清罚单哦!']);
        }

        try {
            $repository->store(request('fee', 0), trim(request('remark', '无')) ?: config('withdraw.status')[1]);
        } catch (\Exception $e) {
            return response()->ajax(0, $e->getMessage());
        }
        return response()->ajax(1, '发送成功!');
    }

    /**
     * 财务订单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order()
    {
        $games = Game::get();
        return view('frontend.v2.finance.order', compact('games'));
    }

    /**
     * 财务订单接口
     * @param OrderRepository $orderRepository
     * @param GameRepository $gameRepository
     */
    public function orderDataList(OrderRepository $orderRepository, GameRepository $gameRepository)
    {
        $no = request('no');
        $customerServiceName = request('customer_service_name', 0);
        $gameId = request('game_id', 0);
        $status = request('status', 0);
        $wangWang = request('wang_wang');
        $urgentOrder = request('urgent_order', 0);
        $startDate = request('start_date');
        $endDate = request('end_date');
        $pageSize = request('limit', 15);

        $taobaoStatus = request('taobao_status', 0);
        $platform = request('platform', 0);
        $sellerNick = request('seller_nick', '');

        $game = $gameRepository->availableByServiceId(4);

        $orders = $orderRepository->levelingDataList($status, $no,  $taobaoStatus,  $gameId, $wangWang, $customerServiceName, $platform, $startDate, $endDate, $sellerNick, $pageSize);


        // 处理数据
        $tid = [];
        $taobaoTradeData = [];
        foreach($orders as $item) {
            $detail = $item->detail->pluck('field_value', 'field_name')->toArray();
            $tid[] = $detail['source_order_no'];
            $tid[] = $detail['source_order_no_1'] ?? '';
            $tid[] = $detail['source_order_no_2'] ?? '';
        }

        $taobaoTrade = TaobaoTrade::select('tid', 'payment', 'trade_status')->whereIn('tid', array_unique(array_filter($tid)))->get();

        if ($taobaoTrade) {
            foreach ($taobaoTrade as $trade) {
                $taobaoTradeData[$trade->tid] = [
                    'payment' => $trade->payment,
                    'refund' => $trade->trade_status == 7 ? $trade->payment : 0,
                ];
            }
        }
    }
}
