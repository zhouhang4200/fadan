<?php

namespace App\Extensions\Dailian\Controllers;

use App\Events\OrderFinish;
use App\Events\OrderReceiving;
use DB;
use Asset;
use Carbon\Carbon;
use App\Models\User;
use App\Services\Show91;
use App\Models\UserAsset;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Extensions\Asset\Expend;
use App\Exceptions\DailianException;
use Psy\Exception\ErrorException;

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
     * 
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费/安全保证金]
     * @param  [type] $apiDeposit  [回传双金/ 效率保证金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
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
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
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
     * @return [type] [description]
     */
    public function updateAsset()
    {
        DB::beginTransaction();
        try {
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
        } catch (DailianException $e) {
            DB::rollback();
            throw new DailianException($e->getMessage());
        }
        DB::commit();
    }

    /**
     * 检车接单账户余额
     * @return [type] [description]
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
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                       
                // 遍历 平台 =》 平台订单名称
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果确定为某个第三方平台，则写入第三方平台号和订单号
                    if (config('leveling.third')[$this->userId] == $third) {
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
                        // 如果存在这个平台订单，则下架此平台订单
                        if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                            // 控制器-》方法-》参数
                            call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDatas]);
                        }
                    }
                }
            }



            /**
             * 以下只适用于 91 和 代练妈妈
             * @var [type]
             */
            $now = Carbon::now()->toDateTimeString();
            // 订单详情
            $orderDetails = $this->checkThirdClientOrder($this->order);
            // 更新接单时间
            OrderDetail::where('order_no', $this->order->no)
                ->where('field_name', 'receiving_time')
                ->update(['field_value' => $now]);

            // 根据userid, 判断是哪个平台接单
            switch ($this->userId) {
                case 8456: 
                    // 更新第三方平台为 show91
                    OrderDetail::where('order_no', $this->order->no)
                        ->where('field_name', 'third')
                        ->update(['field_value' => 1]);

                    // 更新 third_order_no 为对应平台的订单号
                    OrderDetail::where('order_no', $this->order->no)
                        ->where('field_name', 'third_order_no')
                        ->update(['field_value' => $orderDetails['show91_order_no']]);

                    if ($orderDetails['dailianmama_order_no']) {                     
                        // 下架其他代练平台订单
                        DailianMama::closeOrder($this->order);
                        // 代练妈妈删除订单
                        DailianMama::deleteOrder($this->order);
                    }
                    break;
                case config('dailianmama.qs_user_id'):
                    // 更新第三方平台为 dailianmam
                    OrderDetail::where('order_no', $this->order->no)
                        ->where('field_name', 'third')
                        ->update(['field_value' => 2]);

                    // 更新 third_order_no 为对应平台的订单号
                    OrderDetail::where('order_no', $this->order->no)
                        ->where('field_name', 'third_order_no')
                        ->update(['field_value' => $orderDetails['dailianmama_order_no']]);

                    if ($orderDetails['show91_order_no']) {
                        // 撤单91平台订单
                        $options = ['oid' => $orderDetails['show91_order_no']]; 
                        // 91代练下单
                        Show91::chedan($options);
                    }
                    // 获取代练妈妈平台接单打手名称
                    $orderInfo = DailianMama::orderinfo($this->order);

                    OrderDetail::where('order_no', $this->order->no)
                        ->where('field_name', 'hatchet_man_name')
                        ->update(['field_value' => $orderInfo['data']['userinfo']['nickname']]);
                    break;
                default:
                    throw new DailianException('未找到订单对应的第三方平台!');
                    break;
            }
            // 调用事件
            try {
                event(new OrderReceiving($this->order));
            } catch (ErrorException $errorException) {
                myLog('receiving', [$errorException->getMessage()]);
            } catch (\Exception $exception) {
                myLog('receiving', [$exception->getMessage()]);
            }

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
}
