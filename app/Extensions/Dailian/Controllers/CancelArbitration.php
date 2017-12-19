<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Models\OrderHistory;

class CancelArbitration extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [16]; // 状态：16仲裁中
	protected $beforeHandleStatus = 16; // 操作之前的状态:16仲裁中
    protected $handledStatus;// 操作后的状态
    protected $type             = 21; // 操作：21取消仲裁
	// 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($orderNo, $userId, $apiAmount = null, $apiDeposit = null, $apiService = null, $writeAmount = null)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->handledStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('created_at')->value('before'))['status'];
    		// 获取订单对象
		    $this->getObject();
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
