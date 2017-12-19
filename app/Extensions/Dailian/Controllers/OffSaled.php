<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;

class OffSaled extends DailianAbstract implements DailianInterface
{
    //已下架
    protected $acceptableStatus = [12]; // 状态：12未接单
	protected $beforeHandleStatus = 12; // 操作之前的状态:12未接单
    protected $handledStatus    = 22; // 状态：22已下架
    protected $type             = 15; // 操作：15下架
	// 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($orderNo, $userId, $apiAmount = null, $apiDeposit = null, $apiService = null, $writeAmount = null)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            // 获取订单对象
            $this->getObject();
        	$this->beforeHandleStatus = $this->getOrder()->status ?? 12;
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
