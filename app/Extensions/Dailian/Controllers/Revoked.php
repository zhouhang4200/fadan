<?php

namespace App\Extensions\Dailian\Controllers;

class Revoked extends DailianAbstract implements DailianInterface
{
     //同意撤销-》已撤销
    protected $acceptableStatus = [15]; // 状态：18锁定
	protected $beforeHandleStatus = 15; // 操作之前的状态:
    protected $handledStatus    = 19; // 状态：
    protected $type             = 24; // 操作：24同意撤销
	// 运行, 第一个参数为订单号，第二个参数为操作用户id, 第三个为给接单的代练费（填的协商的钱),第四个参数接口传的给发单商户返的双金, 第五个为接口传的手续费
    public function run($no, $userId, $amount, $apiDeposit, $apiService, $writeDeposit)
    {	
    	DB::beginTransaction();
    	try {
    		// 赋值
    		$this->orderNo = $no;
        	$this->userId  = $userId;
        	// 获取锁定前的状态
        	$this->$beforeHandleStatus = $this->getObject()->status;
    		// 获取订单对象
		    $this->getObject();
		    // 创建操作前的订单日志详情
		    $this->createLogObject();
		    // 设置订单属性
		    $this->setAttributes();
		    // 保存更改状态后的订单
		    $this->save();
		    // 更新平台资产
		    $this->updateAsset($amount, $apiDeposit, $apiService, $writeDeposit);
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
     * @param  [type] $amount [填写代练费]
     * @param  [type] $deposit [填写的双金费]
     * @param  [type] $apiService [回传手续费]
     * @return [bool]             [流水记录]
     */
    public function updateAsset($amount, $apiDeposit, $apiService, $writeDeposit)
    {
        // 订单的安全保证金
        $security = $this->order->orderDetail->pluck('field_name')->security_deposit;
        // 订单的效率保证金
        $efficiency = $this->order->orderDetail->pluck('field_name')->efficiency_deposit;
        // 剩余代练费 = 订单代练费 - 回传代练费
        $leftAmount = bcsub($this->order->amount, $amount);
        // 订单双金 = 订单安全保证金 + 订单效率保证金
        $orderDeposit = bcadd($security, $efficiency);
        // 回传双金 + 回传手续费
        $apiAll = bcadd($apiDeposit, $apiService);
        // 回传双金 + 手续费 == 写入的双金
        $isZero = bcsub($apiAll, $writeDeposit);

        if ($leftAmount >= 0 && bcsub($orderDeposit, $apiAll) >= 0 && $isZero == 0) {       
            DB::beginTransaction();
            try {
               // 接单 协商代练费收入
               Asset::handle(new Income($amount, 12, $this->order->no, '协商代练收入', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }

                // 发单 退回剩余代练费 $leftAmount
                Asset::handle(new Income($leftAmount, 7, $this->order->no, '退回协商代练费', $this->order->creator_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new Exception('申请失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new Exception('申请失败');
                }
                
                // 如果订单安全保证金 > 回传双金 + 手续费 
                if (bcsub($security, $apiAll) > 0) {
                    // 发单 安全保证金收入
                    Asset::handle(new Income($apiAll, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 发单 支出手续费
                    Asset::handle(new Expend($apiService, 2, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 退回 剩余安全保证金
                    $leftSecurity = bcsub($security, $apiAll);

                    Asset::handle(new Income($leftSecurity, 8, $this->order->no, '安全保证金退回', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 退回 全额效率保证金
                    Asset::handle(new Income($efficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 代练手续费收入
                    Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }
                } else if (bcsub($security, $apiAll) == 0) {
                    // 发单 安全保证金收入
                    Asset::handle(new Income($apiAll, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 发单 支出手续费
                    Asset::handle(new Expend($apiService, 2, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 退回全额 效率保证金
                    Asset::handle(new Income($efficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 代练手续费收入
                    Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }
                } else {
                    // 发单 全额
                    Asset::handle(new Income($security, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 发单  剩余效率保证金收入
                    $creatorEfficiency = bcsub($apiAll, $security);

                    Asset::handle(new Income($creatorEfficiency, 11, $this->order->no, '效率保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 发单 代练手续费支出
                    Asset::handle(new Expend($apiService, 2, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 退回剩余效率保证金
                    $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

                    Asset::handle(new Income($leftEfficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    // 接单 手续费收入
                    Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

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
