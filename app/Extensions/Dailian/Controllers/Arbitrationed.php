<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use Redis;
use Exception;
use App\Models\OrderDetail;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;
use App\Models\LevelingConsult;
use App\Exceptions\AssetException;
use App\Exceptions\DailianException;
use App\Repositories\Frontend\OrderDetailRepository;

/**
 * 仲裁操作
 */
class Arbitrationed extends DailianAbstract implements DailianInterface
{
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
		    // 后续操作
		    $this->after();
		    //仲裁完成之后，订单状态改为完成
		    $this->after();

            $this->orderCount();
		    LevelingConsult::where('order_no', $this->orderNo)->update(['complete' => 2]);
		    // 24H自动完成
			delRedisCompleteOrders($this->orderNo);
			// 操作成功，删除redis里面以前存在的订单报警
            $this->deleteOperateSuccessOrderFromRedis($this->orderNo);
    	} catch (DailianException $e) {
    		// 我们平台操作失败，写入redis报警
            $this->addOperateFailOrderToRedis($this->order, 26);
    		DB::rollBack();
            throw new DailianException($e->getMessage());
    	} catch (AssetException $exception) {
            // 未知异常
            throw new DailianException($exception->getMessage());
        } catch (Exception $exception) {
            // 未知异常
            throw new DailianException($exception->getMessage());
        }
    	DB::commit();
    	// 返回
        return true;
    }

    /**
     * 流水
     * @param  [type] $apiAmount   [回传代练费]
     * @param  [type] $apiDeposit [回传双金]
     * @param  [type] $apiService [回传手续费]
     * @return [type]             [流水记录]
     */
	public function updateAsset()
	{
		// 从leveling_consult 中取各种值
        $consult = LevelingConsult::where('order_no', $this->orderNo)->first();

        // 订单详情，接单后third_order_no会有对应的相应接单平台的订单号
        $orderDetails = OrderDetail::where('order_no', $this->orderNo)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

        if (! $consult) {
        	throw new DailianException('状态错误');
        }

        // 第三方平台做此操作的时候，确认该平台身份，该平台在我们平台注册的账号与传递过来的账号是否一致
        switch ($orderDetails['third']) {
        	case 1: // 91
		        if ($this->userId != config('show91.qs_user_id')) {
		            throw new DailianException('当前操作人不是该订单操作者本人!');
		        }
        		break;
        	case 2: // 代练妈妈
        		if ($this->userId != config('dailianmama.qs_user_id')) {
		            throw new DailianException('当前操作人不是该订单操作者本人!');
		        }
        		break;
        	default:
        		foreach (config('leveling.third') as $userId => $third) {
        			if ($orderDetails['third'] == $third) {
        				if ($this->userId != $userId) {
        					throw new DailianException('当前操作人不是该订单操作者本人!');
        				}
        			}
        		}
        		break;
        }

        // 接口传递的代练费
        $apiAmount = $consult->api_amount;
        // 接口传的双金
        $apiDeposit = $consult->api_deposit;
        // 接口传额手续费
        $apiService = $consult->api_service;
		// 订单的安全保证金
		$security = $orderDetails['security_deposit'];
		// 订单的效率保证金 
		$efficiency = $orderDetails['efficiency_deposit'];
		// 剩余代练费 = 订单代练费 - 回传代练费
        $leftAmount = bcsub($this->order->amount, $apiAmount);
        // 订单双金 = 订单安全保证金 + 订单效率保证金
        $deposit = bcadd($security, $efficiency);
        // 回传双金必须小于代练双金
        $leftDeposit = bcsub($deposit, $apiDeposit);
        // 回传手续费小于回传双金
        $bool = bcsub($apiDeposit, $apiService);
        // 回传双金 + 回传手续费
        // $apiAll = bcadd($apiDeposit, $apiService);
        if ($leftAmount >= 0 && $leftDeposit >= 0 && $bool >= 0) {    

            if ($leftAmount > 0) {
                // 发单 代练费退回(剩余回传代练费)
                Asset::handle(new Income($leftAmount, 7, $this->order->no, '退还代练费', $this->order->creator_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }
            }

            if ($apiAmount > 0) {
                // 接单 代练收入
                Asset::handle(new Income($apiAmount, 12, $this->order->no, '代练费收入', $this->order->gainer_primary_user_id));

                if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }

                if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new DailianException('流水记录写入失败');
                }
            }

            // 如果订单安全保证金 > (回传双金 + 手续费)
            // 安全保证金 》 回传双金
            if (bcsub($security, $apiDeposit) > 0) {
                if ($apiDeposit > 0) {
                    // 发单 安全保证金收入
                    Asset::handle(new Income($apiDeposit, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($apiService > 0) {
                    // 发单 手续费支出
                    Asset::handle(new Expend($apiService, 3, $this->order->no, '手续费支出', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                // 接单 剩下的安全保证金
                $leftSecurity = bcsub($security, $apiDeposit);

                if ($leftSecurity > 0) {
                    Asset::handle(new Income($leftSecurity, 8, $this->order->no, '退还安全保证金', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($efficiency) {
                    // 接单 退还效率保证金
                    Asset::handle(new Income($efficiency, 9, $this->order->no, '退还效率保证金', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($apiService > 0) {
                    // 接单 代练手续费收入
                    Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }
            } else if (bcsub($security, $apiDeposit) == 0) {
                if ($apiDeposit > 0) {
                    // 发单 安全保证金收入
                    Asset::handle(new Income($apiDeposit, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($apiService > 0) {
                    // 发单 手续费支出
                    Asset::handle(new Expend($apiService, 3, $this->order->no, '手续费支出', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                // 接单 退还全额效率保证金
                if ($efficiency) {
                    Asset::handle(new Income($efficiency, 9, $this->order->no, '退还效率保证金', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($apiService > 0) {
                    // 接单 代练手续费收入
                    Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }
            } else {
                // 发单 全额安全保证金收入
                if ($security) {
                    Asset::handle(new Income($security, 10, $this->order->no, '安全保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                // 发单 效率保证金收入
                $creatorEfficiency = bcsub($apiDeposit, $security);

                if ($creatorEfficiency > 0) {
                    Asset::handle(new Income($creatorEfficiency, 11, $this->order->no, '效率保证金收入', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($apiService > 0) {
                    // 发单 手续费支出
                    Asset::handle(new Expend($apiService, 3, $this->order->no, '手续费支出', $this->order->creator_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                // 接单 退还剩余效率保证金
                $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

                if ($leftEfficiency > 0) {
                    Asset::handle(new Income($leftEfficiency, 9, $this->order->no, '退还效率保证金', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }

                if ($apiService > 0) {
                     // 接单 代练手续费收入
                    Asset::handle(new Income($apiService, 6, $this->order->no, '代练手续费收入', $this->order->gainer_primary_user_id));

                    if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }

                    if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new DailianException('流水记录写入失败');
                    }
                }
            }
            // 写入获得双金金额
            OrderDetailRepository::updateByOrderNo($this->orderNo, 'get_amount', $apiDeposit);
            // 写入手续费
            OrderDetailRepository::updateByOrderNo($this->orderNo, 'poundage', $apiService);
            // 写入结算时间
            OrderDetailRepository::updateByOrderNo($this->orderNo, 'checkout_time', date('Y-m-d H:i:s'));

	    } else {
	    	throw new DailianException('参数传入错误或不满足条件');
	    }
	}
}
