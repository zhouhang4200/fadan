<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Exceptions\DailianException; 

/**
 * 锁定操作
 */
class Lock extends DailianAbstract implements DailianInterface
{
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
            // 申请验收过期自动删除
            delRedisCompleteOrders($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();

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
                $orderDetails = $this->checkThirdClientOrder($this->order);

                switch ($orderDetails['third']) {
                    case 1:
                        // 91锁定接口
                        $options = ['oid' => $orderDetails['show91_order_no']];
                        Show91::changeOrderBlock($options);
                        break;
                    case 2:
                        // 代练妈妈锁定接口
                        DailianMama::operationOrder($this->order, 20002);
                        break;
                    default:
                        throw new DailianException('第三方接单平台不存在!');
                        break;
                }
                
                
                // $orderDetails = OrderDetail::where('order_no', $this->order->no)
                //     ->pluck('field_value', 'field_name')
                //     ->toArray();

                // if ($orderDetails['third'] == 1) { //91代练
                //     if (! $orderDetails['third_order_no']) {
                //         throw new DailianException('第三方订单号不存在');
                //     }

                //     $options = [
                //         'oid' => $orderDetails['third_order_no'],
                //     ];
                //     // 结果
                //     Show91::changeOrderBlock($options);
                // }
                return true;
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
