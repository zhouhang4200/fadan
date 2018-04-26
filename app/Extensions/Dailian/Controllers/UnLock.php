<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\RequestTimeoutException;
use DB;
use Exception;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Exceptions\DailianException;

/**
 * 取消锁定操作
 */
class UnLock extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [18]; // 状态：18锁定
	protected $beforeHandleStatus = 18; // 操作之前的状态:18锁定
    protected $handledStatus;   // 操作后状态：
    protected $type             = 17; // 操作：17取消锁定

    /**
     * [run 取消锁定 -> 锁定前状态]
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
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
            $this->runAfter = $runAfter;
            // 获取锁定前的状态
            $this->getBeforeStatus($orderNo);
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
            $this->orderCount();
            $this->after();
            // 如果申请验收改为别的状态，删除redis里面的订单
            delRedisCompleteOrders($this->orderNo);
            // 如果还原前一个状态为 申请验收 ，redis 加订单
            addRedisCompleteOrders($this->orderNo, $this->handledStatus);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 17);
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}  catch (RequestTimeoutException $exception) {
            //  写入redis报警
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
     * 调用外部锁定发接口
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
                    // 如果订单详情里面存在某个代练平台的订单号
                    if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['cancelLock']], [$orderDatas]);
                    }
                }
            }

            /**
             * 以下 只 适用于 91  和 代练妈妈
             * @var [type]
             */
            $orderDetails = $this->checkThirdClientOrder($this->order);

            switch ($orderDetails['third']) {
                case 1:
                    // 91 取消锁定
                    Show91::changeOrderBlock(['oid' => $orderDetails['show91_order_no']]);
                    break;
                case 2:
                    // 代练妈妈解除锁定接口
                    DailianMama::operationOrder($this->order, 20010);
                    break;
            }
            return true;

        }
    }

    /**
     * 获取订单前一个状态
     * @param $orderNo
     * @throws DailianException
     * @internal param $ [type] $orderNo [description]
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

        if (in_array(13, $previousArr)) {
             $this->handledStatus = 13;
        } else {
            $this->handledStatus = $previousArr[0];
        }

    }
}
