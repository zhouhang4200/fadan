<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use App\Services\Show91;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException as Exception; 

class Revoking extends DailianAbstract implements DailianInterface
{
     //撤销中
    protected $acceptableStatus = [13, 14, 17, 18]; // 状态：18锁定
    protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 15; // 状态：15撤销中
    protected $type             = 18; // 操作：18撤销

	/**
     * [run 撤销 -> 撤销中]
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
            // 获取锁定前的状态
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

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

     /**
     * 调用外部提交协商发接口
     * @return [type] [description]
     */
    public function after()
    {
        if ($this->runAfter) {
            try {
                $consult = LevelingConsult::where('order_no', $this->order->no)->first();

                if (! $consult) {
                    throw new Exception('不存在申诉和协商记录');
                }

                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $thirdOrderNo = $this->order->detail()->where('field_name', 'third_order_no')->value('field_value');

                    if (! $thirdOrderNo) {
                        throw new Exception('第三方订单号不存在');
                    }
                    $options = [
                        'oid' => $thirdOrderNo,
                        'selfCancel.pay_price' => $consult->amount,
                        'selfCancel.pay_bond' => $consult->deposit,
                        'selfCancel.content' => $consult->revoke_message,
                    ];

                    // 结果
                    $result = Show91::addCancelOrder($options);
                    $result = json_decode($result, true);
                    
                    if (! $result) {
                        throw new CustomException('外部接口错误,请重试!');
                    }
                    
                    if ($result && $result['result']) {
                        throw new Exception($result['reason']);
                    }
                }
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
