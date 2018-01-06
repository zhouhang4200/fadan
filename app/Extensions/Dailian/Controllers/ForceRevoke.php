<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use App\Extensions\Asset\Income;
use App\Exceptions\DailianException as Exception; 

class ForceRevoke extends DailianAbstract implements DailianInterface
{
     //强制撤销 -》 撤销
    protected $acceptableStatus = [13, 14, 15, 16, 17, 18]; // 状态：
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 23; // 状态：强制撤销
    protected $type             = 25; // 操作：25强制撤销

	/**
     * [run 回传的 强制撤销操作 -》 强制撤销]
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
            // 获取订单对象
            $this->getObject();
        	$this->beforeHandleStatus = $this->getOrder()->status;
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

            throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * [退代练费给发单，退双金给接单]
     * @return [type] [description]
     */
    public function updateAsset()
    {
        DB::beginTransaction();
        try {
            // 发单 退回代练费
            Asset::handle(new Income($this->order->amount, 7, $this->order->no, '退回代练费', $this->order->creator_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new Exception('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new Exception('流水记录写入失败');
            }

            if ($this->order->detail()->where('field_name', 'security_deposit')->value('field_value')) {        
                // 接单 退回安全保证金
                Asset::handle(new Income($this->order->detail()->where('field_name', 'security_deposit')->value('field_value'), 8, $this->order->no, '安全保证金退回', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('流水记录写入失败');
                }
            }

            if ($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value')) {        
                // 接单 退效率保证金
                Asset::handle(new Income($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value'), 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('流水记录写入失败');
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
        DB::commit();
    }
}
