<?php

namespace App\Services;

use Cache;
use Redis;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\UserAsset;
use App\Models\OrderHistory;
use App\Extensions\Asset\Income;
use App\Exceptions\CustomException;
use App\Extensions\Asset\Expend;
use App\Events\NotificationEvent;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingOrderComplain;
use App\Models\GameLevelingOrderConsult;
use App\Models\GameLevelingOrderPreviousStatus;

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
        $data = [
            'order_no' => static::$order->trade_no,
            'user_id' => static::$order->user_id,
            'admin_user_id' => static::$adminUser ?? null,
            'type' => $type,
            'name' => config('order.operation_type')[$type],
            'description' => $description,
            'before' => '',
            'after' => '',
            'created_at' => Carbon::now()->toDateTimeString(),
            'creator_primary_user_id' => static::$order->parent_user_id,
        ];

        OrderHistory::create($data);
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
            throw new Exception('协商代练金额超出代练订单金额!');
        }

        if ($deposit < 0 || $deposit > bcadd(static::$order->security_deposit, static::$order->efficiency_deposit)) {
            throw new Exception('协商双金超出代练订单双金!');
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
            throw new Exception('当前操作用户不存在!');
        }
    }

    /**
     * 检测发单人和平台余额
     */
    public static function checkUserAndPlatformBalance()
    {
        if(UserAsset::balance(static::$order->parent_user_id) < 2000) {
            // https://oapi.dingtalk.com/robot/send?access_token=b5c71a94ecaba68b9fec8055100324c06b1d98a6cd3447c5d05e224efebe5285 代练小组
            // https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8 测试用
            $userInfo = User::find(static::$order->parent_user_id);

            $existCache = Cache::get(static::$order->parent_user_id);
            if (!$existCache && in_array(static::$order->parent_user_id, [8317, 8803, 8790, 8785, 8711, 8523])) {
                $now = Carbon::now();
                $expiresAt = $now->diffInMinutes(Carbon::parse(date('Y-m-d'))->endOfDay());
                Cache::put(static::$order->parent_user_id, '1', $expiresAt);

                // 发送通知
                event((new NotificationEvent('balanceNotice', [
                    'type' => 1, // 1 资金 2 短信
                    'user_id' => static::$order->parent_user_id,
                    'title' => '余额不足',
                    'message' => '[淘宝发单平台]提醒您，您的账户(ID:' . static::$order->parent_user_id . ')余额已不足2000元，请及时充值，保证业务正常进行。'
                ])));

//                sendSms(0, $this->order->no, $userInfo->phone, '[淘宝发单平台]提醒您，您的账户(ID:' . static::$order->parent_user_id . ')余额已不足2000元，请及时充值，保证业务正常进行。', '');
            }

            $client = new Client();
            $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => '发单商户ID: ' . static::$order->parent_user_id . ', 昵称(' . $userInfo->nickname . ')。账户余额低于2000元, 已发送短信通知, 请运营同事及时跟进。'
                    ],
                    'at' => [
                        'isAtAll' => true
                    ]
                ]
            ]);
        }
        // 发单平台余额检测
        $platformBalanceAlarm = config('leveling.balance_alarm')[static::$order->take_parent_id];
        if (UserAsset::balance(static::$order->take_parent_id) < $platformBalanceAlarm) {
            $userInfo = User::find(static::$order->take_parent_id);

            $existCache = Cache::get(static::$order->take_parent_id);
            if (!$existCache) {
                $now = Carbon::now();
                $expiresAt = $now->diffInMinutes(Carbon::parse(date('Y-m-d'))->endOfDay());
                Cache::put(static::$order->take_parent_id, '1', $expiresAt);

//                sendSms(0, $this->order->no, $userInfo->phone, '[淘宝发单平台]提醒您，您的账户(ID:' . static::$order->take_parent_id . ')余额已不足' . $platformBalanceAlarm . '元，请及时充值，保证业务正常进行。', '');
            }

            $client = new Client();
            $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => '接单平台ID: ' . static::$order->take_parent_id . ', 昵称(' . $userInfo->nickname . ')。账户余额低于' . $platformBalanceAlarm . ', 已发送短信通知, 请运营同事及时跟进。'
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
     * 上架
     */
    public function onSale()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 22) {
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }
            // 修改订单状态和记录订单日志
            static::$order->status = 13;
            static::$order->save();

            $description = "用户[".static::$user->username."]将订单从[已下架]设置为[待接单]状态！";
            static::createOrderHistory(14, $description);

            // 订单数量角标
            static::orderCount(22, 13);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            static::$order->status = 22;
            static::$order->save();

            $description = "用户[".static::$user->username."]将订单从[待接单]设置为[已下架]状态！";
            static::createOrderHistory(15, $description);

            // 从自动下架任务中删除
            autoUnShelveDel(static::$order->trade_no);

            // 订单数量角标
            static::orderCount(1, 22);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已撤单]状态！";

            static::$order->status = 24;
            static::$order->save();
            static::createOrderHistory(23, $description);

            // 从自动下架任务中删除
            autoUnShelveDel(static::$order->trade_no);

            // 订单数量角标
            static::orderCount(1, 24);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
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
            static::createOrderHistory(16, $description);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 18);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            static::createOrderHistory(17, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 订单数量角标
            static::orderCount(18, static::$order->status);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 申请协商
     * @param $amount
     * @param $deposit
     * @param $reason
     * @throws Exception
     */
    public function applyConsult($amount, $deposit, $reason)
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [13, 14, 17, 18])) {
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
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
            static::createOrderHistory(18, $description);

            // 将协商数据写入协商表
            $handleDeposit = static::handleDeposit($amount, $deposit);
            $initiator = static::initiator();

            GameLevelingOrderConsult::create([
                'user_id' => static::$user->id,
                'parent_user_id' => static::$user->parentInfo()->id,
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'amount' => $amount,
                'security_deposit' => $handleDeposit['security_deposit'],
                'efficiency_deposit' => $handleDeposit['efficiency_deposit'],
                'poundage' => 0,
                'reason' => $reason,
                'status' => 1,
                'initiator' => $initiator,
            ]);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 15);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            static::createOrderHistory(19, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 更改协商表状态
            GameLevelingOrderConsult::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->update(['status' => 3]);

            // 订单数量角标
            static::orderCount(15, static::$order->status);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 不同意协商
     * @throws Exception
     */
    public function refuseConsult()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 15) {
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            static::createOrderHistory(33, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 更改协商表状态
            GameLevelingOrderConsult::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->update(['status' => 3]);

            // 订单数量角标
            static::orderCount(15, static::$order->status);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已协商]状态！";

            static::$order->status = 19;
            static::$order->complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            static::createOrderHistory(24, $description);

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
                throw new Exception('当前操作人不是该订单拥有者!');
            }
            // 手续费 <= 协商双金
            if ($gameLevelingOrderConsult->poundage > bcadd($gameLevelingOrderConsult->security_deposit, $gameLevelingOrderConsult->efficiency_deposit)) {
                throw new Exception('协商手续费超出了协商双金!');
            }

            //（发单剩余代练费收入，支出手续费，收入双金)
            if ($userAmount = bcsub(static::$order->amount, $gameLevelingOrderConsult->amount) > 0) {
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
                Asset::handle(new Income($gameLevelingOrderConsult->amount, 12, static::$order->trade_no, '协商代练费收入', static::$order->parent_take_user_id));
            }

            if ($backSecurityDeposit = bcsub(static::$order->security_deposit, $gameLevelingOrderConsult->security_deposit) > 0) {
                Asset::handle(new Income($backSecurityDeposit, 8, static::$order->trade_no, '协商安全保证金退回', static::$order->parent_take_user_id));
            }

            if ($backEfficiencyDeposit = bcsub(static::$order->efficiency_deposit, $gameLevelingOrderConsult->efficiency_deposit) > 0) {
                Asset::handle(new Income($backEfficiencyDeposit, 9, static::$order->trade_no, '协商效率保证金退回', static::$order->parent_take_user_id));
            }

            if ($gameLevelingOrderConsult->poundage > 0) {
                Asset::handle(new Income($gameLevelingOrderConsult->poundage, 6, static::$order->trade_no, '协商手续费收入', static::$order->parent_take_user_id));
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 19);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 申请仲裁
     * @param string $reason
     * @throws Exception
     */
    public function applyComplain($reason = '')
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (! in_array(static::$order->status, [13, 14, 15])) {
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
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
            static::createOrderHistory(20, $description);

            // 将仲裁数据写入仲裁表
            $initiator = static::initiator();

            GameLevelingOrderComplain::create([
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            static::createOrderHistory(21, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 更改仲裁表状态
            GameLevelingOrderComplain::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->where('status', 1)
                ->update(['status' => 3]);

            // 订单数量角标
            static::orderCount(16, $gameLevelingOrderPreviousStatus->status);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已仲裁]状态！";

            static::$order->status = 21;
            static::$order->complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            static::createOrderHistory(26, $description);

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
            if (! config('leveling.third')[static::$user->parentInfo()->id] || config('leveling.third')[static::$user->parentInfo()->id] != static::$order->platform_id) {
                throw new Exception('当前操作人不是该订单所有者!');
            }

            // 手续费 <= 协商双金
            if ($gameLevelingOrderComplain->poundage > bcadd($gameLevelingOrderComplain->security_deposit, $gameLevelingOrderComplain->efficiency_deposit)) {
                throw new Exception('仲裁手续费超出了协商双金!');
            }

            //（发单剩余代练费收入，支出手续费，收入双金)
            if ($userAmount = bcsub(static::$order->amount, $gameLevelingOrderComplain->amount) > 0) {
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
                Asset::handle(new Income($gameLevelingOrderComplain->amount, 12, static::$order->trade_no, '仲裁代练费收入', static::$order->parent_take_user_id));
            }

            if ($backSecurityDeposit = bcsub(static::$order->security_deposit, $gameLevelingOrderComplain->security_deposit) > 0) {
                Asset::handle(new Income($backSecurityDeposit, 8, static::$order->trade_no, '仲裁安全保证金退回', static::$order->parent_take_user_id));
            }

            if ($backEfficiencyDeposit = bcsub(static::$order->efficiency_deposit, $gameLevelingOrderComplain->efficiency_deposit) > 0) {
                Asset::handle(new Income($backEfficiencyDeposit, 9, static::$order->trade_no, '仲裁效率保证金退回', static::$order->parent_take_user_id));
            }

            if ($gameLevelingOrderComplain->poundage > 0) {
                Asset::handle(new Income($gameLevelingOrderComplain->poundage, 6, static::$order->trade_no, '仲裁手续费收入', static::$order->parent_take_user_id));
            }

            // 订单数量角标
            static::orderCount(16, 21);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
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
            static::createOrderHistory(28, $description);

            // 写入 redis 24H自动验收
            $now = Carbon::now()->toDateTimeString();
            $key = static::$order->trade_no;
            Redis::hSet('complete_orders', $key, $now);

            // 订单数量角标
            static::orderCount($gameLevelingOrderPreviousStatus->status, 14);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 获取订单前一个状态
            $gameLevelingOrderPreviousStatus = GameLevelingOrderPreviousStatus::where('game_leveling_order_trade_no', static::$order->trade_no)
                ->latest('id')
                ->first();

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[".config('order.status_leveling')[$gameLevelingOrderPreviousStatus->status]."]状态！";

            static::$order->status = $gameLevelingOrderPreviousStatus->status;
            static::$order->save();
            static::createOrderHistory(29, $description);

            // 删除最后一条状态数据
            $gameLevelingOrderPreviousStatus->delete();

            // 订单数量角标
            static::orderCount(14, $gameLevelingOrderPreviousStatus->status);

            // 删除24小时自动验收队列
            Redis::hDel('complete_orders',  static::$order->trade_no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已结算]状态！";

            static::$order->status = 20;
            static::$order->complete_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            static::createOrderHistory(12, $description);

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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[代练中]设置为[异常中]状态！";

            static::$order->status = 17;
            static::$order->save();
            static::createOrderHistory(30, $description);

            // 订单数量角标
            static::orderCount(13, 17);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[异常中]设置为[代练中]状态！";

            static::$order->status = 13;
            static::$order->save();
            static::createOrderHistory(31, $description);

            // 订单数量角标
            static::orderCount(17, 13);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
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
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[强制撤单]状态！";

            static::$order->status = 23;
            static::$order->save();
            static::createOrderHistory(25, $description);

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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
        }
        DB::commit();
    }

    /**
     * 接单
     * @throws Exception
     */
    public function take()
    {
        DB::beginTransaction();
        try {
            // 订单当前状态是否可以修改
            if (static::$order->status != 1) {
                throw new Exception("操作失败！当前订单状态【".config('order.status_leveling')[static::$order->status]."】不可调用此操作！");
            }

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[待接单]设置为[代练中]状态！";

            static::$order->status = 13;
            static::$order->take_at = Carbon::now()->toDateTimeString();
            static::$order->save();
            static::createOrderHistory(27, $description);

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
                    throw new Exception("您的账号余额不足!");
                } else {
                    throw new Exception("发单流水扣除异常!");
                }
            }
            // 接单流水
            $leftAmount = UserAsset::where('user_id', static::$order->take_parent_user_id)->value('balance');

            $deposit = bcadd(static::$order->security_deposit, static::$order->efficiency_deposit);

            if ($leftAmount <= 0 || $leftAmount < $deposit) {
                throw new Exception('接单商户余额不足!');
            }

            if (static::$order->security_deposit > 0) {
                Asset::handle(new Expend(static::$order->security_deposit, 4, static::$order->trade_no, '接单安全保证金支出', static::$order->take_parent_user_id));
            }

            if (static::$order->efficiency_deposit > 0) {
                Asset::handle(new Expend(static::$order->efficiency_deposit, 5, static::$order->trade_no, '接单效率保证金支出', static::$order->take_parent_user_id));
            }

            // 检测发单人和平台余额
            static::checkUserAndPlatformBalance();

            // 从自动下架任务中删除
            autoUnShelveDel(static::$order->trade_no);

            // 订单数量角标
            static::orderCount(1, 13);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate-service-error', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
        }
        DB::commit();
    }
}