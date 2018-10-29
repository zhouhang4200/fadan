<?php

namespace App\Http\Controllers\Frontend\V2\Order;

use DB;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Models\OrderHistory;
use App\Models\OrderBasicData;
use App\Models\GameLevelingOrder;
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
        return GameLevelingOrder::filter(request()->all())
            ->with([
            'gameLevelingOrderDetail',
            'gameLevelingOrderComplain',
            'gameLevelingOrderConsult'])
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('frontend.v2.order.game-leveling.create');
    }

    /**
     * 下单
     */
    public function doCreate()
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

                if ($gameLevelingPlatforms->count() < 1) {
                    return response()->ajax(1, '操作成功!');
                }
                // 删除下单成功的
                foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                    call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('leveling.action')['delete']], [$order]);
                }
            } else {
                return response()->ajax(0, '订单不存在!');
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

                if ($gameLevelingPlatforms->count() < 1) {
                    return response()->ajax(1, '操作成功!');
                }
                // 下单成功的接单平台
                foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                    call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('leveling.action')['onSale']], [$order]);
                }
            } else {
                return response()->ajax(0, '订单不存在!');
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

                if ($gameLevelingPlatforms->count() < 1) {
                    return response()->ajax(1, '操作成功!');
                }
                // 下单成功的接单平台
                foreach ($gameLevelingPlatforms as $gameLevelingPlatform) {
                    call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('leveling.action')['offSale']], [$order]);
                }
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['complete']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['lock']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['cancelLock']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                OrderOperateController::init(Auth::user(), $order)->applyConsult(request('amount'), request('deposit'), request('poundage'), request('reason'));
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['applyConsult']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 取消协商
     * @return mixed
     */
    public function cancelConsult()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(Auth::user(), $order)->cancelConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['cancelConsult']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['agreeConsult']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['rejectConsult']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                $pic[]['pic1'] = request('pic1');
                $pic[]['pic2'] = request('pic2');
                $pic[]['pic3'] = request('pic3');

                OrderOperateController::init(Auth::user(), $order)->applyComplain(request('reason'));
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['applyComplain']], [$order, $pic]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['cancelComplain']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['modifyOrder']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * @return mixed
     */
    public function addTime()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['addTime']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 加款
     * @return mixed
     */
    public function addAmount()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['addAmount']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 订单详情
     * @return mixed
     */
    public function orderInfo()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['orderInfo']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 获取截图
     * @return mixed
     */
    public function getScreenShot()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['getScreenShot']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 获取留言
     * @return mixed
     */
    public function getMessage()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['getMessage']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['replyMessage']], [$order, request('message')]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['modifyGamePassword']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 获取仲裁详情
     * @return mixed
     */
    public function complainInfo()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $result = call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['complainInfo']], [$order]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
     * 获取仲裁详情
     * @return mixed
     */
    public function addComplainDetail()
    {
        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $pic = request('pic');
                $content = request('content');
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['addComplainDetail']], [$order, $pic, $content]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('leveling.action')['sendImage']], [$order, $pic]);
            } else {
                return response()->ajax(0, '订单不存在!');
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
                return response()->ajax(0, '订单不存在!');
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

                    call_user_func_array([config('gameleveling.controller')[$gameLevelingOrder->platform_id], config('leveling.action')['addAmount']], [$gameLevelingOrder]);
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

                    call_user_func_array([config('gameleveling.controller')[$gameLevelingOrder->platform_id], config('leveling.action')['modifyGamePassword']], [$gameLevelingOrder]);
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
