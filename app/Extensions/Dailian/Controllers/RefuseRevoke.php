<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\Show91;
use App\Models\OrderHistory;
use App\Models\OrderDetail;
use App\Exceptions\DailianException; 

/**
 * 取消撤销操作
 */
class RefuseRevoke extends DailianAbstract implements DailianInterface
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
            $this->after();
            delRedisCompleteOrders($this->orderNo);
            
            if ($this->order->detail()->where('field_name', 'third')->value('field_value') != 1) {
                (new Lock)->run($orderNo, $userId);
            }
    	} catch (DailianException $e) {
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	}
    	DB::commit();

        return true;
    }

    public function getBeforeStatus($orderNo)
    {
        $beforeStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('id')->value('before'))['status'];
        // 获取上一条操作记录，如果上一条为仲裁中，则取除了仲裁中和撤销中的最早的一条状态
        if (! $beforeStatus) {
            throw new DailianException('订单操作记录不存在');
        }
        if ($beforeStatus == 16 || $beforeStatus == 18) {
            $orderHistories = OrderHistory::where('order_no', $orderNo)->latest('id')->get();
            
            $arr = [];
            foreach ($orderHistories as $key => $orderHistory) {
                $status = unserialize($orderHistory->before);

                if (isset($status['status']) && !in_array($status['status'], [15, 16, 18])) {
                    $arr[$key] = $status['status'];
                }
            }
            $this->handledStatus = current($arr);
        } else {
            $this->handledStatus = $beforeStatus;
        }
    }

     /**
     * 调用外部提交协商发接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                $orderDetails = $this->checkShow91AndDailianMamaOrder($this->order);

                switch ($orderDetails['third']) {
                    case 1:
                        // 91 同意撤销 接口
                        $options = [
                            'oid' => $orderDetails['show91_order_no'],
                            'v' => 2,
                            'p' => config('show91.password'),
                        ]; 

                        Show91::confirmSc($options);
                        break;
                    case 2:
                        throw new DailianException('该订单被代练妈妈平台接单，该平台没有【不同意撤销】操作!');
                        break;
                    default:
                        throw new DailianException('不存在第三方接单平台!');
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
                //         'v' => 2,
                //         'p' => config('show91.password'),
                //     ]; 

                //     Show91::confirmSc($options);
                // }
                return true;
            } catch (DailianException $e) {
                throw new DailianException($e->getMessage());
            }
        }
    }
}
