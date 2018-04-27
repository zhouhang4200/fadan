<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Exception;
use App\Services\Show91;
use App\Models\LevelingConsult;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Exceptions\DailianException;

/**
 * 取消撤销操作
 */
class UnRevoke extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [15]; // 状态：15撤销中
	protected $beforeHandleStatus = 15; // 操作之前的状态:15撤销中
    protected $handledStatus; // 状态：操作之后的状态
    protected $type             = 19; // 操作：19取消撤销

	/**
     * [run 取消撤销 -> 撤销前的状态]
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
        	// 获取上一个操作状态
            $this->getBeforeStatus($orderNo);
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
            $this->changeConsultStatus();
            $this->after();
            $this->orderCount();
            // 删除redis 申请验收订单
            delRedisCompleteOrders($this->orderNo);
            // 如果还原前一个状态为 申请验收 ，redis 加订单
            addRedisCompleteOrders($this->orderNo, $this->handledStatus);
            
            if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 2) {
                (new Lock)->run($orderNo, $userId, $runAfter = false);
            }
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}  catch (RequestTimeoutException $exception) {
            //  写入redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            myLog('opt-ex',  ['操作' => '取消撤销', $exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            throw new DailianException('订单异常');
        }
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * 获取前一个状态
     * @param $orderNo
     * @throws DailianException
     */
    public function getBeforeStatus($orderNo)
    {
        $orderDetail = OrderDetail::where('order_no', $orderNo)
            ->where('field_name', 'order_previous_status')
            ->first();

        if (! $orderDetail) {
            throw new DailianException('订单前一个状态不存在');
        }

        $previousArr = explode('|', $orderDetail->field_value);

        if (! is_array($previousArr)) {
            throw new DailianException('订单前一个状态数据异常');
        }

        if (in_array(18, $previousArr)) {
            $this->handledStatus = 18;
        } else {
            $this->handledStatus = $previousArr[0];
        }
    }

    public function changeConsultStatus()
    {
        LevelingConsult::where('order_no', $this->orderNo)->update(['consult' => 0]);
    }

    /**
     * 调用外部提交协商发接口
     * @return bool|void [type] [description]
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
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['cancelRevoke']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下只适用于 91  和 代练妈妈
             */
            $orderDetails = $this->checkThirdClientOrder($this->order);

            switch ($orderDetails['third']) {
                case 1:
                    // 91 取消撤销
                    Show91::cancelSc(['oid' => $orderDetails['show91_order_no']]);
                    break;
                case 2:
                    // 代练妈妈取消协商接口
                    DailianMama::operationOrder($this->order, 20012);
                    break;
            }
            return true;
        }
    }
}
