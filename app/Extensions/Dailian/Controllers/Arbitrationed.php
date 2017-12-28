<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Exception;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Models\LevelingConsult;

class Arbitrationed extends DailianAbstract implements DailianInterface
{
	// 已仲裁
    protected $acceptableStatus = [16]; // 状态：16仲裁中
	protected $beforeHandleStatus = 16; // 操作之前的状态:16仲裁中
    protected $handledStatus    = 21; // 状态：21已仲裁
    protected $type             = 26; // 操作：26 客服仲裁
    
	/**
	 * [客服仲裁 -》 已仲裁]
	 * @param  [type] $orderNo     [订单号]
	 * @param  [type] $userId      [操作人]
	 * @param  [type] $apiAmount   [回传代练费]
	 * @param  [type] $apiDeposit  [回传双金]
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

    /**
     * 流水
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit [回传双金]
     * @param  [type] $apiService    [回传手续费]
     * @return [type]                       [流水记录]
     */
	public function updateAsset()
	{
		// 从leveling_consult 中取各种值
        $consult = LevelingConsult::where('order_no', $this->orderNo)->where('complete', 1)->first();
        $apiAmount = $consult->api_amount;
        $apiDeposit = $consult->api_deposit;
        $apiService = $consult->api_service;
		// 订单的安全保证金
		$security = $this->order->detail()->where('field_name', 'security_deposit')->value('field_value');
		// 订单的效率保证金 efficiency_deposit
		$efficiency = $this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
		// 剩余代练费 = 订单代练费 - 回传代练费
        $leftAmount = bcsub($this->order->amount, $apiAmount);
        // 订单双金 = 订单安全保证金 + 订单效率保证金
        $deposit = bcadd($security, $efficiency);
        // 回传所有 = 回传双金 + 回传首席非
        $apiAll = bcadd($apiDeposit, $apiService);
        // 判断回传是否有效
        $bool = bcsub($deposit, $apiAll);

        if ($leftAmount >= 0 && $bool >= 0) {    

            DB::beginTransaction();
        	try {
        		if ($leftAmount > 0) {
	                // 发单 代练费退回(剩余回传代练费)
	                Asset::handle(new Income($leftAmount, 7, $this->order->no, '退还代练费', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }
        		}

                if ($apiAmount > 0) {
	                // 接单 代练收入
	                Asset::handle(new Income($apiAmount, 12, $this->order->no, '代练费收入', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }
                }

                // 如果订单安全保证金 > (回传双金 + 手续费)
                if (bcsub($security, $apiAll) > 0) {  
                	if ($apiAll) {
		                // 发单 安全保证金收入
		                Asset::handle(new Income($apiAll, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
                	}

	                if ($apiService > 0) {	                	
		                // 发单 手续费支出
		                Asset::handle(new Expend($apiService, 3, $this->order->no, '手续费支出', $this->order->creator_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }

	                // 接单 剩下的安全保证金
	                $leftSecurity = bcsub($security, $apiAll);

	                if ($leftSecurity > 0) {
		                Asset::handle(new Income($leftSecurity, 8, $this->order->no, '退还安全保证金', $this->order->gainer_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }


	                // 接单 退还效率保证金
	                Asset::handle(new Income($efficiency, 9, $this->order->no, '退还效率保证金', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if ($apiService > 0) {
		                // 接单 代练手续费收入
		                Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }
                } else if (bcsub($security, $apiAll) == 0) {
                	if ($apiAll > 0) {
	                	// 发单 安全保证金收入
		                Asset::handle(new Income($apiAll, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
                	}

	                if ($apiService > 0) {
		                // 发单 手续费支出
		                Asset::handle(new Expend($apiService, 3, $this->order->no, '手续费支出', $this->order->creator_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }

	                // 接单 退还全额效率保证金
	                Asset::handle(new Income($efficiency, 9, $this->order->no, '退还效率保证金', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if ($apiService > 0) {
		                // 接单 代练手续费收入
		                Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }
                } else {
                	// 发单 全额安全保证金收入
                	Asset::handle(new Income($security, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 发单 效率保证金收入
	                $creatorEfficiency = bcsub($apiAll, $security);

	                if ($creatorEfficiency > 0) {
		                Asset::handle(new Income($creatorEfficiency, 11, $this->order->no, '效率保证金收入', $this->order->creator_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }

	                if ($apiService > 0) {
		                // 发单 手续费支出
		                Asset::handle(new Expend($apiService, 3, $this->order->no, '手续费支出', $this->order->creator_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }

	                // 接单 退还剩余效率保证金
	                $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

	                if ($leftEfficiency > 0) {
		                Asset::handle(new Income($leftEfficiency, 9, $this->order->no, '退还效率保证金', $this->order->gainer_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }

	                if ($apiService > 0) {
		                 // 接单 代练手续费收入
		                Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

		                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new Exception('申请失败');
		                }

		                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new Exception('申请失败');
		                }
	                }
                }
	        } catch (Exception $e) {
	            DB::rollBack();
	        }
	        DB::commit();
	    } else {
	    	throw new Exception('参数传入错误或不满足条件');
	    }
	}
}
