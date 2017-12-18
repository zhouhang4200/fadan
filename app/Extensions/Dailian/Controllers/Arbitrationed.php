<?php

namespace App\Extensions\Dailian\Controllers;

class Arbitrationed extends DailianConstract implements DailianInterface
{
	// 已仲裁
    protected $acceptableStatus = [16]; // 状态：16仲裁中
	protected $beforeHandleStatus = 16; // 操作之前的状态:16仲裁中
    protected $handledStatus    = 21; // 状态：21已仲裁
    protected $type             = 26; // 操作：26客服仲裁
	// 运行, 第一个参数为订单号，第二个参数为操作用户id
    public function run($no, $userId, $apiAmount, $apiDeposit, $apiService)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $no;
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
		    $this->updateAsset($apiAmount, $apiDeposit, $apiService);
		    // 订单日志描述
		    $this->logDescription();
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
	public function updateAsset($apiAmount, $apiDeposit, $apiService)
	{
		// 订单的安全保证金
		$security = $this->order->orderDetail->pluck('field_name')->security_deposit;
		// 订单的效率保证金
		$efficiency = $this->order->orderDetail->pluck('field_name')->efficiency_deposit;
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
                // 发单 代练费退回(剩余回传代练费)
                Asset::handle(new Income($leftAmount, 7, $this->order->no, '客服仲裁退还代练费', $this->order->creator_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }
                // 接单 代练收入
                Asset::handle(new Income($apiAmount, 12, $this->order->no, '客服仲裁的代练收入', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }

                // 如果订单安全保证金 > (回传双金 + 手续费)
                if (bcsub($security, $apiAll) > 0) {    	
	                // 发单 安全保证金收入
	                Asset::handle(new Income($apiAll, 10, $this->order->no, '客服仲裁安全保证金收入', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 发单 手续费支出
	                Asset::handle(new Expend($apiService, 2, $this->order->no, '客服仲裁手续费支出', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 接单 剩下的安全保证金
	                $leftSecurity = bcsub($security, $apiAll);

	                Asset::handle(new Income($leftSecurity, 8, $this->order->no, '客服仲裁退还安全保证金', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 接单 退还效率保证金
	                Asset::handle(new Income($efficiency, 9, $this->order->no, '客服仲裁退还效率保证金', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 接单 代练手续费收入
	                Asset::handle(new Income($apiService, 6, $this->order->no, '客服仲裁代练手续费收入', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }
                } else if (bcsub($security, $apiAll) == 0) {
                	// 发单 安全保证金收入
	                Asset::handle(new Income($apiAll, 10, $this->order->no, '客服仲裁安全保证金收入', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 发单 手续费支出
	                Asset::handle(new Expend($apiService, 2, $this->order->no, '客服仲裁手续费支出', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 接单 退还效率保证金
	                Asset::handle(new Income($efficiency, 9, $this->order->no, '客服仲裁退还效率保证金', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 接单 代练手续费收入
	                Asset::handle(new Income($apiService, 6, $this->order->no, '客服仲裁代练手续费收入', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }
                } else {
                	// 发单 全额安全保证金收入
                	Asset::handle(new Income($security, 10, $this->order->no, '客服仲裁安全保证金收入', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 发单 效率保证金收入
	                $creatorEfficiency = bcsub($apiAll, $security);

	                Asset::handle(new Income($creatorEfficiency, 11, $this->order->no, '客服仲裁效率保证金收入', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 发单 手续费支出
	                Asset::handle(new Expend($apiService, 2, $this->order->no, '客服仲裁手续费支出', $this->order->creator_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                // 接单 退还剩余效率保证金
	                $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

	                Asset::handle(new Income($leftEfficiency, 9, $this->order->no, '客服仲裁退还效率保证金', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }
	                 // 接单 代练手续费收入
	                Asset::handle(new Income($apiService, 6, $this->order->no, '客服仲裁代练手续费收入', $this->order->gainer_primary_user_id));

	                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new Exception('申请失败');
	                }

	                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new Exception('申请失败');
	                }
                }
	        } catch (Exception $e) {
	            DB::rollBack();
	        }
	        DB::commit();
	    }
	}
}
