<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Exception;
use App\Models\OrderHistory;
use App\Services\Show91;

class UnRevoke extends DailianAbstract implements DailianInterface
{
    //取消撤销
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
            (new Lock)->run($orderNo, $userId);
    	} catch (Exception $e) {
    		DB::rollBack();
    		echo json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
            exit;
            // throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

    public function getBeforeStatus($orderNo)
    {
        $beforeStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('id')->value('before'))['status'];
        // 获取上一条操作记录，如果上一条为仲裁中，则取除了仲裁中和撤销中的最早的一条状态
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
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $options = [
                        'oid' => $this->order->detail()->where('field_name', 'third_order_no')->value('field_value'),
                    ]; 
                    // 结果
                    $result = Show91::cancelSc($options);
                    $result = json_decode($result);

                    if ($result && $result->reason) {
                        $reason = $result->reason;
                        throw new Exception($reason);
                    }
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
                return response()->json(['status' => 0, 'message' => $e->getMessage()]);
            }
        }
    }
}
