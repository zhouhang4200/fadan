<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Game;
use App\Models\GameRegion;
use App\Models\GameServer;
use App\Models\TaobaoTrade;
use App\Models\OrderBasicData;
use App\Extensions\Asset\Expend;
use App\Models\GameLevelingType;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingOrderLog;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingPlatform;
use App\Models\GameLevelingOrderDetail;
use App\Services\OrderOperateController;
use App\Exceptions\GameLevelingOrderOperateException;

/**
 * 游戏代练订单控制器
 * Class GameLevelingController
 * @package App\Http\Controllers\Frontend\V2\Order
 */
class IndexController extends Controller
{

    /**
     * 获取代练订单集合
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $orders = GameLevelingOrder::filter(array_merge([
            'parent_user_id' => request()->user()->getPrimaryUserId()
        ], request()->all()))
            ->select(
                'trade_no',
                'parent_user_id',
                'take_parent_user_id',
                'channel_order_trade_no',
                'channel_order_status',
                'platform_trade_no',
                'platform_id',
                'status',
                'title',
                'game_role',
                'game_account',
                'game_password',
                'seller_nick',
                'buyer_nick',
                'amount',
                'source_amount',
                'security_deposit',
                'efficiency_deposit',
                'created_at',
                'take_at',
                'day',
                'hour'
            )
            ->with([
                'gameLevelingOrderDetail' => function($query) {
                    $query->select(
                        'game_leveling_order_trade_no',
                        'game_name',
                        'game_server_name',
                        'game_region_name',
                        'game_leveling_type_name',
                        'hatchet_man_phone',
                        'hatchet_man_qq',
                        'player_qq',
                        'player_phone',
                        'user_remark',
                        'username'
                    );
                },
                'gameLevelingOrderComplain' => function($query) {
                    $query->select(
                        'game_leveling_order_trade_no',
                        'status',
                        'reason',
                        'initiator'
                    );
                },
                'gameLevelingOrderConsult' => function($query) {
                    $query->select(
                        'game_leveling_order_trade_no',
                        'amount',
                        'security_deposit',
                        'efficiency_deposit',
                        'status',
                        'reason',
                        'initiator'
                    );
                }])
            ->orderBy('id', 'desc')
            ->paginate(20);

        $responseData['total'] = $orders->total();

        foreach ($orders as $item) {
            $item->pay_amount = $item->payAmount();
            $item->get_amount = $item->getAmount();
            $item->left_time = $item->leftTime();
            $item->get_amount = $item->getAmount();
            $item->get_poundage = $item->getPoundage();
            $item->profit = ($item->get_amount  - $item->pay_amount  - $item->get_poundage) + 0;
            $item->remark_edit = false;
            $item->consult_describe = $item->getConsultDescribe();
            $responseData['items'][] = $item;
        }

        return response()->json(['status' => 1, 'message' => 'success', 'data' => $responseData]);
    }

    /**
     * 对应订单状态的数量
     */
    public function statusQuantity()
    {
        return GameLevelingOrder::selectRaw('status, count(1) as quantity')
            ->where('parent_user_id', request()->user()->getPrimaryUserId())
            ->groupBy('status')
            ->pluck('quantity', 'status')
            ->toArray();
    }

    /**
     * 下单
     */
    public function store()
    {
        // 下单
        try {
            $order = GameLevelingOrder::placeOrder(request()->user(),request()->all());
            $order->checkAutoMarkUpPrice();
            return response()->json(['status' => 1, 'message' => '下单成功']);
        } catch (\Exception $exception) {
            return response()->json(['status' => 0, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 获取要编辑的订单数据
     */
    public function edit()
    {
        $order = GameLevelingOrder::with([
            'gameLevelingOrderDetail',
            'gameLevelingOrderConsult',
            'gameLevelingOrderComplain',
        ])->filter([
            'trade_no' => request('trade_no'),
            'parent_user_id' => request()->user()->getPrimaryUserId(),
        ])->first();

        $order->left_time = $order->leftTime();

        // 已结算单剩余代练时间为空
        if (in_array($order->status, [19, 20, 21, 23, 24])) {
            $order->left_time = '';
        }

        $order->pay_amount = $order->payAmount();
        $order->get_amount = $order->getAmount();
        $order->get_poundage = $order->getPoundage();
        $order->complain_amount = $order->complainAmount();
        $order->consult_describe = $order->getConsultDescribe();
        $order->complain_describe = $order->getComplainDescribe();
        // 获取淘宝订单数据
        $order->taobao_data = TaobaoTrade::select([
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
            ->where('tid', $order->channel_order_trade_no)->first();
        return response($order);
    }

    /**
     * 更新订单信息
     * @return mixed
     */
    public function update()
    {
        DB::beginTransaction();
        $order = GameLevelingOrder::filter([
            'trade_no' => request('trade_no'),
            'parent_user_id' => request()->user()->getPrimaryUserId(),
        ])->lockForUpdate()->first();


        try {
            // 更改订单详情表数据
            $game = Game::find(request('game_id'));
            $region = GameRegion::find(request('game_region_id'));
            $server = GameServer::find(request('game_server_id'));
            $gameLevelingType = GameLevelingType::find(request('game_leveling_type_id'));
            $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->get();

            // 订单日志
            $user = User::find($order->user_id);
            $description = "用户[{$user->username}]修改了订单";
            GameLevelingOrderLog::createOrderHistory($order, $user, 22, $description);

            // 是否设置了自动加价
            $order->checkAutoMarkUpPrice();

            /***存在来源订单号（淘宝主订单号）, 写入关联淘宝订单表***/
            GameLevelingOrder::changeSameOriginOrderSourcePrice($order, request()->all());

            // 更新基础表数据
            $orderBasicData = OrderBasicData::where('order_no', $order->trade_no)->first();

            if ($orderBasicData) {
                $orderBasicData->game_id = $order->game_id;
                $orderBasicData->game_name = $game->name;
                $orderBasicData->price = $order->amount;
                $orderBasicData->security_deposit = $order->security_deposit;
                $orderBasicData->efficiency_deposit = $order->efficiency_deposit;
                $orderBasicData->original_price = $order->source_price;
                $orderBasicData->save();
            }

            if ($gameLevelingPlatforms->count() > 0) {
                // 订单是没有接单情况可修改所有信息 in_array($order->status, [1, 22])
                if (in_array($order->status, [1, 22])) {
                    if($order->update(request()->all())){
                        $data = [
                            'game_name' => $game->name,
                            'game_region_name' => $region->name,
                            'game_server_name' => $server->name,
                            'game_leveling_type_name' => $gameLevelingType->name,
                            'user_qq' => request('user_qq'),
                            'player_phone' => request('player_phone'),
                            'explain' => request('explain'),
                            'requirement' => request('requirement'),
                            'user_remark' => request('remark'),
                        ];

                        if ($res = GameLevelingOrderDetail::where('game_leveling_order_trade_no', request('trade_no'))
                            ->update($data)){
                            // 调用更新接口
                            foreach($gameLevelingPlatforms as $gameLevelingPlatform) {
                                call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['modifyOrder']], [$order]);
                            }
                        } else{
                            DB::rollBack();
                            return response()->ajax(0, '更新失败');
                        }
                    } else{
                        DB::rollBack();
                        return response()->ajax(0, '更新失败');
                    }
                } elseif (in_array($order->status, [13, 14, 17, 18])) { // 状态锁定 可改密码
                    if (request('game_password') != $order->game_password) {
                        $order->game_password = request('game_password');
                        $order->save();

                        call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['modifyGamePassword']], [$order]);
                    }
                } else {
                    DB::rollBack();
                    return response()->ajax(0, '当前状态不允许更改!');
                }
            } else {
                return response()->ajax(0, '更新成功!');
            }
        } catch (\Exception $e) {
            DB::rollback();
            myLog('order-update-error', [$e->getMessage(), $e->getLine(), $e->getFile()]);
            return response()->ajax(0, '更新失败服务器异常!');
        }

        DB::commit();
        return response()->ajax(1, '更新成功!');
    }

    /**
     * 订单操作日志
     *
     * @return mixed
     */
    public function log()
    {
        return GameLevelingOrderLog::where([
            'game_leveling_order_trade_no' => request('trade_no'),
            'parent_user_id' => request()->user()->getPrimaryUserId(),
        ])->get();
    }

    /**
     * 删除
     *
     * @return mixed
     */
    public function delete()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->delete();
                // 该订单下单成功的接单平台
                $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                    ->get();

                if ($gameLevelingPlatforms->count() > 0) {
                    // 删除下单成功的
                    foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                        call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['delete']], [$order]);
                    }
                }
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 上架
     * @return mixed
     */
    public function onSale()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->onSale();
                // 该订单下单成功的接单平台
                $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                    ->get();

                if ($gameLevelingPlatforms->count() > 0) {
                    // 下单成功的接单平台
                    foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                        call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['onSale']], [$order]);
                    }
                }
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 下架
     * @return mixed
     */
    public function offSale()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->offSale();
                // 该订单下单成功的接单平台
                $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                    ->get();

                if ($gameLevelingPlatforms->count() > 0) {
                    // 下单成功的接单平台
                    foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                        call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['offSale']], [$order]);
                    }
                }
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 完成
     * @return mixed
     */
    public function complete()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->complete();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['complete']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 锁定
     * @return mixed
     */
    public function lock()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->lock();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['lock']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 解除锁定
     * @return mixed
     */
    public function cancelLock()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->cancelLock();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['cancelLock']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 申请协商
     * @return mixed
     */
    public function applyConsult()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->applyConsult(request('payment_amount'), request('payment_deposit'), request('poundage', 0), request('reason', '无'));
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['applyConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-controller-error', ['原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 取消协商
     * @return mixed
     */
    public function cancelConsult()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->cancelConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['cancelConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 同意协商
     * @return mixed
     */
    public function agreeConsult()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->agreeConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['agreeConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 不同意协商
     * @return mixed
     */
    public function rejectConsult()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->rejectConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['rejectConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 申请仲裁
     * @return mixed
     */
    public function applyComplain()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $pic['pic1'] = request('pic1', '');
                $pic['pic2'] = request('pic2', '');
                $pic['pic3'] = request('pic3', '');

                OrderOperateController::init(Auth::user(), $order)->applyComplain(request('reason', '无'));
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['applyComplain']], [$order, $pic]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 取消仲裁
     * @return mixed
     */
    public function cancelComplain()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->cancelComplain();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['cancelComplain']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 加时
     * 增加代练天数/小时 (待验收 已接单 异常:状态可增加时间与天数)
     * @return mixed
     */
    public function addDayHour()
    {
        DB::beginTransaction();

        $order = GameLevelingOrder::filter([
            'trade_no' => request('trade_no'),
            'parent_user_id' => request()->user()->getPrimaryUserId(),
        ])->lockForUpdate()->first();

        if (in_array($order->status, [13, 14, 17])) {
            $order->day = bcadd($order->day, request('day'), 0);
            $order->hour = bcadd($order->hour, request('hour'), 0);
            $order->save();
        } else {
            return response()->ajax(0, '当前状态不支付增加代练时间!');
        }

        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['addTime']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '增加代练时间成功!');
    }

    /**
     * 加款
     * @return mixed
     * @throws GameLevelingOrderOperateException
     */
    public function addAmount()
    {
        DB::beginTransaction();

        $order = GameLevelingOrder::filter([
            'trade_no' => request('trade_no'),
            'parent_user_id' => request()->user()->getPrimaryUserId(),
        ])->lockForUpdate()->first();

        if (!$order) {
            throw new GameLevelingOrderOperateException('订单不存在!');
        }

        if (bcadd($order->amount, request('amount'), 2) <= $order->amount) {
            throw new GameLevelingOrderOperateException('加价金额必须大于原始发单金额!');
        }

        // 代练中 待验收 异常
        if (in_array($order->status, [13, 14, 17])) {
            $order->amount = bcadd($order->amount, request('amount'), 2);
            $order->save();
        }

        try {
            call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['addAmount']], [$order]);
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！' . $e->getMessage());
        }
        # 扣款
        Asset::handle(new Expend(request('amount'), 7, request('trade_no'), '代练改价支出', $order->creator_primary_user_id));
        # 写操作日志
        $description = "用户[" . request()->user()->username . "]增加代练价格 [ 加价前:" . bcsub($order->amount, request('amount'), 2) . " 加价后: " . $order->amount . " ]";
        GameLevelingOrderLog::createOrderHistory($order,request()->user(), 35, $description);
        DB::commit();

        return response()->ajax(1, '加价成功!');
    }

    /**
     * 订单详情
     * @return mixed
     */
    public function orderInfo()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['orderInfo']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 获取申请验收图片
     * @return mixed
     */
    public function applyCompleteImage()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $images = call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['getScreenShot']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!', $images);
    }

    /**
     * 获取留言
     * @return mixed
     */
    public function getMessage()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['getMessage']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 回复留言
     * @return mixed
     */
    public function replyMessage()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['replyMessage']], [$order, request('message')]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 修改账号的密码
     * @return mixed
     */
    public function modifyGamePassword()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['modifyGamePassword']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 获取仲裁信息
     * @return mixed
     */
    public function complainInfo()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $result = call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['complainInfo']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return $result;
    }

    /**
     * 添加仲裁详情
     * @return mixed
     */
    public function addComplainInfo()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $pic = request('pic');
                $content = request('reason');
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['addComplainDetail']], [$order, $pic, $content]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * 发送图片
     * @return mixed
     */
    public function sendImage()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $pic = request('pic');
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['sendImage']], [$order, $pic]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

    /**
     * @return mixed
     */
    public function message()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $message = call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['getMessage']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!', $message);
    }

    /**
     * 发送订单留言
     * @return mixed
     */
    public function sendMessage()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $content = request('content');
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['replyMessage']], [$order, $content]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            return response()->ajax(0, '订单异常！');
        }
        DB::commit();
        return response()->ajax(1, '操作成功!');
    }

//    /**
//     * 修改订单
//     * @return mixed
//     */
//    public function updateOrder()
//    {
//        $requestData = request('data');
//        DB::beginTransaction();
//        try {
//            $gameLevelingOrder = GameLevelingOrder::where('trade_no', request('trade_no'))->first();
//
//            if (!$gameLevelingOrder) {
//                throw new GameLevelingOrderOperateException('订单不存在!');
//            }
//            $gameLevelingOrderDetail = GameLevelingOrderDetail::where('trade_no', request('trade_no'))->first();
//
//            // 未接单
//            if (in_array($gameLevelingOrder->status, [1, 23])) {
//                $gameLevelingOrder->game_id = $requestData['game_id'];
//                $gameLevelingOrder->repeat = $requestData['repeat'];
//                $gameLevelingOrder->amount = $requestData['amount'];
//                $gameLevelingOrder->security_deposit = $requestData['security_deposit'];
//                $gameLevelingOrder->efficiency_deposit = $requestData['efficiency_deposit'];
//                $gameLevelingOrder->game_region_id = $requestData['game_region_id'];
//                $gameLevelingOrder->game_server_id = $requestData['game_server_id'];
//                $gameLevelingOrder->game_leveling_type_id = $requestData['game_leveling_type_id'];
//                $gameLevelingOrder->day = $requestData['day'];
//                $gameLevelingOrder->hour = $requestData['hour'];
//                $gameLevelingOrder->title = $requestData['title'];
//                $gameLevelingOrder->game_account = $requestData['game_account'];
//                $gameLevelingOrder->game_password = $requestData['game_password'];
//                $gameLevelingOrder->game_role = $requestData['game_role'];
//                $gameLevelingOrder->take_order_password = $requestData['take_order_password'];
//                $gameLevelingOrder->price_increase_step = $requestData['price_increase_step'];
//                $gameLevelingOrder->price_ceiling = $requestData['price_ceiling'];
//                $gameLevelingOrder->save();
//
//                $gameLevelingOrderDetail->game_region_name = $requestData['game_region_name'];
//                $gameLevelingOrderDetail->game_server_name = $requestData['game_server_name'];
//                $gameLevelingOrderDetail->game_leveling_type_name = $requestData['game_leveling_type_name'];
//                $gameLevelingOrderDetail->game_name = $requestData['game_name'];
//                $gameLevelingOrderDetail->explain = $requestData['explain'];
//                $gameLevelingOrderDetail->requirement = $requestData['requirement'];
//                $gameLevelingOrderDetail->save();
//            } elseif (in_array($gameLevelingOrder->status, [13, 14, 17, 18])) {
//                // 加价
//                if ($gameLevelingOrder->amount < $requestData['game_leveling_amount']) {
//                    $gameLevelingOrder->amount = $requestData['game_leveling_amount'];
//                    $gameLevelingOrder->save();
//
//                    call_user_func_array([config('gameleveling.controller')[$gameLevelingOrder->platform_id], config('gameleveling.action')['addAmount']], [$gameLevelingOrder]);
//                } elseif ($gameLevelingOrder->amount > $requestData['game_leveling_amount']) {
//                    return response()->ajax(0, '代练价格只可增加!');
//                }
//
//                // 加时
//                if ($requestData['game_leveling_day'] > $gameLevelingOrder->day || ($requestData['game_leveling_day'] == $gameLevelingOrder->day && $requestData['game_leveling_hour'] > $gameLevelingOrder->hour)) {
//                    $gameLevelingOrder->day = $requestData['game_leveling_day'];
//                    $gameLevelingOrder->hour = $requestData['game_leveling_hour'];
//                    $gameLevelingOrder->save();
//                } else {
//                    return response()->ajax(0, '代练时间只可增加!');
//                }
//
//                // 修改账号密码
//                if ($requestData['password'] != $gameLevelingOrder->game_password) {
//                    $gameLevelingOrder->game_password = $requestData['password'];
//                    $gameLevelingOrder->save();
//
//                    call_user_func_array([config('gameleveling.controller')[$gameLevelingOrder->platform_id], config('gameleveling.action')['modifyGamePassword']], [$gameLevelingOrder]);
//                }
//            }
//
//            // 订单日志
//            $historyData = [
//                'order_no' => $gameLevelingOrder->trade_no,
//                'user_id' => $gameLevelingOrder->user_id,
//                'admin_user_id' => '',
//                'type' => 1,
//                'name' => '修改',
//                'description' => "用户[{$gameLevelingOrder->user_id}]修改了订单",
//                'before' => '',
//                'after' => '',
//                'created_at' => Carbon::now()->toDateTimeString(),
//                'creator_primary_user_id' => $gameLevelingOrder->parent_user_id,
//            ];
//
//            OrderHistory::create($historyData);
//
//            // 是否设置了自动加价
//            $gameLevelingOrder->checkAutoMarkUpPrice();
//
//           /***存在来源订单号（淘宝主订单号）, 写入关联淘宝订单表***/
//            GameLevelingOrder::changeSameOriginOrderSourcePrice($gameLevelingOrder, $requestData);
//
//            // 更新基础表数据
//            $orderBasicData = OrderBasicData::where('order_no', $gameLevelingOrder->trade_no)->first();
//
//            $orderBasicData->game_id = $gameLevelingOrder->game_id;
//            $orderBasicData->game_name = $gameLevelingOrderDetail->game_name;
//            $orderBasicData->price = $gameLevelingOrder->price;
//            $orderBasicData->security_deposit = $gameLevelingOrder->security_deposit;
//            $orderBasicData->efficiency_deposit = $gameLevelingOrder->efficiency_deposit;
//            $orderBasicData->original_price = $gameLevelingOrder->source_price;
//            $orderBasicData->save();
//        } catch (GameLevelingOrderOperateException $e) {
//            DB::rollback();
//            return response()->ajax(0, $e->getMessage());
//        } catch (Exception $exception) {
//            DB::rollBack();
//            return response()->ajax(0, '订单异常!');
//        }
//        DB::commit();
//
//        return response()->ajax(1, '修改成功!');
//    }

    /**
     * 发单用户备注
     */
    public function updateUserRemark()
    {
        $order = GameLevelingOrder::where('trade_no', request('trade_no'))
            ->where('parent_user_id', request()->user()->getPrimaryUserId())
            ->first();
        $order->gameLevelingOrderDetail->user_remark = request('user_remark');
        $order->gameLevelingOrderDetail->save();
    }
}
