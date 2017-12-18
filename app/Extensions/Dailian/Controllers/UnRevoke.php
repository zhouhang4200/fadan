<?php

namespace App\Extensions\Dailian\Controllers;

class UnRevoke extends DailianAbstract implements DailianInterface
{
    //取消撤销
    protected $acceptableStatus = [15]; // 状态：15撤销中
	protected $beforeHandleStatus = 15; // 操作之前的状态:15撤销中
    protected $handledStatus; // 状态：操作之后的状态
    protected $type             = 19; // 操作：19取消撤销
	// 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($no, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $no;
        	$this->userId  = $userId;
        	// 获取锁定前的状态
        	$this->$handledStatus = unserialize(OrderHistory::where('order_no', $no)->latest('created_at')->value('before'))->status;
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
