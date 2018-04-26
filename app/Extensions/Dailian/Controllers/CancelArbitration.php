<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Exception;
use App\Services\Show91;
use App\Models\LevelingConsult;
use App\Services\DailianMama;
use App\Exceptions\DailianException;
use App\Models\OrderDetail;

/**
 * 取消仲裁操作
 */
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
    		// 获取订单对象
		    $this->getObject();
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
            // 删除状态不是 申请验收 的 redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 如果还原前一个状态为 申请验收 ，redis 加订单
            addRedisCompleteOrders($this->orderNo, $this->handledStatus);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (RequestTimeoutException $exception) {
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

        if (in_array(15, $previousArr)) {
            $this->handledStatus = 15;
        } else {
            $this->handledStatus = $previousArr[0];
        }
    }

    public function changeConsultStatus()
    {
        LevelingConsult::where('order_no', $this->orderNo)->update(['complain' => 0]);
    }

    /**
     * 撤销申诉
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {

            $orderDetails = $this->checkThirdClientOrder($this->order);
            $consult = LevelingConsult::where('order_no', $this->order->no)->first();

            if (! $consult) {
                throw new DailianException('订单申诉或协商记录不存在!');
            }

            switch ($orderDetails['third']) {
                case 1:
                    // 91 取消申诉接口
                    Show91::cancelAppeal(['aid' => $orderDetails['show91_order_no']]);
                    break;
                case 2:
                    // 代练妈妈取消申诉接口
                    DailianMama::operationOrder($this->order, 20008);
                    break;
            }

            if (config('leveling.third_orders')) {
                // 获取订单和订单详情以及仲裁协商信息
                $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                // 如果没有撤销信息，抛出错误
                if (! $orderDatas['consult_order_no']) {
                    throw new DailianException('申诉记录不存在');
                }
               // 遍历代练平台
                foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                    // 如果订单详情里面存在某个代练平台的订单号
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['cancelArbitration']], [$orderDatas]);
                    }
                }
            }
        }
    }
}
