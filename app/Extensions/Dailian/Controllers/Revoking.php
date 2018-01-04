<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Services\Show91;
use App\Models\LevelingConsult;

class Revoking extends DailianAbstract implements DailianInterface
{
     //撤销中
    protected $acceptableStatus = [13, 14, 17, 18]; // 状态：18锁定
    protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 15; // 状态：15撤销中
    protected $type             = 18; // 操作：18撤销
    protected $runAfter         = 1;

	/**
     * [run 撤销 -> 撤销中]
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
        	// 获取锁定前的状态
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
     * 调用外部提交协商发接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                    $options = [
                        'oid' => $this->order->detail()->where('field_name', 'third_order_no')->value('field_value'),
                        'selfCancel.pay_price' => $consult->amount,
                        'selfCancel.pay_bond' => $consult->deposit,
                        'selfCancel.content' => $consult->revoke_message,
                    ];
                    // dd($options);
                    // 结果
                    $result = Show91::addCancelOrder($options);
                    $result = json_decode($result);
                    dd($result);
                    if ($result->reason) {
                        $reason = $result->reason ?? '下单失败!';
                        throw new Exception($reason);
                    }
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
