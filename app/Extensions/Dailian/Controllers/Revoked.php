<?php

namespace App\Extensions\Dailian\Controllers;

use App\Models\OrderDetail;
use App\Models\UserOrderDetail;
use App\Repositories\Frontend\OrderDetailRepository;
use DB;
use Asset;
use App\Models\User;
use App\Extensions\Asset\Income;
use App\Extensions\Asset\Expend;
use App\Models\LevelingConsult;
use App\Services\Show91;
use App\Exceptions\DailianException as Exception; 

/**
 * 同意撤销操作
 */
class Revoked extends DailianAbstract implements DailianInterface
{
     //同意撤销-》已撤销
    protected $acceptableStatus = [15, 16]; // 状态：15撤销中 16仲裁中
	protected $beforeHandleStatus; // 操作之前的状态:15撤销中
    protected $handledStatus    = 19; // 状态：19 已撤销
    protected $type             = 24; // 操作：24同意撤销

    /**
     * [run 同意撤销 -> 已撤销]
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

            $this->after();

            LevelingConsult::where('order_no', $this->orderNo)->update(['complete' => 1]);

            delRedisCompleteOrders($this->orderNo);
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
     * @throws Exception
     * @internal param $ [type] $amount [协商代练费]
     * @internal param $ [type] $deposit [回传双金费]
     * @internal param $ [type] $apiService [回传手续费]
     * @internal param $ [type] $writeDeposit [协商的双金]
     */
    public function updateAsset()
    {
        // 从leveling_consult 中取各种值
        $consult = LevelingConsult::where('order_no', $this->orderNo)->first();

        if ($consult->consult == 1) {
            $user = User::where('id', $this->order->gainer_primary_user_id)->first();
            $userIds = $user->children->pluck('id')->merge($user->id)->toArray();
        } else if ($consult->consult == 2) {
            $user = User::where('id', $this->order->creator_primary_user_id)->first();
            $userIds = $user->children->pluck('id')->merge($user->id)->toArray();
        } else {
            throw new Exception('未找到该单撤销发起人！');
        }

        if (! in_array($this->userId, $userIds)) {
            throw new Exception('当前操作人不是该订单操作者本人!');
        }

        $amount = $consult->amount;
        $writeDeposit = $consult->deposit;

        if (! $amount || ! $consult || ! $writeDeposit) {
            throw new Exception('不存在申诉和协商记录或不存在协商代练费或双金!');
        }
        // $apiDeposit = $consult->api_deposit;
        $apiService = $consult->api_service;
        // 订单的安全保证金
        $security = $this->order->detail()->where('field_name', 'security_deposit')->value('field_value');
        // 订单的效率保证金
        $efficiency = $this->order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
        // 剩余代练费 = 订单代练费 - 回传代练费
        $leftAmount = bcsub($this->order->amount, $amount);
        // 订单双金 = 订单安全保证金 + 订单效率保证金
        // $orderDeposit = bcadd($security, $efficiency);
        // 回传手续费 《= 协商所填双金
        $isRight = bcsub($writeDeposit, $apiService);
        // 回传双金 + 回传手续费
        // $apiAll = bcadd($apiDeposit, $apiService);
        // 回传双金 + 手续费 == 写入的双金
        // $isZero = bcsub($apiAll, $writeDeposit);

        if ($leftAmount >= 0 && $isRight >= 0) {    
            DB::beginTransaction();
            try {
                if ($amount > 0) {
                    // 接单 协商代练费收入
                    Asset::handle(new Income($amount, 12, $this->order->no, '协商代练收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('流水记录写入失败');
                    }
                }

                if ($leftAmount > 0) {
                    // 发单 退回剩余代练费 $leftAmount
                    Asset::handle(new Income($leftAmount, 7, $this->order->no, '退回协商代练费', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new Exception('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new Exception('流水记录写入失败');
                    }
                }
                
                // 如果订单安全保证金 > 填写双金
                if (bcsub($security, $writeDeposit) > 0) {
                    if ($writeDeposit > 0) {
                        // 发单 安全保证金收入
                        Asset::handle(new Income($writeDeposit, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    // 手续费有可能为0，比如 12，20, 0，20
                    if ($apiService > 0) {
                        // 发单 支出手续费
                        Asset::handle(new Expend($apiService, 3, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    // 接单 退回 剩余安全保证金
                    $leftSecurity = bcsub($security, $writeDeposit);

                    if ($leftSecurity > 0) {
                        Asset::handle(new Income($leftSecurity, 8, $this->order->no, '安全保证金退回', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($efficiency > 0) {
                        // 接单 退回 全额效率保证金
                        Asset::handle(new Income($efficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 代练手续费收入
                        Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }
                } else if (bcsub($security, $writeDeposit) == 0) {
                    if ($writeDeposit > 0) {
                        // 发单 安全保证金收入
                        Asset::handle(new Income($writeDeposit, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 发单 支出手续费
                        Asset::handle(new Expend($apiService, 3, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($efficiency) {
                        // 接单 退回全额 效率保证金
                        Asset::handle(new Income($efficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 代练手续费收入
                        Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }
                } else {
                    if ($security) {
                        // 发单 全额
                        Asset::handle(new Income($security, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    // 发单  剩余效率保证金收入
                    $creatorEfficiency = bcsub($writeDeposit, $security);

                    if ($creatorEfficiency > 0) {
                        Asset::handle(new Income($creatorEfficiency, 11, $this->order->no, '效率保证金收入', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 发单 代练手续费支出
                        Asset::handle(new Expend($apiService, 3, $this->order->no, '代练手续费支出', $this->order->creator_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    // 接单 退回剩余效率保证金
                    $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

                    if ($leftEfficiency > 0) {
                        Asset::handle(new Income($leftEfficiency, 9, $this->order->no, '效率保证金退回', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 手续费收入
                        Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }

                        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new Exception('流水记录写入失败');
                        }
                    }
                }
                // 写入获得金额
                OrderDetailRepository::updateByOrderNo($this->orderNo, 'get_amount', $writeDeposit);
                // 写入手续费
                OrderDetailRepository::updateByOrderNo($this->orderNo, 'poundage', $apiService);
                // 写入结算时间
                OrderDetailRepository::updateByOrderNo($this->orderNo, 'checkout_time', date('Y-m-d H:i:s'));
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }
            DB::commit();
        } else {
            throw new Exception('无回传双金手续费或回传双金手续费超过订单双金!');
        }
    }

    public function after()
    {
        if ($this->runAfter) {
            try {
                if ($this->order->detail()->where('field_name', 'third')->value('field_value') == 1) { //91代练
                    $thirdOrderNo = $this->order->detail()->where('field_name', 'third_order_no')->value('field_value');

                    if (! $thirdOrderNo) {
                        throw new Exception('第三方订单号不存在');
                    }

                    $options = [
                        'oid' => $thirdOrderNo, 
                        'v' => 1,
                        'p' => config('show91.password'),
                    ];
                    // 结果
                    Show91::confirmSc($options);
                }
                return true;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
}
