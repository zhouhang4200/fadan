<?php

namespace App\Http\Controllers\Api\Partner;

use App\Models\GameLevelingPlatform;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingOrderComplain;
use App\Models\GameLevelingOrderConsult;
use App\Services\OrderOperateController;
use App\Exceptions\GameLevelingOrderOperateException;
use Illuminate\Support\Facades\Redis;

class GameLevelingOrderOperateController
{
    /**
     * 订单查询接口
     * @param Request $request
     * @return mixed
     */
    public function query(Request $request)
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
                'left_time'                        => $order->leftTime(),
                'created_at'                       => $order->created_at ?? '',
                'receiving_time'                   => $order->take_at ?? '',
                'check_time'                       => $order->apply_complete_at ?? '',
                'checkout_time'                    => $order->complete_at ?? '',
                'pre_sale'                         => $order->pre_sale ?? '',
                'customer_service_name'            => $order->customer_service_name ?? '',
                'consult_desc'                     => $order->gameLevelingOrderConsult ? $order->gameLevelingOrderConsult->reason : '',
                'complain_desc'                    => $order->gameLevelingOrderComplain ? $order->gameLevelingOrderComplain->reason : '',
                'payment_amount'                   => $order->payAmount(),
                'get_amount'                       => $order->getAmount(),
                'poundage'                         => $order->getPoundage(),
                'profit'                           => $order->getProfit(),
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
     * @param Request $request
     * @return mixed
     */
    public function take(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('hatchet_man_name')) {
                return response()->partner(0, '参数缺失!');
            }

            if (!request('hatchet_man_qq') && !request('hatchet_man_phone')) {
                return response()->partner(0, '打手电话和打手QQ必须存在一个!');
            }

            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 询用查询接口
            $queryResult = call_user_func_array([config('gameleveling.controller')[$gameLevelingPlatform->platform_id], config('gameleveling.action')['orderInfo']], [$order]);

            // 同步价格
            if ($queryResult[config('gameleveling.third_orders_price')[$gameLevelingPlatform->platform_id]['data']][config('gameleveling.third_orders_price')[$gameLevelingPlatform->platform_id]['price']] != $order->amount) {
                Redis::hDel('order:price-markup', $order->trade_no);
                return response()->partner(0, '接单失败, 订单价格不一致!' . $order->trade_no);
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->take(request('hatchet_man_name'), request('hatchet_man_qq'), request('hatchet_man_phone'));
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
     * @param Request $request
     * @return mixed
     */
    public function applyComplete(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->applyComplete();
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
     * @param Request $request
     * @return mixed
     */
    public function cancelComplete(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->cancelComplete();
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
     * @param Request $request
     * @return mixed
     */
    public function applyConsult(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || is_null(request('api_amount')) || is_null(request('api_deposit')) || is_null(request('api_service')) || is_null(request('content'))) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->applyConsult(request('api_amount'), request('api_deposit'), request('api_service'), request('content'));
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
     * @param Request $request
     * @return mixed
     */
    public function cancelConsult(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->cancelConsult();
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
     * @param Request $request
     * @return mixed
     */
    public function rejectConsult(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->rejectConsult();
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
     * @param Request $request
     * @return mixed
     */
    public function agreeConsult(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || is_null(request('api_service'))) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->agreeConsult();
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
     * @param Request $request
     * @return mixed
     */
    public function forceDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->forceDelete();
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
     * @param Request $request
     * @return mixed
     */
    public function applyComplain(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('content', '接口没有传申请仲裁信息')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            // 开始调用
            OrderOperateController::init($request->user, $order)->applyComplain(request('content', '接口没有回传申请仲裁信息'));
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
     * @param Request $request
     * @return mixed
     */
    public function cancelComplain(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->cancelComplain();
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
     * @param Request $request
     * @return mixed
     */
    public function arbitration(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || is_null(request('api_amount')) || is_null(request('api_deposit')) || is_null(request('api_service'))) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->arbitration(request('api_amount'), request('api_deposit'), request('api_service'));
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
     * @param Request $request
     * @return mixed
     */
    public function anomaly(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->anomaly();
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
     * @param Request $request
     * @return mixed
     */
    public function cancelAnomaly(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->cancelAnomaly();
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
     * @param Request $request
     * @return mixed
     */
    public function callback(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('no')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->callback(request('order_no'));
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
     * @param Request $request
     * @return mixed
     */
    public function complete(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no')) {
                return response()->partner(0, '参数缺失!');
            }
            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->complete();
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
     * @param Request $request
     * @return mixed
     */
    public function leaveMessage(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('date') || !request('message')) {
                return response()->partner(0, '参数缺失!');
            }

            $gameLevelingPlatform = GameLevelingPlatform::where('platform_trade_no', request('order_no'))->first();

            $order = GameLevelingOrder::where('trade_no', $gameLevelingPlatform->game_leveling_order_trade_no)->first();

            if (!$gameLevelingPlatform || !$order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
            // 开始调用
            OrderOperateController::init($request->user, $order)->leaveMessage(request('date'), request('message'));
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
