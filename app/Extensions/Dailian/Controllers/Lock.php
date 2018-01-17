<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\Show91;
use App\Exceptions\DailianException as Exception; 

class Lock extends DailianAbstract implements DailianInterface
{
     //锁定
    protected $acceptableStatus = [13, 14, 17]; // 状态：13,代练中，14待验收，17异常
    protected $beforeHandleStatus; // 操作之前的状态:13,代练中
    protected $handledStatus    = 18; // 状态：18锁定
    protected $type             = 16; // 操作：16锁定

	/**
     * [run 锁定 -> 锁定]
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

            $this->after();

            delRedisCompleteOrders($this->orderNo);

    	} catch (Exception $e) {
    		DB::rollBack();

            throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

     /**
     * 调用外部锁定发接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $thirdOrderNo = $this->order->detail()->where('field_name', 'third_order_no')->value('field_value');

                    if (! $thirdOrderNo) {
                        throw new Exception('第三方订单号不存在');
                    }

                    $options = [
                        'oid' => $thirdOrderNo,
                    ];
                    // 结果
                    Show91::changeOrderBlock($options);
                }
                return true;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
