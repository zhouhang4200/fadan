<?php

namespace App\Services;

use App\Models\GameLevelingOrderComplain;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\OrderHistory;
use App\Extensions\Asset\Income;
use App\Extensions\Asset\Expend;
use App\Models\GameLevelingOrder;
use App\Http\Controllers\Controller;
use App\Models\GameLevelingOrderConsult;
use App\Models\GameLevelingOrderPreviousStatus;

class OrderOperateController extends Controller
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
    public static function createOrderHistory($type, $description = '')
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
    public static function handleDeposit($amount = 0, $deposit = 0)
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
    public static function initiator()
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
     * 上架
     */
    public function onSale()
    {
        DB::beginTransaction();
        try {
            // 修改订单状态和记录订单日志
            static::$order->status = 13;
            static::$order->save();

            $description = "用户[".static::$user->username."]将订单从[已下架]设置为[待接单]状态！";
            static::createOrderHistory(14, $description);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
            // 修改订单状态和记录订单日志
            static::$order->status = 22;
            static::$order->save();

            $description = "用户[".static::$user->username."]将订单从[待接单]设置为[已下架]状态！";
            static::createOrderHistory(15, $description);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已撤单]状态！";

            static::$order->status = 24;
            static::$order->save();
            static::createOrderHistory(23, $description);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
            // 记录订单前一个状态
            GameLevelingOrderPreviousStatus::create([
                'game_leveling_order_trade_no' => static::$order->trade_no,
                'status' => static::$order->status
            ]);

            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已锁定]状态！";

            static::$order->status = 18;
            static::$order->save();
            static::createOrderHistory(16, $description);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
            // 记录订单前一个状态
            GameLevelingOrderPreviousStatus::create([
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
            // 修改订单状态和记录订单日志
            $description = "用户[".static::$user->username."]将订单从[".config('order.status_leveling')[static::$order->status]."]设置为[已协商]状态！";

            static::$order->status = 19;
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
                throw new Exception('手续费超出了协商双金!');
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
            // 记录订单前一个状态
            GameLevelingOrderPreviousStatus::create([
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
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
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-operate', ['trade_no' => static::$order, 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new Exception('订单操作异常!');
        }
        DB::commit();
    }
}