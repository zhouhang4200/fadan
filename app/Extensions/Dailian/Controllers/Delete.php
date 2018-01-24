<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use App\Services\Show91;
use App\Extensions\Asset\Income;
use App\Exceptions\DailianException; 

/**
 * 删除操作
 */
class Delete extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [1, 22]; // 状态：1未接单， 22已下架
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 24; // 状态：24已删除
    protected $type             = 23; // 操作：23删除
	/**
     * [run 删除 -》 删除]
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
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();
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
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }
        } catch (DailianException $e) {
            DB::rollback();
            throw new DailianException($e->getMessage());
        }
        DB::commit();
    }

    /**
     * 调用外部提交协商发接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                $orderDetails = OrderDetail::where('order_no', $this->order->no)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

                if ($orderDetails['third'] == 1) { //91代练
                    if (! $orderDetails['third_order_no']) {
                        throw new DailianException('第三方订单号不存在');
                    }
                    
                    $options = [
                        'oid' => $orderDetails['third_order_no'],
                    ]; 
                    // 结果
                    Show91::chedan($options);
                }
                return true;
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
