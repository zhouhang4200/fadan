<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Exception;
use App\Extensions\Asset\Income;
use App\Extensions\Asset\Expend;
use App\Models\LevelingConsult;
use App\Services\Show91;

class Revoked extends DailianAbstract implements DailianInterface
{
     //同意撤销-》已撤销
    protected $acceptableStatus = [15, 16]; // 状态：15撤销中 16仲裁中
	protected $beforeHandleStatus; // 操作之前的状态:15撤销中
    protected $handledStatus    = 19; // 状态：19 已撤销
    protected $type             = 24; // 操作：24同意撤销

	/**
     * [run 同意撤销 -> 已撤销]
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
        	// 获取锁定前的状态
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

    /**
     * 流水
     * @param  [type] $amount [协商代练费]
     * @param  [type] $deposit [回传双金费]
     * @param  [type] $apiService [回传手续费]
     * @param  [type] $writeDeposit [协商的双金]
     * @return [bool]             [流水记录]
     */
    public function updateAsset()
    {
        // 从leveling_consult 中取各种值
        $consult = LevelingConsult::where('order_no', $this->orderNo)->first();
        $amount = $consult->amount;

        if (!$amount || !$consult) {
            throw new Exception('状态错误!');
        }
        $apiDeposit = $consult->api_deposit;
        $apiService = $consult->api_service;
        $writeDeposit = $consult->deposit;
        // 订单的安全保证金
        $security = $this->order->detail()->where('field_name', 'security_deposit')->value('field_value');
        // 订单的效率保证金
        $efficiency = $this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
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
                if ($amount > 0) {
                    // 接单 协商代练费收入
                    Asset::handle(new Income($amount, 12, $this->order->no, '协商代练收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }
                }

                if ($leftAmount > 0) {
                    // 发单 退回剩余代练费 $leftAmount
                    Asset::handle(new Income($leftAmount, 7, $this->order->no, '退回协商代练费', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('申请失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('申请失败');
                    }
                }
                
                // 如果订单安全保证金 > 回传双金 + 手续费 
                if (bcsub($security, $apiAll) > 0) {
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

                    // 手续费有可能为0，比如 12，20, 0，20
                    if ($apiService > 0) {
                        // 发单 支出手续费
                        Asset::handle(new Expend($apiService, 3, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('申请失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('申请失败');
                        }
                    }

                    // 接单 退回 剩余安全保证金
                    $leftSecurity = bcsub($security, $apiAll);

                    if ($leftSecurity > 0) {
                        Asset::handle(new Income($leftSecurity, 8, $this->order->no, '安全保证金退回', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('申请失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('申请失败');
                        }
                    }

                    if ($efficiency > 0) {
                        // 接单 退回 全额效率保证金
                        Asset::handle(new Income($efficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

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
                        // 发单 支出手续费
                        Asset::handle(new Expend($apiService, 3, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('申请失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('申请失败');
                        }
                    }

                    // 接单 退回全额 效率保证金
                    Asset::handle(new Income($efficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

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
                        // 发单 代练手续费支出
                        Asset::handle(new Expend($apiService, 3, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('申请失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('申请失败');
                        }
                    }

                    // 接单 退回剩余效率保证金
                    $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

                    if ($leftEfficiency > 0) {
                        Asset::handle(new Income($leftEfficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('申请失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('申请失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 手续费收入
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
            return response()->json(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    public function after()
    {
        if ($this->runAfter) {
            LevelingConsult::where('order_no', $this->orderNo)->update(['complete' => 1]);
        }
    }
}
