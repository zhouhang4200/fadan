<?php

namespace App\Extensions\Dailian\Controllers;

class Revoking extends DailianAbstract implements DailianInterface
{
     //撤销中
    protected $acceptableStatus = [13, 14, 17, 18]; // 状态：18锁定
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 15; // 状态：15撤销中
    protected $type             = 18; // 操作：18撤销
	// 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($no, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $no;
        	$this->userId  = $userId;
        	// 获取锁定前的状态
        	$this->$beforeHandleStatus = $this->getObject()->status;
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
		    $this->logDescription();
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
