<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use App\Extensions\Asset\Income;
use App\Services\Show91;
use App\Exceptions\DailianException as Exception; 

class Complete extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [14]; // 状态：14待验收
    protected $beforeHandleStatus = 14; // 操作之前的状态:14待验收
    protected $handledStatus      = 20; // 状态：20已结算
    protected $type               = 12; // 操作：12完成
    
	/**
     * [run 完成 -> 已结算]
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit  [回传双金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
     */
    public function run($orderNo, $userId, $runAfter = 1)
    {	
    	DB::beginTransaction();
        try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->runAfter = $runAfter;
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

            // $this->after();

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

            if ($this->order->detail()->where('field_name', 'security_deposit')->value('field_value')) {    
                // 接单 退回安全保证金
                Asset::handle(new Income($this->order->detail()->where('field_name', 'security_deposit')->value('field_value'), 8, $this->order->no, '退回安全保证金', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }
            }

            if ($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value')) {
                // 接单 退效率保证金
                Asset::handle(new Income($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value'), 9, $this->order->no, '退回效率保证金', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }
            }

        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => $e->getMessage()]);
        }
        DB::commit();
    }

    /**
     * 订单验收结算
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练

                    $options = [
                        'oid' => $this->order->detail()->where('field_name', 'third_order_no')->value('field_value'), // 撤销id 可以用单号
                        'p' => '123456',
                    ];
                    // 结果
                    $result = Show91::accept($options);
                    $result = json_decode($result);

                    if ($result && $result->reason) {
                        $reason = $result->reason;
                        throw new Exception($reason);
                    }
                }
            } catch (Exception $e) {
                return response()->json(['status' => 0, 'message' => $e->getMessage()]);
            }
        }
    }
}
