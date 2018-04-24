<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Services\Show91;
use App\Models\LevelingConsult;
use App\Services\DailianMama;
use App\Events\OrderRevoking;
use App\Exceptions\DailianException;

/**
 * 申请撤销操作
 */
class Revoking extends DailianAbstract implements DailianInterface
{
    protected $acceptableStatus = [13, 14, 17, 18]; // 状态：18锁定
    protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 15; // 状态：15撤销中
    protected $type             = 18; // 操作：18撤销

    /**
     * [run 撤销 -> 撤销中]
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
            // 获取订单对象
            $this->getObject();
            // 获取撤销前的状态
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
            // 删除状态不是 申请验收 的 redis 订单
            delRedisCompleteOrders($this->orderNo);
            // 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
        } catch (DailianException $e) {
            // 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 18);
            DB::rollBack();
            throw new DailianException($e->getMessage());
    	}  catch (Exception $exception) {
            //  写入redis报警
            $this->addOperateFailOrderToRedis($this->order, $this->type);
            DB::rollBack();
            throw new DailianException($exception->getMessage());
        }
    	DB::commit();
        return true;
    }

    /**
     * 调用外部提交协商发接口
     * @throws DailianException
     */
    public function after()
    {
        // 调用事件
        try {
            event(new OrderRevoking($this->order));
        } catch (Exception $errorException) {
            myLog('ex', ['申请撤销 事件',  $errorException->getMessage()]);
        }

        if ($this->runAfter) {

                $orderDetails = $this->checkThirdClientOrder($this->order);

                $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                if (! $consult) {
                    throw new DailianException('不存在申诉和协商记录');
                }

                switch ($orderDetails['third']) {
                    case 1:
                        // 91 申请协商接口
                        $options = [
                            'oid' => $orderDetails['show91_order_no'],
                            'selfCancel.pay_price' => $consult->amount,
                            'selfCancel.pay_bond' => $consult->deposit,
                            'selfCancel.content' => $consult->revoke_message,
                        ];

                        Show91::addCancelOrder($options);
                        break;
                    case 2:
                        // 代练妈妈协商接口
                        DailianMama::operationOrder($this->order, 20006);
                        break;
                }

                if (config('leveling.third_orders')) {
                    // 获取订单和订单详情以及仲裁协商信息
                    $orderDatas = $this->getOrderAndOrderDetailAndLevelingConsult($this->orderNo);
                    // 如果没有撤销信息，抛出错误
                    if (! $orderDatas['consult_order_no']) {
                        throw new DailianException('撤销记录不存在');
                    }
                    // 遍历代练平台
                    foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                        // 如果订单详情里面存在某个代练平台的订单号
                        if ($third == $orderDatas['third'] && isset($orderDatas['third_order_no']) && ! empty($orderDatas['third_order_no'])) {
                            // 控制器-》方法-》参数
                            call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['applyRevoke']], [$orderDatas]);
                        }
                    }
                }
        }
    }
}
