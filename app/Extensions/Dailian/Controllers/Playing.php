<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Exception;
use App\Models\User;
use App\Models\UserAsset;
use App\Extensions\Asset\Expend;

class Playing extends DailianAbstract implements DailianInterface
{
     //强制撤销 -》 撤销
    protected $acceptableStatus = [1]; // 状态：未接单
	protected $beforeHandleStatus; // 操作之前的状态:
    protected $handledStatus    = 13; // 状态：代练中
    protected $type             = 27; // 操作：接单

	/**
     * 
     * @param  [type] $orderNo     [订单号]
     * @param  [type] $userId      [操作人]
     * @param  [type] $apiAmount   [回传代练费/安全保证金]
     * @param  [type] $apiDeposit  [回传双金/ 效率保证金]
     * @param  [type] $apiService  [回传代练手续费]
     * @param  [type] $writeAmount [协商代练费]
     * @return [type]              [true or exception]
     */
    public function run($orderNo, $userId)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $orderNo;
        	$this->userId  = $userId;
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

    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	// 返回
        return true;
    }

     // 保存更改状态后的订单
    public function save()
    {
        $this->order->status = $this->handledStatus;
        $this->order->gainer_user_id = $this->userId;
        $this->order->gainer_primary_user_id = User::find($this->userId)->getPrimaryUserId();

        if (!$this->order->save()) {
            throw new Exception('订单操作失败');
        }

        return $this->order;
    }

    /**
     * [接单支出安全和效率保证金]
     * @return [type] [description]
     */
    public function updateAsset()
    {
        DB::beginTransaction();
        try {
            $safePayment = $this->order->detail()->where('field_name', 'security_deposit')->value('field_value');
            $effectPayment = $this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
            // 检测接单账号余额
            $this->checkGainerMoney($safePayment, $effectPayment);
            if ($safePayment > 0) {                      
                // 接单 安全保证金支出
                Asset::handle(new Expend($safePayment, 4, $this->order->no, '安全保证金支出', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }
            }

            if ($effectPayment > 0) {                      
                // 接单 效率保证金支出
                Asset::handle(new Expend($effectPayment, 5, $this->order->no, '效率保证金支出', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }
            }
        } catch (Exception $e) {
            DB::rollback();
        }
        DB::commit();
    }

    /**
     * 检车接单账户余额
     * @return [type] [description]
     */
    public function checkGainerMoney($safePayment, $effectPayment)
    {
        // 接单商户余额
        $leftAmount = UserAsset::where('user_id', $this->order->gainer_primary_user_id)->value('balance');

        $doublePayment = bcadd($safePayment, $effectPayment);

        if ($leftAmount <= 0 || $leftAmount < $doublePayment) {
            throw new Exception('余额不足');
        }
    }
}
