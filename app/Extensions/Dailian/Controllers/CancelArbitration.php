<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\Show91;
use App\Models\OrderHistory;
use App\Models\LevelingConsult;
use App\Services\DailianMama;
use App\Exceptions\DailianException; 
use App\Models\OrderDetail;

/**
 * 取消仲裁操作
 */
class CancelArbitration extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [16]; // 状态：16仲裁中
    protected $beforeHandleStatus = 16; // 操作之前的状态:16仲裁中
    protected $handledStatus;// 操作后的状态
    protected $type               = 21; // 操作：21取消仲裁
    
	/**
     * [取消仲裁 -》 仲裁申请前状态]
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit  [回传双金]
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
            $this->getBeforeStatus($orderNo);
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
            $this->changeConsultStatus();
            $this->after();
            $this->orderCount();
            // 删除状态不是 申请验收 的 redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 如果还原前一个状态为 申请验收 ，redis 加订单
            addRedisCompleteOrders($this->orderNo, $this->handledStatus);

    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();
        return true;
    }

    public function getBeforeStatus($orderNo)
    {
        $beforeStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('id')->value('before'))['status'];
        // 获取上一条操作记录，如果上一条为仲裁中，则取除了仲裁中和撤销中的最早的一条状态
        if (! $beforeStatus) {
            throw new DailianException('订单操作记录不存在');
        }
        if ($beforeStatus == 16 || $beforeStatus == 18) {
            $orderHistories = OrderHistory::where('order_no', $orderNo)->latest('id')->get();
            $arr = [];
            foreach ($orderHistories as $key => $orderHistory) {
                $status = unserialize($orderHistory->before);

                if (isset($status['status']) && !in_array($status['status'], [15, 16, 18])) {
                    $arr[$key] = $status['status'];
                }
            }
            $this->handledStatus = current($arr);
        } else {
            $this->handledStatus = $beforeStatus;
        }
    }

    public function changeConsultStatus()
    {
        LevelingConsult::where('order_no', $this->orderNo)->update(['complain' => 0]);
    }

    /**
     * 撤销申诉
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                $orderDetails = $this->checkThirdClientOrder($this->order);
                $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                if (! $consult) {
                    throw new DailianException('订单申诉或协商记录不存在!');
                }
                    
                switch ($orderDetails['third']) {
                    case 1:
                        // 91 取消申诉接口
                        $options = ['aid' => $orderDetails['show91_order_no']];
                        Show91::cancelAppeal($options);
                        break;
                    case 2:
                        // 代练妈妈取消申诉接口
                        DailianMama::operationOrder($this->order, 20008);
                        break;
                    default:
                        throw new DailianException('第三方接单平台不存在!');
                        break;
                }
                // $orderDetails = OrderDetail::where('order_no', $this->order->no)
                //     ->pluck('field_value', 'field_name')
                //     ->toArray();

                // if ($orderDetails['third'] == 1) { //91代练
                //     $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                //     if (! $consult) {
                //         throw new DailianException('订单申诉和协商记录不存在');
                //     }

                //     if (! $orderDetails['third_order_no']) {
                //         throw new DailianException('第三方订单号不存在');
                //     }

                //     $options = [
                //         'aid' => $orderDetails['third_order_no'], // 撤销id 可以用单号
                //     ];
                //     // 结果
                //     Show91::cancelAppeal($options);
                // }
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
