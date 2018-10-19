<?php

namespace App\Http\Controllers\Api\Partner;

use DB;
use Exception;
use App\Models\LevelingMessage;
use App\Models\GameLevelingOrder;
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
                'hatchet_man_name'                 => $order->hatchet_man_name ?? '',
                'hatchet_man_phone'                => $order->hatchet_man_phone ?? '',
                'hatchet_man_qq'                   => $order->hatchet_man_qq ?? '',
                'left_time'                        => $detail['left_time'],
                'created_at'                       => $detail['created_at'],
                'receiving_time'                   => $detail['receiving_time'],
                'check_time'                       => $detail['check_time'],
                'checkout_time'                    => $detail['checkout_time'],
                'pre_sale'                         => $detail['pre_sale'],
                'customer_service_name'            => $detail['customer_service_name'],
                'consult_desc'                     => $detail['consult'],
                'complain_desc'                    => $detail['complain'],
                'payment_amount'                   => $detail['payment_amount'],
                'get_amount'                       => $detail['get_amount'],
                'poundage'                         => $detail['poundage'],
                'profit'                           => $detail['profit'],
                'tm_status'                        => $tmStatus,
            ];

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
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
    public function receive()
    {
        DB::beginTransaction();
        try {
            if (!request('order_no') || !request('hatchet_man_qq') || !request('hatchet_man_phone') || !request('hatchet_man_name')) {
                return response()->partner(0, '参数缺失!');
            }
            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }
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
            if (!request('order_no') || !request('data') || !request('message')) {
                return response()->partner(0, '参数缺失!');
            }

            $order = GameLevelingOrder::where('trade_no', request('order_no'))->first();

            if (! $order) {
                throw new GameLevelingOrderOperateException("订单不存在!");
            }

            $data = [
                'user_id' => $order->parent_user_id, // 发单用户ID
                'third' => $order->platform_id,
                'third_order_no' => $order->platform_trade_no, // 第三方平台单号
                'foreign_order_no' => $order->channel_order_trade_no, // 天猫单号
                'order_no' => $order->trade_no, // 我们平台单号
                'date' => request('data'), // 第三方平台单号留言时间
                'contents' => request('message'), // 第三方平台单号留言内容
            ];

            LevelingMessage::create($data);

            levelingMessageCount($order->parent_user_id, 1, 1);
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
