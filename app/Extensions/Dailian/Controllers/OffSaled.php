<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Exception;
use App\Services\Show91;
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
     * @internal param $ [type] $orderNo     [订单号]
     * @internal param $ [type] $userId      [操作人]
     * @internal param $ [type] $apiAmount   [回传代练费]
     * @internal param $ [type] $apiDeposit  [回传双金]
     * @internal param $ [type] $apiService  [回传代练手续费]
     * @internal param $ [type] $writeAmount [协商代练费]
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
            // 下架后 从自动下架任务中删除
            autoUnShelveDel($this->orderNo);
            // 从留言获取任务中删除
            levelingMessageDel($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (RequestTimeoutException $exception) {
            //  写入redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '下架', $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
    	DB::commit();

        return true;
    }

    /**
     * 调用外部下架接口
     * @throws DailianException
     */
    public function after()
    {
        if ($this->runAfter) {

            if (config('leveling.third_orders') && $this->userId != 8456) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号
                    if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['offSale']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下只适用于  91  和 代练妈妈
             */
            $orderDetails = $this->checkThirdClientOrder($this->order);

            // 下架91订单
            if ($orderDetails['show91_order_no']) {
                Show91::grounding(['oid' => $orderDetails['show91_order_no']]);
            }

             // 代练妈妈下架接口
            if ($orderDetails['dailianmama_order_no']) {
                DailianMama::closeOrder($this->order);
            }
        }
    }
}
