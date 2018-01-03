<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Services\Show91;

class NoReceive extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [22]; // 已下架
    protected $beforeHandleStatus = 22; // 已下架
    protected $handledStatus      = 1; // 未接单
    protected $type               = 14; // 上架
    protected $runAfter           = 1;

    /**
     * [run 上架 ->未接单]
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

            $this->after();

    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * 调用外部上架接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $options = ['oid' => $this->order->detail()->where('field_name', 'third_order_no')->value('field_value')]; // 第三方订单号
                    // 结果
                    $result = Show91::grounding($options);
                    $result = json_decode($result);

                    if ($result->result && $result->reason) {
                        $reason = $result->reason ?? '下单失败!';
                        throw new Exception($reason);
                    }
                }
            } catch (Exception $e) {

            }
        }
    }
}
