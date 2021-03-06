<?php

namespace App\Extensions\Dailian\Controllers;

use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use DB;
use Asset;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Services\Show91;
use App\Models\UserAsset;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Events\OrderReceiving;
use App\Exceptions\AssetException;
use App\Extensions\Asset\Expend;
use App\Exceptions\DailianException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

/**
 * 接单操作
 * @package App\Extensions\Dailian\Controllers
 */
class Playing extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [1]; // 状态：未接单
    protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 13; // 状态：代练中
    protected $type             = 27; // 操作：接单

    /**
     * @internal param $ [type] $orderNo     [订单号]
     * @internal param $ [type] $userId      [操作人]
     * @internal param $ [type] $apiAmount   [回传代练费/安全保证金]
     * @internal param $ [type] $apiDeposit  [回传双金/ 效率保证金]
     * @internal param $ [type] $apiService  [回传代练手续费]
     * @internal param $ [type] $writeAmount [协商代练费]
     */
    public function run($orderNo, $userId, $runAfter = 1)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->runAfter = $runAfter;
            // 获取订单对象
            $this->getObject();
        	$this->beforeHandleStatus = $this->getOrder()->status;
		    // 创建操作前的订单日志详情
		    $this->createLogObject();
		    // 设置订单属性
		    $this->setAttributes();
		    // 保存更改状态后的订单
		    $this->save();
		    // 更新平台资产
		    $this->updateAsset();
		    // 订单日志描述
		    $this->setDescription();
		    // 保存操作日志
		    $this->saveLog();
            $this->after();
            $this->orderCount();
            // 删除状态不在 申请验收 的redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 从自动下架任务中删除
            autoUnShelveDel($this->orderNo);
            $this->checkUserBalance();
            $this->runEvent();
        } catch (DailianException $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '接单', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException($exception->getMessage());
    	} catch (AssetException $exception) {
            // 资金异常
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '接单', '订单号' => $this->orderNo, 'user' => $this->userId, $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
    	DB::commit();

        return true;
    }

     // 保存更改状态后的订单
    public function save()
    {
        $this->order->status = $this->handledStatus;
        $this->order->gainer_user_id = $this->userId;
        $this->order->gainer_primary_user_id = User::find($this->userId)->getPrimaryUserId();

        if (!$this->order->save()) {
            throw new DailianException('订单操作失败');
        }

        return $this->order;
    }

    /**
     * [接单支出安全和效率保证金]
     * @throws DailianException
     */
    public function updateAsset()
    {

        // 兼容之前扣款流程,如果有扣过下单款则接单不再扣款,否则按新扣款流程在接单时才扣发单方款
        if(!checkPayment($this->order->no)) {

             try {
                 Asset::handle(new Expend($this->order->amount, 6, $this->order->no, '代练支出', $this->order->creator_primary_user_id));

             } catch (CustomException $exception) {
                 if ($exception->getMessage() == '您的账号余额不足') {
                     // 发送短信通知发单人
                     $phone = User::where('id', $this->order->creator_primary_user_id)->value('phone');
                     if ($phone) {
                         // 发送通知
                         event((new NotificationEvent('balanceNotice', [
                             'type' => 1, // 1 资金 2 短信
                             'user_id' => $this->order->creator_primary_user_id,
                             'title' => '打手接单失败',
                             'message' => '您在淘宝发单平台的账户余额不足，打手接单失败，请立刻充值，保证业务正常运行。'
                         ])));
                     }
                 }
                 throw new Exception($exception->getMessage());
             }

            // 写多态关联
            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new Exception('申请失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new Exception('申请失败');
            }
        }

        $orderDetails = OrderDetail::where('order_no', $this->order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();

        $safePayment = $orderDetails['security_deposit'];
        $effectPayment = $orderDetails['efficiency_deposit'];
        // 检测接单账号余额
        $this->checkGainerMoney($safePayment, $effectPayment);
        if ($safePayment > 0) {
            // 接单 安全保证金支出
            Asset::handle(new Expend($safePayment, 4, $this->order->no, '安全保证金支出', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }
        }

        if ($effectPayment > 0) {
            // 接单 效率保证金支出
            Asset::handle(new Expend($effectPayment, 5, $this->order->no, '效率保证金支出', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }
        }
    }

    /**
     * 检车接单账户余额
     * @param $safePayment
     * @param $effectPayment
     * @throws DailianException
     */
    public function checkGainerMoney($safePayment, $effectPayment)
    {
        // 接单商户余额
        $leftAmount = UserAsset::where('user_id', $this->order->gainer_primary_user_id)->value('balance');

        $doublePayment = bcadd($safePayment, $effectPayment);

        if (! $leftAmount || $leftAmount < $doublePayment) {
            throw new DailianException('商户余额不足');
        }
    }

    /**
     * 接单之后，计算接单时间
     * @throws DailianException
     */
    public function after()
    {
        if ($this->runAfter) {
             /**
             * 以下只适用于 91 和 代练妈妈
             * @var [type]
             */
            $now = Carbon::now()->toDateTimeString();
            // 订单详情
            $orderDetails = $this->checkThirdClientOrder($this->order);

            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 遍历 平台 =》 平台订单名称
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果确定为某个第三方平台，则写入第三方平台号和订单号
                    if (config('leveling.third')[$this->userId] == $third) {
                        if (! isset($orderDatas[$thirdOrderNoName]) || empty($orderDatas[$thirdOrderNoName])) {
                            throw new DailianException('订单号不存在，请联系淘宝代练平台配置关联订单号'); 
                        }
                        // 如果知道了是某个平台，并且存在平台订单号，则写入数据
                        if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                             // 更新第三方平台为 show91
                            OrderDetail::where('order_no', $this->order->no)
                                ->where('field_name', 'third')
                                ->update(['field_value' => $third]);

                            // 更新 third_order_no 为对应平台的订单号
                            OrderDetail::where('order_no', $this->order->no)
                                ->where('field_name', 'third_order_no')
                                ->update(['field_value' => $orderDatas[$thirdOrderNoName]]);
                        }
                    // 其他平台订单撤单
                    } else {
                        if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                            try {
                                // 控制器-》方法-》参数
                                call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDatas]);
                            } catch (Exception $exception) {

                            }
                        }
                    }
                }
            }

            // 更新接单时间
            OrderDetail::where('order_no', $this->order->no)
                ->where('field_name', 'receiving_time')
                ->update(['field_value' => $now]);

            // 写入留言获取
            $updateAfterOrderDetail = $this->checkThirdClientOrder($this->order);
            levelingMessageAdd($this->order->creator_primary_user_id,
                $this->order->no,
                $updateAfterOrderDetail['third_order_no'],
                $updateAfterOrderDetail['third'],
                $updateAfterOrderDetail['source_order_no'],
                0
            );
        }
    }

    /**
     * 检测发单人账号余额
     */
    public function checkUserBalance()
    {
        if(UserAsset::balance($this->order->creator_primary_user_id) < 2000) {
            // https://oapi.dingtalk.com/robot/send?access_token=b5c71a94ecaba68b9fec8055100324c06b1d98a6cd3447c5d05e224efebe5285 代练小组
            // https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8 测试用
            $userInfo = User::find($this->order->creator_primary_user_id);

            $existCache = Cache::get($this->order->creator_primary_user_id);
            if (!$existCache && in_array($this->order->creator_primary_user_id, [8317, 8803, 8790, 8785, 8711, 8523])) {
                $now = Carbon::now();
                $expiresAt = $now->diffInMinutes(Carbon::parse(date('Y-m-d'))->endOfDay());
                Cache::put($this->order->creator_primary_user_id, '1', $expiresAt);

                // 发送通知
                event((new NotificationEvent('balanceNotice', [
                    'type' => 1, // 1 资金 2 短信
                    'user_id' => $this->order->creator_primary_user_id,
                    'title' => '余额不足',
                    'message' => '[淘宝发单平台]提醒您，您的账户(ID:' . $this->order->creator_primary_user_id . ')余额已不足2000元，请及时充值，保证业务正常进行。'
                ])));

//                sendSms(0, $this->order->no, $userInfo->phone, '[淘宝发单平台]提醒您，您的账户(ID:' . $this->order->creator_primary_user_id . ')余额已不足2000元，请及时充值，保证业务正常进行。', '');
            }

            $client = new Client();
            $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => '发单商户ID: ' . $this->order->creator_primary_user_id . ', 昵称(' . $userInfo->nickname . ')。账户余额低于2000元, 已发送短信通知, 请运营同事及时跟进。'
                    ],
                    'at' => [
                        'isAtAll' => true
                    ]
                ]
            ]);
        }
        // 发单平台余额检测
        $platformBalanceAlarm = config('leveling.balance_alarm')[$this->order->gainer_primary_user_id];
        if (UserAsset::balance($this->order->gainer_primary_user_id) < $platformBalanceAlarm) {
            $userInfo = User::find($this->order->gainer_primary_user_id);

            $existCache = Cache::get($this->order->gainer_primary_user_id);
            if (!$existCache) {
                $now = Carbon::now();
                $expiresAt = $now->diffInMinutes(Carbon::parse(date('Y-m-d'))->endOfDay());
                Cache::put($this->order->gainer_primary_user_id, '1', $expiresAt);

//                sendSms(0, $this->order->no, $userInfo->phone, '[淘宝发单平台]提醒您，您的账户(ID:' . $this->order->gainer_primary_user_id . ')余额已不足' . $platformBalanceAlarm . '元，请及时充值，保证业务正常进行。', '');
            }

            $client = new Client();
            $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                'json' => [
                    'msgtype' => 'text',
                    'text' => [
                        'content' => '接单平台ID: ' . $this->order->gainer_primary_user_id . ', 昵称(' . $userInfo->nickname . ')。账户余额低于' . $platformBalanceAlarm . ', 已发送短信通知, 请运营同事及时跟进。'
                    ],
                    'at' => [
                        'isAtAll' => true
                    ]
                ]
            ]);
        }
    }
}
