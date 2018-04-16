<?php

namespace App\Extensions\Dailian\Controllers;

use App\Events\OrderFinish;
use DB;
use Asset;
use ErrorException;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Extensions\Asset\Income;
use App\Exceptions\DailianException; 
use App\Repositories\Frontend\OrderDetailRepository;

/**
 * 订单完成操作
 */
class Complete extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus   = [14]; // 状态：14待验收
    protected $beforeHandleStatus = 14; // 操作之前的状态:14待验收
    protected $handledStatus      = 20; // 状态：20已结算
    protected $type               = 12; // 操作：12完成
    
	/**
     * [run 完成 -> 已结算]
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
            // 删除状态不阻碍申请验收redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 从留言获取任务中删除
            levelingMessageDel($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 12);
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    // 流水，代练完成，接单商户完成代练收入
    public function updateAsset()
    {
        DB::beginTransaction();
        try {
        	// 接单 代练收入
            Asset::handle(new Income($this->order->amount, 12, $this->order->no, '代练订单完成收入', $this->order->gainer_primary_user_id));

            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new DailianException('流水记录写入失败');
            }

            if ($this->order->detail()->where('field_name', 'security_deposit')->value('field_value')) {    
                // 接单 退回安全保证金
                Asset::handle(new Income($this->order->detail()->where('field_name', 'security_deposit')->value('field_value'), 8, $this->order->no, '退回安全保证金', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }
            }

            if ($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value')) {
                // 接单 退效率保证金
                Asset::handle(new Income($this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value'), 9, $this->order->no, '退回效率保证金', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }
            }
            // 写入结算时间
            OrderDetailRepository::updateByOrderNo($this->orderNo, 'checkout_time', date('Y-m-d H:i:s'));
        } catch (DailianException $e) {
            DB::rollback();
            throw new DailianException($e->getMessage());
        }
        DB::commit();
    }

    /**
     * 订单验收结算 
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
                        if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                            // 控制器-》方法-》参数
                            call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['complete']], [$orderDatas]);
                        }
                    }
                }

                /**
                 * 以下只适用于  91  和 代练妈妈
                 */

                $orderDetails = $this->checkThirdClientOrder($this->order);

                switch ($orderDetails['third']) {
                    case 1:
                        // 91 完成接口
                        $options = [
                            'oid' => $orderDetails['show91_order_no'], 
                            'p' => config('show91.password'),
                        ];
                        Show91::accept($options);
                        break;
                    case 2:
                        // 代练妈妈完成接口
                        DailianMama::operationOrder($this->order, 20013);
                        break;
                }
                try {
                    event(new OrderFinish($this->order));
                } catch (ErrorException $errorException) {
                    myLog('finish', [$errorException->getMessage()]);
                } catch (\Exception $exception) {
                    myLog('finish', [$exception->getMessage()]);
                }
                return true;
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
