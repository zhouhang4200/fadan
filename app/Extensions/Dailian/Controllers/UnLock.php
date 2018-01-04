<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Models\OrderHistory;

class UnLock extends DailianAbstract implements DailianInterface
{
     //取消锁定 -> 锁定前的状态
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
    public function run($orderNo, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
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
    	} catch (Exception $e) {
    		DB::rollBack();
    		echo json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
            exit;
            // throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

}
