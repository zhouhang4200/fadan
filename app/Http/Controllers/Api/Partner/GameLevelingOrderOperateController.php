<?php

namespace App\Http\Controllers\Api\Partner;

use DB;
use Exception;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingOrderComplain;
use App\Models\GameLevelingOrderConsult;
use App\Services\OrderOperateController;
use App\Exceptions\GameLevelingOrderOperateException;

class GameLevelingOrderOperateController
{
    /**
     * 订单查询接口
     * @return mixed
     */
    public function query()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();
            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 协商/仲裁信息
            $consult = GameLevelingOrderConsult::where('game_leveling_order_trade_no', $order->trade_no)->latest('id')->first();
            $complain = GameLevelingOrderComplain::where('game_leveling_order_trade_no', $order->trade_no)->latest('id')->first();

            // 如果存在接单时间
            if (isset($order->take_at) && !empty($order->take_at)) {
                // 计算到期的时间戳
                $expirationTimestamp = strtotime($order->take_at) + $order->day * 86400 + $order->hour * 3600;
                // 计算剩余时间
                $leftSecond = $expirationTimestamp - time();
                $leftTime = Sec2Time($leftSecond); // 剩余时间
            } else {
                $leftTime = '';
            }

            // 计算利润、手续费、获得金额等
            if (!in_array($order->status, [19, 20, 21])) {
                $paymentAmount = '';
                $getAmount = '';
                $poundage = '';
                $profit = '';
            } else {
                // 支付金额
                if ($order->status == 19) {
                    $amount = $consult->amount;
                } elseif ($order->status == 21) {
                    $amount = $complain->amount;
                } else {
                    $amount = $order->amount;
                }
                // 支付金额
                $paymentAmount = $amount !=0 ?  $amount + 0:  $amount;

                $getAmount = (float)0 + 0;
                $poundage = (float)0 + 0;

                // 利润
                $profit = ((float)$order->source_price - $paymentAmount + $getAmount - $poundage) + 0;
            }

            $orderInfo = [
                'order_no'                         => $order->trade_no,
                'status'                           => $order->status,
                'status_explain'                   => config('order.status_leveling')[$order->status],
                'game_name'                        => $order->gameLevelingOrderDetail->game_name,
                'game_region'                      => $order->gameLevelingOrderDetail->game_region_name ?? '',
                'game_serve'                       => $order->gameLevelingOrderDetail->game_server_name ?? '',
                'game_role'                        => $order->game_role ?? '',
                'game_account'                     => $order->game_account ?? '',
                'game_password'                    => $order->game_password ?? '',
                'game_leveling_type'               => $order->game_leveling_type_name ?? '',
                'game_leveling_title'              => $order->title ?? '',
                'game_leveling_price'              => $order->amount ?? '',
                'game_leveling_day'                => $order->day ?? '',
                'game_leveling_hour'               => $order->hour ?? '',
                'game_leveling_security_deposit'   => $order->security_deposit ?? '',
                'game_leveling_efficiency_deposit' => $order->efficiency_deposit ?? '',
                'game_leveling_requirements'       => $order->gameLevelingOrderDetail->requirement ?? '',
                'game_leveling_instructions'       => $order->gameLevelingOrderDetail->explain ?? '',
                'businessman_phone'                => $order->gameLevelingOrderDetail->user_phone ?? '',
                'businessman_qq'                   => $order->gameLevelingOrderDetail->user_qq ?? '',
                'order_password'                   => $order->take_order_password ?? '',
                'third'                            => $order->platform_id ?? '',
                'third_order_no'                   => $order->platform_trade_no ?? '',
                'hatchet_man_name'                 => $order->gameLevelingOrderDetail->hatchet_man_name ?? '',
                'hatchet_man_phone'                => $order->gameLevelingOrderDetail->hatchet_man_phone ?? '',
                'hatchet_man_qq'                   => $order->gameLevelingOrderDetail->hatchet_man_qq ?? '',
                'left_time'                        => $leftTime,
                'created_at'                       => $order->created_at ?? '',
                'receiving_time'                   => $order->take_at ?? '',
                'check_time'                       => $order->apply_complete_at ?? '',
                'checkout_time'                    => $order->complete_at ?? '',
                'pre_sale'                         => $order->pre_sale ?? '',
                'customer_service_name'            => $order->customer_service_name ?? '',
                'consult_desc'                     => $consult->reason,
                'complain_desc'                    => $complain->reason,
                'payment_amount'                   => $paymentAmount,
                'get_amount'                       => $getAmount,
                'poundage'                         => $poundage,
                'profit'                           => $profit,
                'tm_status'                        => $order->channel_order_status ?? '',
            ];

            myLog('game-leveling-order-operate-query-success', [$order->platform_id, $orderInfo]);

            return response()->ajax(1, '查询成功', [
                'order_info' => base64_encode(openssl_encrypt(json_encode($orderInfo),
                    'aes-128-cbc', config('partner.platform')[$order->platform_id]['aes_key'],
                    true, config('partner.platform')[$order->platform_id]['aes_iv'])),
            ]);
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '查询订单接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '查询订单接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 接单接口
     * @return mixed
     */
    public function take()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('hatchet_man_name')) {
                return response()->partner(0, '参数缺失!');
            }

            if (!request('hatchet_man_qq') && !request('hatchet_man_phone')) {
                return response()->partner(0, '打手电话和打手QQ必须存在一个!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 询用查询接口
            $queryResult = call_user_func_array([config('leveling.controller')[$order->platform_id], config('leveling.action')['getOrderDetail']], $order);

            // 同步价格
            if ($queryResult[config('leveling.third_orders_price')[$order->platform_id]['data']][config('leveling.third_orders_price')[$order->platform_id]['price']] != $order->amount) {
                AutoMarkupOrderEveryHour::deleteRedisHashKey($order->trade_no);
                return response()->partner(0, '接单失败, 订单价格不一致!' . $order->trade_no);
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->take(request('hatchet_man_name'), request('hatchet_man_qq'), request('hatchet_man_phone'));
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '接单接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '接单接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 申请验收
     * @return mixed
     */
    public function applyComplete()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->applyComplete();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '申请验收接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '申请验收接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 取消验收
     * @return mixed
     */
    public function cancelComplete()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->cancelComplete();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消验收接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消验收接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 申请协商
     * @return mixed
     */
    public function applyConsult()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('api_amount') || !request('api_deposit') || !request('api_service') || !request('content')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->applyConsult(request('api_amount'), request('api_deposit'), request('api_service'), request('content'));
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '申请协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '申请协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 取消协商
     * @return mixed
     */
    public function cancelConsult()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->cancelConsult();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 不同意协商
     * @return mixed
     */
    public function refuseConsult()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->refuseConsult();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '不同意协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '不同意协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 同意协商
     * @return mixed
     */
    public function agreeConsult()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('api_service')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->agreeConsult();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '同意协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '同意协商接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 强制撤单
     * @return mixed
     */
    public function forceDelete()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->forceDelete();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '强制撤单接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '强制撤单接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 申请仲裁
     * @return mixed
     */
    public function applyComplain()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('content', '接口没有传申请仲裁信息')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($order, request('user'))->applyComplain(request('content', '接口没有回传申请仲裁信息'));
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '申请仲裁接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '申请仲裁接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 取消仲裁
     * @return mixed
     */
    public function cancelComplain()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->cancelComplain();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消仲裁接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消仲裁接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 客服仲裁
     * @return mixed
     */
    public function arbitration()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('api_amount') || !request('api_deposit') || !request('api_service')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->arbitration(request('api_amount'), request('api_deposit'), request('api_service'));
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '客服仲裁接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '客服仲裁接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 异常
     * @return mixed
     */
    public function anomaly()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->anomaly();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '异常接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '异常接口失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 取消异常
     * @return mixed
     */
    public function cancelAnomaly()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->cancelAnomaly();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消异常失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '取消异常失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 回调
     * @return mixed
     */
    public function callback()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->callback(request('no'));
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '回调失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '回调失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 完成
     * @return mixed
     */
    public function complete()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->complete();
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '完成失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '完成失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }

    /**
     * 留言
     * @return mixed
     */
    public function leaveMessage()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('date') || !request('message')) {
                return response()->partner(0, '参数缺失!');
            }

            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($order, request('user'))->leaveMessage(request('date'), request('message'));
        } catch (GameLevelingOrderOperateException $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '留言失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, $e->getMessage());
        } catch (Exception $e) {
            DB::rollback();
            myLog('game-leveling-order-operate-error', ['订单号' => request('order_no') ?? '', '留言失败原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            return response()->partner(0, '接口异常!');
        }
        DB::commit();
        return response()->partner(1, '操作成功!');
    }
}