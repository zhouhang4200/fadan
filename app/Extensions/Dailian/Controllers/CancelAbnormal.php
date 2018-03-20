<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Redis;
use App\Services\DailianMama;
use App\Exceptions\DailianException; 

/**
 * 取消异常操作
 */
class CancelAbnormal extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [17]; // 状态：待验收
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 13; // 状态：代练中
    protected $type             = 31; // 操作：取消异常

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
            delRedisCompleteOrders($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 31);
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();
        return true;
    }

    public function after()
    {
        if ($this->runAfter) {
            try {
                $orderDetails = $this->checkThirdClientOrder($this->order);

                switch ($orderDetails['third']) {
                    case 1:
                        throw new DailianException('91平台没有此操作!');
                        break;
                    case 2:
                        // 代练妈妈取消异常
                        DailianMama::operationOrder($this->order, 20011);
                        break;
                    default:
                        throw new DailianException('第三方接单平台不存在!');
                        break;
                }
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
