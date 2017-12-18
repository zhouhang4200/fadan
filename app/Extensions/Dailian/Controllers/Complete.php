<?php

namespace App\Extensions\Dailian\Controllers;

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

    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    // 流水，代练完成，接单商户完成代练收入
    public function updateAsset()
    {
        DB::beginTransaction();
        try {
        	// 接单 代练收入
            Asset::handle(new Income($this->order->amount, 12, $this->order->no, '代练订单完成收入', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new Exception('申请失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new Exception('申请失败');
            }

            // 接单 退回安全保证金
            Asset::handle(new Income($this->order->orderDetail->pluck('field_name')->security_deposit, 8, $this->order->no, '退回安全保证金', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new Exception('申请失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new Exception('申请失败');
            }

            // 接单 退效率保证金
            Asset::handle(new Income($this->order->orderDetail->pluck('field_name')->efficiency_deposit, 9, $this->order->no, '退回效率保证金', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new Exception('申请失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new Exception('申请失败');
            }
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }
}
