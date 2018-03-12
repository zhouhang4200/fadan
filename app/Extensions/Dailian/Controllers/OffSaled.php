<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Exceptions\DailianException; 
/**
 * 下架操作
 */
class OffSaled extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [1]; // 状态：1未接单
    protected $beforeHandleStatus; // 操作之前的状态:1未接单
    protected $handledStatus    = 22; // 状态：22已下架
    protected $type             = 15; // 操作：15下架

	/**
     * [run 下架 -> 已下架]
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
            $this->orderCount();
            // 状态不在申请验收自动删除
            delRedisCompleteOrders($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();

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
                $orderDetails = $this->checkThirdClientOrder($this->order);

                switch ($orderDetails['third']) {
                    case 1:
                        // 91下架接口
                        $options = ['oid' => $orderDetails['show91_order_no']]; 
                        Show91::grounding($options);
                        break;
                    case 2:
                        // 代练妈妈下架接口
                        DailianMama::closeOrder($this->order);
                        break;
                    default:
                        // 没接单的情况下，下架两边的订单
                        if ($orderDetails['show91_order_no']) {
                            // 91下架接口
                            $options = ['oid' => $orderDetails['show91_order_no']]; 
                            Show91::grounding($options);
                        }
                        if ($orderDetails['dailianmama_order_no']) {
                            // 代练妈妈下架接口
                            DailianMama::closeOrder($this->order);
                        }
                        // throw new DailianException('第三方接单平台不存在!');
                        break;
                }
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
