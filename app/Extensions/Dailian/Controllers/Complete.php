<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Income;
use Exception; 
use App\Models\Order; // 代练模型
use App\Models\OrderHistory; // 操作日志

class Complete extends DailianConstract implements DailianInterface
{
	protected $acceptableStatus = [14]; // 状态：14待验收
	protected $beforeHandleStatus = 14; // 操作之前的状态:14待验收
    protected $handledStatus    = 20; // 状态：20已结算
    protected $type             = 12; // 操作：12完成
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
		   	// 保存权重
		   	$this->saveWeight();
		    // 后续操作
		    $this->after();
    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    // 更新财产
    public function updateAsset()
    {
    	// 第二个参数为子类型，85 -> 代练完成收入，省略前面的8，具体参考config('tradetype.user_sub')
        Asset::handle(new Income($this->order->amount, 5, $this->order->no, '代练订单完成', $this->order->gainer_primary_user_id));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new Exception('申请失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new Exception('申请失败');
        }
    }
}
