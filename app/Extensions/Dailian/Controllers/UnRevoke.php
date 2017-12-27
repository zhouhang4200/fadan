<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Models\OrderHistory;

class UnRevoke extends DailianAbstract implements DailianInterface
{
    //取消撤销
    protected $acceptableStatus = [15]; // 状态：15撤销中
	protected $beforeHandleStatus = 15; // 操作之前的状态:15撤销中
    protected $handledStatus; // 状态：操作之后的状态
    protected $type             = 19; // 操作：19取消撤销

	/**
     * [run 取消撤销 -> 撤销前的状态]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit  [回传双金]
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
        	// 获取锁定前的状态
            // 获取订单对象
            $this->getObject();
        	$this->handledStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('created_at')->value('before'))['status'];
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

            (new Lock)->run($orderNo, $userId);
    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }
}
