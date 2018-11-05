<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Requests\GameLevelingOrderRequest;
use App\Models\GameLevelingOrderDetail;
use App\Models\GameLevelingPlatform;
use DB;
use Exception;
use App\Models\GameLevelingOrder;
use App\Services\OrderOperateController;
use App\Http\Controllers\Api\ApiController;
use App\Exceptions\GameLevelingOrderOperateException;

/**
 * Class GameLevelingController
 * @package App\Http\Controllers\Api\V1\Order
 */
class GameLevelingOrderController extends ApiController
{
    /**
     * 下单
     * @throws \Exception
     */
    public function create()
    {
        // 验证规则
        $rules = [
            'phone'   => [
                'required',
                'exists:users',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:20',
            ],
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $this->validate(request(), $rules);

        $order = null;
        try {
            $order = GameLevelingOrder::placeOrder(request()->user(),request()->all());
        } catch (\Exception $exception) {
            return $this->failed('下单失败');
        }
        $this->respond($order);
    }

    #查看订单
    public function show()
    {
        
    }

    # 更新订单
    public function update()
    {
        // 验证规则
        $rules = [
            'trade_no' => 'required|string|min:22|max:22',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $this->validate(request(), $rules, [
            'trade_no' => 'trade_no'
        ]);
        
    }

    /**
     * 撤单
     * @return mixed
     */
    public function delete()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->delete();
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
                return $this->failed('订单不存在！', 402);
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed($e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed('订单异常！', 402);
        }
        DB::commit();
        
        return $this->success('操作成功');
    }

    /**
     * 上架
     * @return mixed
     */
    public function onSale()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->onSale();
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 下架
     * @return mixed
     */
    public function offSale()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->offSale();
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 完成
     * @return mixed
     */
    public function complete()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->complete();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['complete']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 锁定
     * @return mixed
     */
    public function lock()
    {
        $this->tradeNoValidate();


        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->lock();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['lock']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 解除锁定
     * @return mixed
     */
    public function cancelLock()
    {
        $this->tradeNoValidate();


        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->cancelLock();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['cancelLock']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
                OrderOperateController::init(auth()->guard('api')->user(), $order)->applyConsult(request('payment_amount'), request('payment_deposit'), request('poundage', 0), request('reason', '无'));
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['applyConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-controller-error', ['原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 取消协商
     * @return mixed
     */
    public function cancelConsult()
    {
        $this->tradeNoValidate();


        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->cancelConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['cancelConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 同意协商
     * @return mixed
     */
    public function agreeConsult()
    {
        $this->tradeNoValidate();


        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->agreeConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['agreeConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 不同意协商
     * @return mixed
     */
    public function rejectConsult()
    {
        $this->tradeNoValidate();


        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->rejectConsult();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['rejectConsult']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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

                OrderOperateController::init(auth()->guard('api')->user(), $order)->applyComplain(request('reason', '无'));
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['applyComplain']], [$order, $pic]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 取消仲裁
     * @return mixed
     */
    public function cancelComplain()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                OrderOperateController::init(auth()->guard('api')->user(), $order)->cancelComplain();
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['cancelComplain']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
            return $this->failed( '当前状态不支付增加代练时间!', 402);
        }

        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['addTime']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('增加代练时间成功!');
    }

    /**
     * 加款
     * @return mixed
     */
    public function addAmount()
    {
        DB::beginTransaction();
        
        try {
            
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
            
            call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['addAmount']], [$order]);
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！' , 402);
        }
        // TODO 扣款
        DB::commit();
        // TODO 写操作日志
        return $this->success('加价成功!');
    }

    /**
     * 订单详情
     * @return mixed
     */
    public function orderInfo()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['orderInfo']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
//        获取到图片返回数组
//        return $this->success('获取成功', [
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

    /**
     * 获取仲裁信息
     * @return mixed
     */
    public function complainInfo()
    {
        $this->tradeNoValidate();

        DB::beginTransaction();
        try {
            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
                $result = call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['complainInfo']], [$order]);
            } else {
                throw new GameLevelingOrderOperateException('订单不存在!');
            }
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->success('操作成功!');
    }

//    /**
//     * @return mixed
//     */
//    public function message()
//    {
//        DB::beginTransaction();
//        try {
//            if ($order = GameLevelingOrder::where('trade_no', request('trade_no'))->first()) {
//                $message = call_user_func_array([config('gameleveling.controller')[$order->platform_id], config('gameleveling.action')['getMessage']], [$order]);
//            } else {
//                throw new GameLevelingOrderOperateException('订单不存在!');
//            }
//        } catch (GameLevelingOrderOperateException $e) {
//            DB::rollback();
//            return $this->failed( $e->getMessage(), 402);
//        } catch (Exception $e) {
//            DB::rollback();
//            return $this->failed( '订单异常！', 402);
//        }
//        DB::commit();
//        return $this->success('操作成功!', $message);
//    }

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
            return $this->failed( $e->getMessage(), 402);
        } catch (Exception $e) {
            DB::rollback();
            return $this->failed( '订单异常！', 402);
        }
        DB::commit();
        return $this->failed('操作成功!');
    }


    private function tradeNoValidate()
    {
        // 验证规则
        $rules = [
            'trade_no' => 'required|string|min:22|max:22',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $this->validate(request(), $rules, [
            'trade_no' => 'trade_no'
        ]);
    }
}
