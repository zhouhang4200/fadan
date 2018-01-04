<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Models\OrderHistory;
use App\Services\Show91;
use App\Models\LevelingConsult;

class CancelArbitration extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [16]; // 状态：16仲裁中
    protected $beforeHandleStatus = 16; // 操作之前的状态:16仲裁中
    protected $handledStatus;// 操作后的状态
    protected $type               = 21; // 操作：21取消仲裁
    
	/**
     * [取消仲裁 -》 仲裁申请前状态]
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
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * 撤销申诉
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                    $options = [
                        'aid' => $this->order->detail()->where('field_name', 'third_order_no')->value('field_value'), // 撤销id 可以用单号
                    ]; // 第三方订单号
                    // 结果
                    $result = Show91::cancelAppeal($options);
                    $result = json_decode($result);

                    if ($result && $result->reason) {
                        $reason = $result->reason;
                        throw new Exception($reason);
                    }
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
