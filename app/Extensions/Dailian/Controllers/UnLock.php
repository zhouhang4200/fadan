<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\DailianMama;
use App\Models\OrderHistory;
use App\Exceptions\DailianException; 
/**
 * 取消锁定操作
 */
class UnLock extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [18]; // 状态：18锁定
	protected $beforeHandleStatus = 18; // 操作之前的状态:18锁定
    protected $handledStatus;   // 操作后状态：
    protected $type             = 17; // 操作：17取消锁定

	/**
     * [run 取消锁定 -> 锁定前状态]
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
            // 获取锁定前的状态
            $this->handledStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('id')->value('before'))['status'];
    		// 获取订单对象
		    $this->getObject();
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
            $this->orderCount();
            $this->after();
            // 如果申请验收改为别的状态，删除redis里面的订单
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

    /**
     * 调用外部锁定发接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                $orderDetails = $this->checkThirdClientOrder($this->order);

                switch ($orderDetails['third']) {
                    case 1:
                        // 91 取消锁定
                        $options = ['oid' => $orderDetails['show91_order_no']];
                        Show91::changeOrderBlock($options);
                        break;
                    case 2:
                        // 代练妈妈解除锁定接口
                        DailianMama::operationOrder($this->order, 20010);
                        break;
                    default:
                        throw new DailianException('第三方接单平台不存在!');
                        break;
                }
                return true;
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
