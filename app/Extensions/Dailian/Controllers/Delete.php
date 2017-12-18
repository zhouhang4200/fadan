<?php

namespace App\Extensions\Dailian\Controllers;

class Delete extends DailianAbstract implements DailianInterface
{
     //删除 -》 删除
    protected $acceptableStatus = [12, 22]; // 状态：13,代练中，12未接单， 22已下架
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 24; // 状态：24已删除
    protected $type             = 23; // 操作：23删除
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
