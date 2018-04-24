<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Exceptions\DailianException; 

/**
 * 取消验收操作
 */
class CancelComplete extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [14]; // 状态：待验收
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 13; // 状态：代练中
    protected $type             = 29; // 操作：取消验收

	/**
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费/安全保证金]
     * @param  [type] $apiDeposit  [回传双金/ 效率保证金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
     */
    public function run($orderNo, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
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
            $this->orderCount();

            delRedisCompleteOrders($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 29);
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (Exception $exception) {
            throw new DailianException($exception->getMessage());
        }
    	DB::commit();
        return true;
    }
}
