<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\CustomException;
use DB;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Exceptions\DailianException; 

/**
 * 上架操作
 */
class NoReceive extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [22]; // 已下架
    protected $beforeHandleStatus = 22; // 已下架
    protected $handledStatus      = 1; // 未接单
    protected $type               = 14; // 上架

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
            $this->orderCount();
            // 申请验收状态不存在自动删除
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
                if (config('leveling.third_orders')) {
                    // 获取订单和订单详情以及仲裁协商信息
                    $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                    // 遍历代练平台
                    foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                        // 如果订单详情里面存在某个代练平台的订单号
                        if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                            // 控制器-》方法-》参数
                            call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['onSale']], [$orderDatas]);
                        }
                    }
                }


                /**
                 * 以下只适用于  91  和 代练妈妈
                 */
                $orderDetails = $this->checkThirdClientOrder($this->order);

                // 上架91订单
                if ($orderDetails['show91_order_no']) {
                    Show91::grounding(['oid' => $orderDetails['show91_order_no']]);
                }
                // 代练妈妈上架
                if ($orderDetails['dailianmama_order_no']) {
                    DailianMama::upOrder($this->order);
                }
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            } catch (CustomException $exception) {
                // 将订单标记为异常
            }
        }
    }
}
