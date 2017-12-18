<?php

namespace App\Extensions\Dailian\Controllers;

class NoReceive extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [22]; // 已下架
    protected $beforeHandleStatus = 22; // 已下架
    protected $handledStatus    = 12; // 未接单
    protected $type             = 14; // 上架

    // 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($no, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $no;
        	$this->userId  = $userId;
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
