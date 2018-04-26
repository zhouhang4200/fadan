<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Asset;
use Exception;
use App\Services\Show91;
use App\Services\DailianMama;
use App\Extensions\Asset\Income;
use App\Exceptions\AssetException;
use App\Exceptions\DailianException;

/**
 * 撤单（删除）操作
 */
class Delete extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [1, 22]; // 状态：1未接单， 22已下架
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 24; // 状态：24已删除(已撤单)
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
            $this->orderCount();
            delRedisCompleteOrders($this->orderNo);
            // 从留言获取任务中删除
            levelingMessageDel($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (AssetException $exception) {
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        }  catch (RequestTimeoutException $exception) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            throw new DailianException('订单异常');
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
        // 发单 退回代练费
        Asset::handle(new Income($this->order->amount, 7, $this->order->no, '退回代练费', $this->order->creator_primary_user_id));

        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new DailianException('流水记录写入失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new DailianException('流水记录写入失败');
        }
    }

    /**
     * 调用外部撤单接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {

            if (config('leveling.third_orders') && $this->userId != 8456) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                    // if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                    if (isset($orderDatas[$thirdOrderNoName]) && ! empty($orderDatas[$thirdOrderNoName])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下只适用于  91  和 代练妈妈
             */
            // 获取订单和订单详情
            $orderDetails = $this->checkThirdClientOrder($this->order);

            if ($orderDetails['show91_order_no']) {
                // 91下架接口
                Show91::chedan(['oid' => $orderDetails['show91_order_no']]);
            }

            if ($orderDetails['dailianmama_order_no']) {
                // 代练妈妈下架接口
                DailianMama::deleteOrder($this->order);
            }
            return true;
        }
    }
}
