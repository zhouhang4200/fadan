<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;

class Delete extends DailianAbstract implements DailianInterface
{
     //删除 -》 删除
    protected $acceptableStatus = [1, 22]; // 状态：1未接单， 22已下架
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 24; // 状态：24已删除
    protected $type             = 23; // 操作：23删除

	/**
     * [run 删除 -》 删除]
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit  [回传双金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
     */
    public function run($orderNo, $userId, $apiAmount = null, $apiDeposit = null, $apiService = null, $writeAmount = null)
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
		    $this->updateAsset($apiAmount = null, $apiDeposit = null, $apiService = null, $writeAmount = null);
		    // 订单日志描述
		    $this->setDescription();
		    // 保存操作日志
		    $this->saveLog();

    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }
}