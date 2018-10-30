<?php

namespace App\Services;

use App\Models\GameLevelingOrderLog;
use DB;
use Cache;
use Asset;
use Redis;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\UserAsset;
use App\Models\SmsTemplate;
use App\Models\OrderHistory;
use App\Models\OrderBasicData;
use App\Models\LevelingMessage;
use App\Extensions\Asset\Income;
use App\Extensions\Asset\Expend;
use App\Events\NotificationEvent;
use App\Models\GameLevelingOrder;
use App\Exceptions\CustomException;
use App\Models\HatchetManBlacklist;
use App\Models\GameLevelingPlatform;
use App\Models\GameLevelingOrderDetail;
use App\Models\GameLevelingOrderConsult;
use App\Models\GameLevelingOrderComplain;
use App\Models\GameLevelingOrderPreviousStatus;
use App\Exceptions\GameLevelingOrderOperateException;

class OrderOperateController
{
    protected static $user;
    protected static $order;
    protected static $adminUser;
    protected static $instance;

    /**
     * @param $user
     * @param $order
     * @param null $adminUser
     * @return mixed
     */
    public static function init(User $user, GameLevelingOrder $order, $adminUser = null)
    {
        static::$user = $user;
        static::$order = $order;

        if ($adminUser) {
            static::$adminUser = $adminUser;
        }

        if (!static::$instance instanceof self) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 写订单日志
     * @param $type
     * @param string $description
     */
    private static function createOrderHistory($type, $description = '')
    {
        GameLevelingOrderLog::create([
            'game_leveling_order_trade_no' => static::$order->trade_no,
            'user_id' => static::$order->user_id,
//            'username' => static::$order->user_id,
            'parent_user_id' => static::$order->parent_user_id,
            'admin_user_id' => static::$adminUser ?? 0,
            'type' => $type,
            'name' => config('order.operation_type')[$type],
            'description' => $description,
        ]);
    }

    /**
     * 拆分双金
     * @param int $amount
     * @param int $deposit
     * @return array
     * @throws Exception
     */
    private static function handleDeposit($amount = 0, $deposit = 0)
    {
        if ($amount < 0 || $amount > static::$order->amount) {
            throw new GameLevelingOrderOperateException('协商代练金额超出代练订单金额!');
        }

        if ($deposit < 0 || $deposit > bcadd(static::$order->security_deposit, static::$order->efficiency_deposit)) {
            throw new GameLevelingOrderOperateException('协商双金超出代练订单双金!');
        }

        if ($deposit <= static::$order->security_deposit) {
            return [
                'security_deposit' => $deposit,
                'efficiency_deposit' => 0,
            ];
        } else {
            return [
                'security_deposit' => static::$order->security_deposit,
                'efficiency_deposit' => bcsub($deposit, static::$order->security_deposit),
            ];
        }
    }

    /**
     * 分辨协商、仲裁操作人
     * @return int
     * @throws Exception
     */
    private static function initiator()
    {
        if (static::$user->id == static::$order->user_id) {
            return 1;
        } elseif (static::$user->id == static::$order->take_user_id) {
            return 2;
        } else {
            throw new GameLevelingOrderOperateException('当前操作用户不存在!');
        }
    }

    /**
     * 检测发单人和平台余额
     * @param $order
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function checkUserAndPlatformBalance($order)
    {
        if(UserAsset::balance($order->parent_user_id) < 2000) {
            // https://oapi.dingtalk.com/robot/send?access_token=b5c71a94ecaba68b9fec8055100324c06b1d98a6cd3447c5d05e224efebe5285 代练小组
            // https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8 测试用
            $userInfo = User::find($order->parent_user_id);

            $existCache = Cache::get($order->parent_user_id);
            if (!$existCache && in_array($order->parent_user_id, [8317, 8803, 8790, 8785, 8711, 8523])) {
                $now = Carbon::now();
                $expiresAt = $now->diffInMinutes(Carbon::parse(date('Y-m-d'))->endOfDay());
                Cache::put($order->parent_user_id, '1', $expiresAt);

                // 发送通知
                event((new NotificationEvent('balanceNotice', [
                    'type' => 1, // 1 资金 2 短信
                    'user_id' => $order->parent_user_id,
                    'title' => '余额不足',
                    'message' => '[淘宝发单平台]提醒您，您的账户(ID:' . $order->parent_user_id . ')余额已不足2000元，请及时充值，保证业务正常进行。'
                ])));

//                sendSms(0, $this->order->no, $userInfo->phone, '[淘宝发单平台]提醒您，您的账户(ID:' . $order->parent_user_id . ')余额已不足2000元，请及时充值，保证业务正常进行。', '');
            }

            $client = new Client();
            $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => '发单商户ID: ' . $order->parent_user_id . ', 昵称(' . $userInfo->nickname . ')。账户余额低于2000元, 已发送短信通知, 请运营同事及时跟进。'
                    ],
                    'at' => [
                        'isAtAll' => true
                    ]
                ]
            ]);
        }

        // 发单平台余额检测
        $platformBalanceAlarm = config('gameleveling.balance_alarm')[$order->take_parent_user_id];
        if (UserAsset::balance($order->take_parent_user_id) < $platformBalanceAlarm) {
            $userInfo = User::find($order->take_parent_user_id);

            $existCache = Cache::get($order->take_parent_user_id);
            if (!$existCache) {
                $now = Carbon::now();
                $expiresAt = $now->diffInMinutes(Carbon::parse(date('Y-m-d'))->endOfDay());
                Cache::put($order->take_parent_user_id, '1', $expiresAt);

//                sendSms(0, $this->order->no, $userInfo->phone, '[淘宝发单平台]提醒您，您的账户(ID:' . $order->take_parent_user_id . ')余额已不足' . $platformBalanceAlarm . '元，请及时充值，保证业务正常进行。', '');
            }

            $client = new Client();
            $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => '接单平台ID: ' . $order->take_parent_user_id . ', 昵称(' . $userInfo->nickname . ')。账户余额低于' . $platformBalanceAlarm . ', 已发送短信通知, 请运营同事及时跟进。'
                    ],
                    'at' => [
                        'isAtAll' => true
                    ]
                ]
            ]);
        }
    }

    /**
     * 写入订单数量角标
     * @param $previousStatus
     * @param $handleStatus
     */
    public static function orderCount($previousStatus, $handleStatus)
    {
        // 接单人
        orderStatusCount(static::$order->take_parent_user_id, $handleStatus);
        orderStatusCount(static::$order->take_parent_user_id, $previousStatus, 4);
        // 发单人
        orderStatusCount(static::$order->parent_user_id, $handleStatus);
        orderStatusCount(static::$order->parent_user_id, $previousStatus, 4);
    }

    /**
     * 找出订单客户订单号找出商户设置的模版发送短信
     * @param $purpose
     * @param $message
     */
    public static function sendMessage($purpose, $message = '')
    {
        try {
            // 获取商户设置的模板
            $template = SmsTemplate::where('user_id', static::$order->parent_user_id)
                ->where('status', 1)
                ->where('purpose', $purpose)
                ->first();

            if ($template) {
                if (isset(static::$order->gameLevelingOrderDetail->player_phone) && static::$order->gameLevelingOrderDetail->player_phone) {
                    $smsContent = '';
                    if (isset(static::$order->seller_nick) && !empty(static::$order->seller_nick)) {
                        $smsContent = '[' . static::$order->seller_nick . '] 提醒您,' .  $template->contents;
                    } else {
                        $smsContent = $template->contents;
                    }
                    // 发送短信
                    sendSms(static::$order->parent_user_id,
                        static::$order->trade_no,
                        static::$order->gameLevelingOrderDetail->player_phone,
                        $smsContent,
                        $message,
                        static::$order->channel_order_trade_no,
                        static::$order->platform_trade_no,
                        static::$order->platform_id
                    );
                }
            }
        } catch (Exception $e) {
            myLog('order-operate-send-message', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    /**
     * 上架
     * @throws Exception
     */
    public function onSale()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 22) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }
            // 修改订单状态和记录订单日志
            static::$order->status = 1;
            static::$order->save();

            $description = "用户[".static::$user->username."]将订单从[已下架]设置为[待接单]状态！";

            GameLevelingOrderLog::createOrderHistory(static::$order, 14, $description);

            // 订单数量角标
            static::orderCount(22, 13);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 下架
     * @throws Exception
     */
    public function offSale()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 1) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            static::$order->status = 22;
            static::$order->save();

            $description = "用户[".static::$user->username."]将订单从[待接单]设置为[已下架]状态！";
            GameLevelingOrderLog::createOrderHistory(static::$order, 15, $description);

            // 从自动下架任务中删除
            autoUnShelveDel(static::$order->trade_no);

            // 订单数量角标
            static::orderCount(1, 22);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 撤单
     * @throws Exception
     */
    public function delete()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 1) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已撤单]状态！";

            static::$order->status = 24;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 23, $description);
            // 从自动下架任务中删除
            autoUnShelveDel(static::$order->trade_no);

            // 订单数量角标
            static::orderCount(1, 24);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 写入基础数据
            OrderBasicData::createData(static::$order);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 锁定
     * @throws Exception
     */
    public function lock()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [13, 14, 17])) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 记录订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::create([
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'status' => static::$order->status
            ]);

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已锁定]状态！";

            static::$order->status = 18;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 16, $description);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 18);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 取消锁定
     * @throws Exception
     */
    public function cancelLock()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 18) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 17, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 订单数量角标
            static::orderCount(18, static::$order->status);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 申请协商
     * @param $amount
     * @param $deposit
     * @param int $poundage
     * @param string $reason
     * @throws GameLevelingOrderOperateException
     */
    public function applyConsult($amount, $deposit, $poundage = 0, $reason = '无协商原因')
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [13, 14, 17, 18])) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            if (!is_numeric($amount) || !is_numeric($deposit) || !is_numeric($poundage)) {
                throw new GameLevelingOrderOperateException("参数类型不合法！");
            }

            // 记录订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::create([
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'status' => static::$order->status,
            ]);

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[协商中]状态！";

            static::$order->status = 15;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 18, $description);

            // 将协商数据写入协商表
            $handleDeposit = static::handleDeposit($amount, $deposit);
            $initiator = static::initiator();
            GameLevelingOrderConsult::updateOrCreate([
                'game_leveling_order_trade_no' => static::$order->trade_no,
            ], [
                'user_id' => static::$user->id,
                'parent_user_id' => static::$user->getPrimaryUserId(),
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'amount' => $amount,
                'security_deposit' => $handleDeposit['security_deposit'],
                'efficiency_deposit' => $handleDeposit['efficiency_deposit'],
                'poundage' => $poundage,
                'reason' => $reason,
                'status' => 1,
                'initiator' => $initiator,
            ]);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 15);
            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
            // 发送短信
            static::sendMessage(4, '代练订单申请协商短信');
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 取消协商
     * @throws Exception
     */
    public function cancelConsult()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 15) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 19, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 更改协商表状态
            GameLevelingOrderConsult::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->update(['status' => 3]);

            // 订单数量角标
            static::orderCount(15, static::$order->status);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 不同意协商
     * @throws Exception
     */
    public function rejectConsult()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 15) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 33, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 更改协商表状态
            GameLevelingOrderConsult::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->update(['status' => 3]);

            // 订单数量角标
            static::orderCount(15, static::$order->status);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 同意协商
     * @param $poundage
     * @throws Exception
     */
    public function agreeConsult($poundage = 0)
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [15, 16])) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已协商]状态！";

            static::$order->status = 19;
            static::$order->complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 24, $description);
            // 更改协商表状态和手续费
            $gameLevelingOrderConsult = GameLevelingOrderConsult::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->first();

            $gameLevelingOrderConsult->poundage = $poundage;
            $gameLevelingOrderConsult->status = 2;
            $gameLevelingOrderConsult->save();

            /******************流水************************/
            // 当前操作人 == 订单拥有者
            $orderParentUserId = $gameLevelingOrderConsult->initiator == 2 ? static::$order->parent_user_id : static::$order->take_parent_user_id;
            $ids = User::find($orderParentUserId)->children->pluck('id')->merge($orderParentUserId)->toArray();
            if (! in_array(static::$user->id, $ids)) {
                throw new GameLevelingOrderOperateException('当前操作人不是该订单拥有者!');
            }
            // 手续费 <= 协商双金
            if ($gameLevelingOrderConsult->poundage > bcadd($gameLevelingOrderConsult->security_deposit, $gameLevelingOrderConsult->efficiency_deposit)) {
                throw new GameLevelingOrderOperateException('协商手续费超出了协商双金!');
            }

            //（发单剩余代练费收入，支出手续费，收入双金)
            $userAmount = bcsub(static::$order->amount, $gameLevelingOrderConsult->amount);
            if ($userAmount > 0) {
                Asset::handle(new Income($userAmount, 7, static::$order->trade_no, '退回协商代练费', static::$order->parent_user_id));
            }

            if ($gameLevelingOrderConsult->security_deposit > 0) {
                Asset::handle(new Income($gameLevelingOrderConsult->security_deposit, 10, static::$order->trade_no, '协商安全保证金收入', static::$order->parent_user_id));
            }

            if ($gameLevelingOrderConsult->efficiency_deposit > 0) {
                Asset::handle(new Income($gameLevelingOrderConsult->efficiency_deposit, 11, static::$order->trade_no, '协商效率保证金收入', static::$order->parent_user_id));
            }

            if ($gameLevelingOrderConsult->poundage > 0) {
                Asset::handle(new Expend($gameLevelingOrderConsult->poundage, 3, static::$order->trade_no, '协商手续费支出', static::$order->parent_user_id));
            }

            // (接单收入代练费，收入退回双金，收入手续费)
            if ($gameLevelingOrderConsult->amount > 0) {
                Asset::handle(new Income($gameLevelingOrderConsult->amount, 12, static::$order->trade_no, '协商代练费收入', static::$order->take_parent_user_id));
            }

            $backSecurityDeposit = bcsub(static::$order->security_deposit, $gameLevelingOrderConsult->security_deposit);
            if ($backSecurityDeposit > 0) {
                Asset::handle(new Income($backSecurityDeposit, 8, static::$order->trade_no, '协商安全保证金退回', static::$order->take_parent_user_id));
            }

            $backEfficiencyDeposit = bcsub(static::$order->efficiency_deposit, $gameLevelingOrderConsult->efficiency_deposit);
            if ($backEfficiencyDeposit > 0) {
                Asset::handle(new Income($backEfficiencyDeposit, 9, static::$order->trade_no, '协商效率保证金退回', static::$order->take_parent_user_id));
            }

            if ($gameLevelingOrderConsult->poundage > 0) {
                Asset::handle(new Income($gameLevelingOrderConsult->poundage, 6, static::$order->trade_no, '协商手续费收入', static::$order->take_parent_user_id));
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 19);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 写入基础数据
            OrderBasicData::createData(static::$order);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 申请仲裁
     * @param string $reason
     * @throws Exception
     */
    public function applyComplain($reason = '无申请仲裁原因')
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [13, 14, 15])) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 记录订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::create([
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'status' => static::$order->status,
            ]);

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[仲裁中]状态！";

            static::$order->status = 16;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 20, $description);

            // 将仲裁数据写入仲裁表
            $initiator = static::initiator();

            GameLevelingOrderComplain::updateOrCreate([
                'game_leveling_order_trade_no' => static::$order->trade_no,
            ], [
                'user_id' => static::$user->id,
                'parent_user_id' => static::$user->parentInfo()->id,
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'amount' => 0,
                'security_deposit' => 0,
                'efficiency_deposit' => 0,
                'poundage' => 0,
                'reason' => $reason,
                'result' => '',
                'remark' => '',
                'status' => 1,
                'initiator' => $initiator,
            ]);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 16);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 发送短信
            static::sendMessage(5, '代练订单申请仲裁短信');
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 取消仲裁
     * @throws Exception
     */
    public function cancelComplain()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 16) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 21, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 更改仲裁表状态
            GameLevelingOrderComplain::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->update(['status' => 3]);

            // 订单数量角标
            static::orderCount(16, $gameLevelingOrderPreviousStatus->status);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 客服仲裁
     * @param int $amount 支付代练费
     * @param int $deposit
     * @param int $poundage
     * @throws Exception
     */
    public function arbitration($amount = 0, $deposit = 0, $poundage = 0)
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 16) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            if (!is_numeric($amount) || !is_numeric($deposit) || !is_numeric($poundage)) {
                throw new GameLevelingOrderOperateException('参数类型不合法!');
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已仲裁]状态！";

            static::$order->status = 21;
            static::$order->complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 26, $description);

            // 更改仲裁表状态和手续费等数据
            $handleDeposit = static::handleDeposit($amount, $deposit);
            $gameLevelingOrderComplain = GameLevelingOrderComplain::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->first();

            $gameLevelingOrderComplain->amount = $amount;
            $gameLevelingOrderComplain->security_deposit = $handleDeposit['security_deposit'];
            $gameLevelingOrderComplain->efficiency_deposit = $handleDeposit['efficiency_deposit'];
            $gameLevelingOrderComplain->poundage = $poundage;
            $gameLevelingOrderComplain->status = 2;
            $gameLevelingOrderComplain->save();

            // 当前操作人是否是该平台
            if (! config('gameleveling.third')[static::$user->parentInfo()->id] || config('gameleveling.third')[static::$user->parentInfo()->id] != static::$order->platform_id) {
                throw new GameLevelingOrderOperateException('当前操作人不是该订单所有者!');
            }

            // 手续费 <= 协商双金
            if ($gameLevelingOrderComplain->poundage > bcadd($gameLevelingOrderComplain->security_deposit, $gameLevelingOrderComplain->efficiency_deposit)) {
                throw new GameLevelingOrderOperateException('仲裁手续费超出了协商双金!');
            }

            //（发单剩余代练费收入，支出手续费，收入双金)
            $userAmount = bcsub(static::$order->amount, $gameLevelingOrderComplain->amount);
            if ($userAmount > 0) {
                Asset::handle(new Income($userAmount, 7, static::$order->trade_no, '退回仲裁代练费', static::$order->parent_user_id));
            }

            if ($gameLevelingOrderComplain->security_deposit > 0) {
                Asset::handle(new Income($gameLevelingOrderComplain->security_deposit, 10, static::$order->trade_no, '仲裁安全保证金收入', static::$order->parent_user_id));
            }

            if ($gameLevelingOrderComplain->efficiency_deposit > 0) {
                Asset::handle(new Income($gameLevelingOrderComplain->efficiency_deposit, 11, static::$order->trade_no, '仲裁效率保证金收入', static::$order->parent_user_id));
            }

            if ($gameLevelingOrderComplain->poundage > 0) {
                Asset::handle(new Expend($gameLevelingOrderComplain->poundage, 3, static::$order->trade_no, '仲裁手续费支出', static::$order->parent_user_id));
            }

            // (接单收入代练费，收入退回双金，收入手续费)
            if ($gameLevelingOrderComplain->amount > 0) {
                Asset::handle(new Income($gameLevelingOrderComplain->amount, 12, static::$order->trade_no, '仲裁代练费收入', static::$order->take_parent_user_id));
            }

            $backSecurityDeposit = bcsub(static::$order->security_deposit, $gameLevelingOrderComplain->security_deposit);
            if ($backSecurityDeposit > 0) {
                Asset::handle(new Income($backSecurityDeposit, 8, static::$order->trade_no, '仲裁安全保证金退回', static::$order->take_parent_user_id));
            }

            $backEfficiencyDeposit = bcsub(static::$order->efficiency_deposit, $gameLevelingOrderComplain->efficiency_deposit);
            if ($backEfficiencyDeposit > 0) {
                Asset::handle(new Income($backEfficiencyDeposit, 9, static::$order->trade_no, '仲裁效率保证金退回', static::$order->take_parent_user_id));
            }

            if ($gameLevelingOrderComplain->poundage > 0) {
                Asset::handle(new Income($gameLevelingOrderComplain->poundage, 6, static::$order->trade_no, '仲裁手续费收入', static::$order->take_parent_user_id));
            }

            // 订单数量角标
            static::orderCount(16, 21);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 写入基础数据
            OrderBasicData::createData(static::$order);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 申请验收
     * @throws Exception
     */
    public function applyComplete()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 13) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 记录订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::create([
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'status' => static::$order->status,
            ]);

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[待验收]状态！";

            static::$order->status = 14;
            static::$order->apply_complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 28, $description);

            // 写入 redis 24H自动验收
            $now = Carbon::now()->toDateTimeString();
            $key = static::$order->trade_no;
            Redis::hSet('complete_orders', $key, $now);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 14);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 发送短信
            static::sendMessage(3, '代练订单申请验收短信');
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 取消验收
     * @throws Exception
     */
    public function cancelComplete()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 14) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 29, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 订单数量角标
            static::orderCount(14, $gameLevelingOrderPreviousStatus->status);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 完成验收
     * @throws Exception
     */
    public function complete()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 14) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已结算]状态！";

            static::$order->status = 20;
            static::$order->complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 12, $description);

            // 流水
            if (static::$order->amount > 0) {
                Asset::handle(new Income(static::$order->amount, 12, static::$order->trade_no, '代练订单完成收入', static::$order->take_parent_user_id));
            }

            if (static::$order->security_deposit > 0) {
                Asset::handle(new Income(static::$order->security_deposit, 8, static::$order->trade_no, '订单完成退回安全保证金', static::$order->take_parent_user_id));
            }

            if (static::$order->efficiency_deposit > 0) {
                Asset::handle(new Income(static::$order->efficiency_deposit, 9, static::$order->trade_no, '订单完成退回效率保证金', static::$order->take_parent_user_id));
            }

            // 订单数量角标
            static::orderCount(14, 20);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 发送短信
            static::sendMessage(2, '代练订单完成短信');

            // 写入基础数据
            OrderBasicData::createData(static::$order);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 异常
     * @throws Exception
     */
    public function anomaly()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 13) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[代练中]设置为[异常中]状态！";

            static::$order->status = 17;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 30, $description);

            // 订单数量角标
            static::orderCount(13, 17);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 取消异常
     * @throws Exception
     */
    public function cancelAnomaly()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 17) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[异常中]设置为[代练中]状态！";

            static::$order->status = 13;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 31, $description);

            // 订单数量角标
            static::orderCount(17, 13);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 强制撤销
     * @throws Exception
     */
    public function forceDelete()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [13, 14, 15, 16, 17, 18])) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[强制撤单]状态！";

            static::$order->status = 23;
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 25, $description);

            // 流水
            if (static::$order->amount > 0) {
                Asset::handle(new Income(static::$order->amount, 7, static::$order->trade_no, '强制撤单退回代练费', static::$order->parent_user_id));
            }

            if (static::$order->security_deposit > 0) {
                Asset::handle(new Income(static::$order->security_deposit, 8, static::$order->trade_no, '强制撤单安全保证金退回', static::$order->take_parent_user_id));
            }

            if (static::$order->efficiency_deposit > 0) {
                Asset::handle(new Income(static::$order->efficiency_deposit, 9, static::$order->trade_no, '强制撤单效率保证金退回', static::$order->take_parent_user_id));
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 23);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 写入基础数据
            OrderBasicData::createData(static::$order);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 接单
     * @param $hatchetManName
     * @param string $hatchetManQq
     * @param string $hatchetManPhone
     * @return mixed
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function take($hatchetManName, $hatchetManQq = '', $hatchetManPhone = '')
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 1) {
                throw new GameLevelingOrderOperateException("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取该商户下面的黑名单打手
            $hatchetManBlacklist = HatchetManBlacklist::whereIn('user_id', [static::$order->parent_user_id, 0])
                ->first();

            if (isset($hatchetManBlacklist) && ! empty($hatchetManBlacklist)) {
                $blacklistQqs = HatchetManBlacklist::whereIn('user_id', [static::$order->parent_user_id, 0])
                    ->pluck('hatchet_man_qq')->toArray();

                $blacklistPhones = HatchetManBlacklist::whereIn('user_id', [static::$order->parent_user_id, 0])
                    ->pluck('hatchet_man_phone')->toArray();

                if (isset($blacklistQqs) && in_array($hatchetManQq, $blacklistQqs)) {
                    return response()->partner(0, '打手已被商户拉入黑名单');
                }
                if (isset($blacklistPhones) && in_array($hatchetManPhone, $blacklistPhones)) {
                    return response()->partner(0, '打手已被商户拉入黑名单');
                }
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[待接单]设置为[代练中]状态！";

            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('platform_id', config('gameleveling.third')[static::$user->id])
                ->first();

            if (! $gameLevelingPlatform) {
                throw new GameLevelingOrderOperateException('未找到对应的接单平台!');
            }

            static::$order->status = 13;
            static::$order->platform_id = $gameLevelingPlatform->platform_id;
            static::$order->platform_trade_no = $gameLevelingPlatform->platform_trade_no;
            static::$order->take_user_id = static::$user->id;
            static::$order->take_parent_user_id = static::$user->parentInfo()->id;
            static::$order->take_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            GameLevelingOrderLog::createOrderHistory(static::$order, 27, $description);

            // 检测发单人和平台余额
            static::checkUserAndPlatformBalance(static::$order);

            // 发单流水
            try {
                Asset::handle(new Expend(static::$order->amount, 6, static::$order->trade_no, '接单代练费支出', static::$order->parent_user_id));
            } catch (CustomException $exception) {
                if ($exception->getMessage() == '您的账号余额不足') {
                    // 发送短信通知发单人
                    $phone = User::where('id', static::$order->parent_user_id)->value('phone');
                    if ($phone) {
                        // 发送通知
                        event((new NotificationEvent('balanceNotice', [
                            'type' => 1, // 1 资金 2 短信
                            'user_id' => static::$order->parent_user_id,
                            'title' => '打手接单失败',
                            'message' => '您在淘宝发单平台的账户余额不足，打手接单失败，请立刻充值，保证业务正常运行。'
                        ])));
                    }
                    throw new GameLevelingOrderOperateException("您的账号余额不足!");
                } else {
                    throw new GameLevelingOrderOperateException("发单流水扣除异常!");
                }
            }
            // 接单流水
            $leftAmount = UserAsset::where('user_id', static::$order->take_parent_user_id)->value('balance');

            $deposit = bcadd(static::$order->security_deposit, static::$order->efficiency_deposit);

            if ($leftAmount <= 0 || $leftAmount < $deposit) {
                throw new GameLevelingOrderOperateException('接单商户余额不足!');
            }

            if (static::$order->security_deposit > 0) {
                Asset::handle(new Expend(static::$order->security_deposit, 4, static::$order->trade_no, '接单安全保证金支出', static::$order->take_parent_user_id));
            }

            if (static::$order->efficiency_deposit > 0) {
                Asset::handle(new Expend(static::$order->efficiency_deposit, 5, static::$order->trade_no, '接单效率保证金支出', static::$order->take_parent_user_id));
            }

            // 更新订单详情表数据
            $gameLevelingOrderDetail = GameLevelingOrderDetail::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->first();
            $gameLevelingOrderDetail->take_username = static::$user->username;
            $gameLevelingOrderDetail->take_parent_username = static::$user->parentInfo()->username;
            $gameLevelingOrderDetail->take_user_qq = static::$user->qq;
            $gameLevelingOrderDetail->take_user_phone = static::$user->phone;
            $gameLevelingOrderDetail->take_parent_qq = static::$user->parentInfo()->qq;
            $gameLevelingOrderDetail->take_parent_phone = static::$user->parentInfo()->phone;
            $gameLevelingOrderDetail->hatchet_man_name = $hatchetManName;
            $gameLevelingOrderDetail->hatchet_man_phone = $hatchetManPhone;
            $gameLevelingOrderDetail->hatchet_man_qq = $hatchetManQq;
            $gameLevelingOrderDetail->save();
            // 从自动下架任务中删除
            autoUnShelveDel(static::$order->trade_no);

            // 订单数量角标
            static::orderCount(1, 13);

            // 删除存在的订单报警
            Redis::hDel('our_notice_orders', static::$order->trade_no);

            // 发送短信
            static::sendMessage(1, '代练订单被接短信');

            // 写入基础数据
            OrderBasicData::createData(static::$order);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 回传
     * @param $platformTradeNo
     * @throws GameLevelingOrderOperateException
     */
    public function callback($platformTradeNo)
    {
        DB::beginTransaction();
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('platform_id', config('gameleveling.third')[static::$user->id])
                ->first();

            if (! $gameLevelingPlatform) {
                GameLevelingPlatform::create([
                    'game_leveling_order_trade_no' => static::$order->trade_no,
                    'platform_id' => config('gameleveling.third')[static::$user->id],
                    'platform_trade_no' => $platformTradeNo,
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 留言
     * @param $date
     * @param $message
     * @throws GameLevelingOrderOperateException
     */
    public function leaveMessage($date, $message = '')
    {
        DB::beginTransaction();
        try {
            $data = [
                'user_id' => static::$order->parent_user_id, // 发单用户ID
                'third' => static::$order->platform_id,
                'third_order_no' => static::$order->platform_trade_no, // 第三方平台单号
                'foreign_order_no' => static::$order->channel_order_trade_no, // 天猫单号
                'order_no' => static::$order->trade_no, // 我们平台单号
                'date' => $date, // 第三方平台单号留言时间
                'contents' => $message, // 第三方平台单号留言内容
            ];

            LevelingMessage::create($data);

            levelingMessageCount(static::$order->parent_user_id, 1, 1);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new GameLevelingOrderOperateException('订单操作异常!');
        }
        DB::commit();
    }
}