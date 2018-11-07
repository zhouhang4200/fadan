<?php

namespace App\Http\Controllers\Frontend\V2\Order\GameLeveling;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\TaobaoTrade;
use App\Models\OrderHistory;
use App\Models\OrderBasicData;
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
        return GameLevelingOrder::filter(array_merge([
            'parent_user_id' => request()->user()->getPrimaryUserId()
        ], request()->all()))
            ->with([
                'gameLevelingOrderDetail',
                'gameLevelingOrderComplain',
                'gameLevelingOrderConsult'])
            ->orderBy('id', 'desc')
            ->paginate(20);
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
        // 验证前台传入数据

        // 下单
        try {
            GameLevelingOrder::placeOrder(request()->user(),request()->all());
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
            $gameLevelingPlatforms = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->get();

            if ($gameLevelingPlatforms->count() > 0) {
                // 订单是没有接单情况可修改所有信息 in_array($order->status, [1, 22])
                if (in_array($order->status, [1, 22])) {
                    if($order->update(request()->all())){
                        if ($res = $order->gameLevelingOrderDetail()
                            ->where('game_leveling_order_trade_no', request('trade_no'))
                            ->update(request()->only([
                                'user_qq',
                                'player_phone',
                                'explain',
                                'requirement',
                            ]))){
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
                } elseif($order->status == 18) { // 状态锁定 可改密码
                    $order->update(request()->only(['game_account', 'game_password']));
                    // 调用更新接口
                    call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['modifyOrder']], [$order]);
                } else {
                    DB::rollBack();
                    return response()->ajax(0, '当前状态不允许更改');
                }

            } else {
                return response()->ajax(0, '更新成功!');
            }
        } catch (\Exception $exception) {
            myLog('modify-order-error', ['message' => $exception->getMessage(), 'file' => $exception->getFile(), 'line' => $exception->getLine()]);
            return response()->ajax(0, '更新失败服务器异常');
        }
        DB::commit();

        return response()->ajax(1, '更新成功');
    }


    /**
     * 订单操作日志
     * @return mixed
     */
    public function log()
    {
        return GameLevelingOrderLog::where([
            'game_leveling_order_trade_no' => request('trade_no'),
            'parent_user_id' => request()->user()->getPrimaryUserId(),
        ])->orderBy('id', 'desc')->get();
    }

    /**
     * 删除
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

                if ($gameLevelingPlatforms->count() > 1) {
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

                if ($gameLevelingPlatforms->count() > 1) {
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

                if ($gameLevelingPlatforms->count() > 1) {
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
     * 修改订单
     * @return mixed
     */
    public function modifyOrder()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['modifyOrder']], [$order]);
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
        // TODO 扣款
        DB::commit();
        // TODO 写操作日志
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['getScreenShot']], [$order]);
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
//        获取到图片返回数组
//        return response()->ajax(1, '获取成功', [
//            ['img' => 'http://tm.test/frontend/v2/images/logo.png'],
//            ['img' => 'http://tm.test/frontend/v2/images/logo.png'],
//            ['img' => 'http://baidu.com'],
//        ]);
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

    /**
     * 修改订单
     * @return mixed
     */
    public function updateOrder()
    {
        $requestData = request('data');
        DB::beginTransaction();
        try {
            $gameLevelingOrder = GameLevelingOrder::where('trade_no', request('trade_no'))->first();

            if (!$gameLevelingOrder) {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
            $gameLevelingOrderDetail = GameLevelingOrderDetail::where('trade_no', request('trade_no'))->first();

            // 未接单
            if (in_array($gameLevelingOrder->status, [1, 23])) {
                $gameLevelingOrder->game_id = $requestData['game_id'];
                $gameLevelingOrder->repeat = $requestData['repeat'];
                $gameLevelingOrder->amount = $requestData['amount'];
                $gameLevelingOrder->security_deposit = $requestData['security_deposit'];
                $gameLevelingOrder->efficiency_deposit = $requestData['efficiency_deposit'];
                $gameLevelingOrder->game_region_id = $requestData['game_region_id'];
                $gameLevelingOrder->game_server_id = $requestData['game_server_id'];
                $gameLevelingOrder->game_leveling_type_id = $requestData['game_leveling_type_id'];
                $gameLevelingOrder->day = $requestData['day'];
                $gameLevelingOrder->hour = $requestData['hour'];
                $gameLevelingOrder->title = $requestData['title'];
                $gameLevelingOrder->game_account = $requestData['game_account'];
                $gameLevelingOrder->game_password = $requestData['game_password'];
                $gameLevelingOrder->game_role = $requestData['game_role'];
                $gameLevelingOrder->take_order_password = $requestData['take_order_password'];
                $gameLevelingOrder->price_increase_step = $requestData['price_increase_step'];
                $gameLevelingOrder->price_ceiling = $requestData['price_ceiling'];
                $gameLevelingOrder->save();

                $gameLevelingOrderDetail->game_region_name = $requestData['game_region_name'];
                $gameLevelingOrderDetail->game_server_name = $requestData['game_server_name'];
                $gameLevelingOrderDetail->game_leveling_type_name = $requestData['game_leveling_type_name'];
                $gameLevelingOrderDetail->game_name = $requestData['game_name'];
                $gameLevelingOrderDetail->explain = $requestData['explain'];
                $gameLevelingOrderDetail->requirement = $requestData['requirement'];
                $gameLevelingOrderDetail->save();
            } elseif (in_array($gameLevelingOrder->status, [13, 14, 17, 18])) {
                // 加价
                if ($gameLevelingOrder->amount < $requestData['game_leveling_amount']) {
                    $gameLevelingOrder->amount = $requestData['game_leveling_amount'];
                    $gameLevelingOrder->save();

                    call_user_func_array([config('gameleveling.controller')[$gameLevelingOrder->platform_id], config('gameleveling.action')['addAmount']], [$gameLevelingOrder]);
                } elseif ($gameLevelingOrder->amount > $requestData['game_leveling_amount']) {
                    return response()->ajax(0, '代练价格只可增加!');
                }

                // 加时
                if ($requestData['game_leveling_day'] > $gameLevelingOrder->day || ($requestData['game_leveling_day'] == $gameLevelingOrder->day && $requestData['game_leveling_hour'] > $gameLevelingOrder->hour)) {
                    $gameLevelingOrder->day = $requestData['game_leveling_day'];
                    $gameLevelingOrder->hour = $requestData['game_leveling_hour'];
                    $gameLevelingOrder->save();
                } else {
                    return response()->ajax(0, '代练时间只可增加!');
                }

                // 修改账号密码
                if ($requestData['password'] != $gameLevelingOrder->game_password) {
                    $gameLevelingOrder->game_password = $requestData['password'];
                    $gameLevelingOrder->save();

                    call_user_func_array([config('gameleveling.controller')[$gameLevelingOrder->platform_id], config('gameleveling.action')['modifyGamePassword']], [$gameLevelingOrder]);
                }
            }

            // 订单日志
            $historyData = [
                'order_no' => $gameLevelingOrder->trade_no,
                'user_id' => $gameLevelingOrder->user_id,
                'admin_user_id' => '',
                'type' => 1,
                'name' => '修改',
                'description' => "用户[{$gameLevelingOrder->user_id}]修改了订单",
                'before' => '',
                'after' => '',
                'created_at' => Carbon::now()->toDateTimeString(),
                'creator_primary_user_id' => $gameLevelingOrder->parent_user_id,
            ];

            OrderHistory::create($historyData);

            // 是否设置了自动加价
            GameLevelingOrder::checkAutoMarkUpPrice($gameLevelingOrder);

           /***存在来源订单号（淘宝主订单号）, 写入关联淘宝订单表***/
            GameLevelingOrder::changeSameOriginOrderSourcePrice($gameLevelingOrder, $requestData);

            // 更新基础表数据
            $orderBasicData = OrderBasicData::where('order_no', $gameLevelingOrder->trade_no)->first();

            $orderBasicData->game_id = $gameLevelingOrder->game_id;
            $orderBasicData->game_name = $gameLevelingOrderDetail->game_name;
            $orderBasicData->price = $gameLevelingOrder->price;
            $orderBasicData->security_deposit = $gameLevelingOrder->security_deposit;
            $orderBasicData->efficiency_deposit = $gameLevelingOrder->efficiency_deposit;
            $orderBasicData->original_price = $gameLevelingOrder->source_price;
            $orderBasicData->save();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return response()->ajax(0, $e->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->ajax(0, '订单异常!');
        }
        DB::commit();

        return response()->ajax(1, '修改成功!');
    }
}
