<?php

namespace App\Extensions\Dailian\Controllers;

use DB;
use Asset;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\LevelingConsult;
use App\Extensions\Asset\Income;
use App\Extensions\Asset\Expend;
use App\Exceptions\OrderNoticeException;
use App\Repositories\Frontend\OrderDetailRepository;

/**
 * 订单报警中 公共操作类（流水信息）
 */
class PublicController
{
	public static function revokeFlows($orderNo)
    {
    	// 从leveling_consult 中取各种值
        $consult = LevelingConsult::where('order_no', $orderNo)->first();
        $order = Order::where('no', $orderNo)->first();
        $orderDetails = OrderDetail::where('order_no', $orderNo)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

        $amount = $consult->amount;
        $writeDeposit = $consult->deposit;

        if (! $amount || ! $consult || ! $writeDeposit) {
            throw new OrderNoticeException('不存在申诉和协商记录或不存在协商代练费或双金!');
        }
        // $apiDeposit = $consult->api_deposit;
        $apiService = $consult->api_service;
        // 订单的安全保证金
        $security = $orderDetails['security_deposit'];
        // 订单的效率保证金
        $efficiency = $orderDetails['efficiency_deposit'];
        // 剩余代练费 = 订单代练费 - 回传代练费
        $leftAmount = bcsub($order->amount, $amount);
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
                    Asset::handle(new Income($amount, 12, $order->no, '协商代练收入', $order->gainer_primary_user_id));

                    if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new OrderNoticeException('流水记录写入失败');
                    }

                    if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new OrderNoticeException('流水记录写入失败');
                    }
                }

                if ($leftAmount > 0) {
                    // 发单 退回剩余代练费 $leftAmount
                    Asset::handle(new Income($leftAmount, 7, $order->no, '退回协商代练费', $order->creator_primary_user_id));

                    if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                        throw new OrderNoticeException('流水记录写入失败');
                    }

                    if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                        throw new OrderNoticeException('流水记录写入失败');
                    }
                }
                
                // 如果订单安全保证金 > 填写双金
                if (bcsub($security, $writeDeposit) > 0) {
                    if ($writeDeposit > 0) {
                        // 发单 安全保证金收入
                        Asset::handle(new Income($writeDeposit, 10, $order->no, '安全保证金收入', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    // 手续费有可能为0，比如 12，20, 0，20
                    if ($apiService > 0) {
                        // 发单 支出手续费
                        Asset::handle(new Expend($apiService, 3, $order->no, '代练手续费支出', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    // 接单 退回 剩余安全保证金
                    $leftSecurity = bcsub($security, $writeDeposit);

                    if ($leftSecurity > 0) {
                        Asset::handle(new Income($leftSecurity, 8, $order->no, '安全保证金退回', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($efficiency > 0) {
                        // 接单 退回 全额效率保证金
                        Asset::handle(new Income($efficiency, 9, $order->no, '效率保证金退回', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 代练手续费收入
                        Asset::handle(new Income($apiService, 6, $order->no, '代练手续费收入', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }
                } else if (bcsub($security, $writeDeposit) == 0) {
                    if ($writeDeposit > 0) {
                        // 发单 安全保证金收入
                        Asset::handle(new Income($writeDeposit, 10, $order->no, '安全保证金收入', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 发单 支出手续费
                        Asset::handle(new Expend($apiService, 3, $order->no, '代练手续费支出', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($efficiency) {
                        // 接单 退回全额 效率保证金
                        Asset::handle(new Income($efficiency, 9, $order->no, '效率保证金退回', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 代练手续费收入
                        Asset::handle(new Income($apiService, 6, $order->no, '代练手续费收入', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }
                } else {
                    if ($security) {
                        // 发单 全额
                        Asset::handle(new Income($security, 10, $order->no, '安全保证金收入', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    // 发单  剩余效率保证金收入
                    $creatorEfficiency = bcsub($writeDeposit, $security);

                    if ($creatorEfficiency > 0) {
                        Asset::handle(new Income($creatorEfficiency, 11, $order->no, '效率保证金收入', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 发单 代练手续费支出
                        Asset::handle(new Expend($apiService, 3, $order->no, '代练手续费支出', $order->creator_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    // 接单 退回剩余效率保证金
                    $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

                    if ($leftEfficiency > 0) {
                        Asset::handle(new Income($leftEfficiency, 9, $order->no, '效率保证金退回', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }

                    if ($apiService > 0) {
                        // 接单 手续费收入
                        Asset::handle(new Income($apiService, 6, $order->no, '代练手续费收入', $order->gainer_primary_user_id));

                        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }

                        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                            throw new OrderNoticeException('流水记录写入失败');
                        }
                    }
                }
                // 写入获得金额
                OrderDetailRepository::updateByOrderNo($orderNo, 'get_amount', $writeDeposit);
                // 写入手续费
                OrderDetailRepository::updateByOrderNo($orderNo, 'poundage', $apiService);
            } catch (OrderNoticeException $e) {
                DB::rollBack();
                throw new OrderNoticeException($e->getMessage());
            }
            DB::commit();
        } else {
            throw new OrderNoticeException('无回传双金手续费或回传双金手续费超过订单双金!');
        }
    }

    public static function completeFlows($orderNo)
    {
    	DB::beginTransaction();
        try {
            $order = Order::where('no', $orderNo)->first();
            $orderDetails = OrderDetail::where('order_no', $orderNo)
                    ->pluck('field_value', 'field_name')
                    ->toArray();
        	// 接单 代练收入
            Asset::handle(new Income($order->amount, 12, $order->no, '代练订单完成收入', $order->gainer_primary_user_id));

            if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new OrderNoticeException('流水记录写入失败');
            }

            if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new OrderNoticeException('流水记录写入失败');
            }

            if ($order->detail()->where('field_name', 'security_deposit')->value('field_value')) {    
                // 接单 退回安全保证金
                Asset::handle(new Income($orderDetails['security_deposit'], 8, $order->no, '退回安全保证金', $order->gainer_primary_user_id));

                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }

                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }
            }

            if ($order->detail()->where('field_name', 'efficiency_deposit')->value('field_value')) {
                // 接单 退效率保证金
                Asset::handle(new Income($orderDetails['efficiency_deposit'], 9, $order->no, '退回效率保证金', $order->gainer_primary_user_id));

                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }

                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }
            }

        } catch (OrderNoticeException $e) {
            DB::rollback();
            throw new OrderNoticeException($e->getMessage());
        }
        DB::commit();
    }

    public static function arbitrationFlows($orderNo)
    {
    	// 从leveling_consult 中取各种值
        $consult = LevelingConsult::where('order_no', $orderNo)->first();
        $order = Order::where('no', $orderNo)->first();
        $orderDetails = OrderDetail::where('order_no', $orderNo)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

        if (!$consult) {
        	throw new OrderNoticeException('状态错误');
        }
        $apiAmount = $consult->api_amount;
        $apiDeposit = $consult->api_deposit;
        $apiService = $consult->api_service;
		// 订单的安全保证金
		$security = $orderDetails['security_deposit'];
		// 订单的效率保证金 efficiency_deposit
		$efficiency = $orderDetails['efficiency_deposit'];
		// 剩余代练费 = 订单代练费 - 回传代练费
        $leftAmount = bcsub($order->amount, $apiAmount);
        // 订单双金 = 订单安全保证金 + 订单效率保证金
        $deposit = bcadd($security, $efficiency);
        // 回传双金必须小于代练双金
        $leftDeposit = bcsub($deposit, $apiDeposit);
        // 回传手续费小于回传双金
        $bool = bcsub($apiDeposit, $apiService);
        // 回传双金 + 回传手续费
        // $apiAll = bcadd($apiDeposit, $apiService);

        if ($leftAmount >= 0 && $leftDeposit >= 0 && $bool >= 0) {    

            DB::beginTransaction();
        	try {
        		if ($leftAmount > 0) {
	                // 发单 代练费退回(剩余回传代练费)
	                Asset::handle(new Income($leftAmount, 7, $order->no, '退还代练费', $order->creator_primary_user_id));

	                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new OrderNoticeException('流水记录写入失败');
	                }

	                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new OrderNoticeException('流水记录写入失败');
	                }
        		}

                if ($apiAmount > 0) {
	                // 接单 代练收入
	                Asset::handle(new Income($apiAmount, 12, $order->no, '代练费收入', $order->gainer_primary_user_id));

	                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
	                    throw new OrderNoticeException('流水记录写入失败');
	                }

	                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
	                    throw new OrderNoticeException('流水记录写入失败');
	                }
                }

                // 如果订单安全保证金 > (回传双金 + 手续费)
                // 安全保证金 》 回传双金
                if (bcsub($security, $apiDeposit) > 0) {  
                	if ($apiDeposit) {
		                // 发单 安全保证金收入
		                Asset::handle(new Income($apiDeposit, 10, $order->no, '安全保证金收入', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
                	}

	                if ($apiService > 0) {	                	
		                // 发单 手续费支出
		                Asset::handle(new Expend($apiService, 3, $order->no, '手续费支出', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                // 接单 剩下的安全保证金
	                $leftSecurity = bcsub($security, $apiDeposit);

	                if ($leftSecurity > 0) {
		                Asset::handle(new Income($leftSecurity, 8, $order->no, '退还安全保证金', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                if ($efficiency) {
		                // 接单 退还效率保证金
		                Asset::handle(new Income($efficiency, 9, $order->no, '退还效率保证金', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                if ($apiService > 0) {
		                // 接单 代练手续费收入
		                Asset::handle(new Income($apiService, 6, $order->no, '代练手续费收入', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }
                } else if (bcsub($security, $apiDeposit) == 0) {
                	if ($apiDeposit > 0) {
	                	// 发单 安全保证金收入
		                Asset::handle(new Income($apiDeposit, 10, $order->no, '安全保证金收入', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
                	}

	                if ($apiService > 0) {
		                // 发单 手续费支出
		                Asset::handle(new Expend($apiService, 3, $order->no, '手续费支出', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                // 接单 退还全额效率保证金
	                if ($efficiency) {
		                Asset::handle(new Income($efficiency, 9, $order->no, '退还效率保证金', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                if ($apiService > 0) {
		                // 接单 代练手续费收入
		                Asset::handle(new Income($apiService, 6, $order->no, '代练手续费收入', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }
                } else {
                	// 发单 全额安全保证金收入
                	if ($security) {
	                	Asset::handle(new Income($security, 10, $order->no, '安全保证金收入', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
                	}

	                // 发单 效率保证金收入
	                $creatorEfficiency = bcsub($apiDeposit, $security);

	                if ($creatorEfficiency > 0) {
		                Asset::handle(new Income($creatorEfficiency, 11, $order->no, '效率保证金收入', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                if ($apiService > 0) {
		                // 发单 手续费支出
		                Asset::handle(new Expend($apiService, 3, $order->no, '手续费支出', $order->creator_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                // 接单 退还剩余效率保证金
	                $leftEfficiency = bcsub($efficiency, $creatorEfficiency);

	                if ($leftEfficiency > 0) {
		                Asset::handle(new Income($leftEfficiency, 9, $order->no, '退还效率保证金', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }

	                if ($apiService > 0) {
		                 // 接单 代练手续费收入
		                Asset::handle(new Income($apiService, 6, $order->no, '代练手续费收入', $order->gainer_primary_user_id));

		                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }

		                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
		                    throw new OrderNoticeException('流水记录写入失败');
		                }
	                }
                }
                // 写入获得双金金额
                OrderDetailRepository::updateByOrderNo($orderNo, 'get_amount', $apiDeposit);
                // 写入手续费
                OrderDetailRepository::updateByOrderNo($orderNo, 'poundage', $apiService);
	        } catch (OrderNoticeException $e) {
	            DB::rollBack();
	        }
	        DB::commit();
	    } else {
	    	throw new OrderNoticeException('参数传入错误或不满足条件');
	    }
    }

    public static function forceRevokeFlows($orderNo)
    {
    	DB::beginTransaction();
        try {
            $orderDetails = OrderDetail::where('order_no', $orderNo)
                    ->pluck('field_value', 'field_name')
                    ->toArray();
            $order = Order::where('no', $orderNo)->first();
            // 发单 退回代练费
            Asset::handle(new Income($order->amount, 7, $order->no, '退回代练费', $order->creator_primary_user_id));

            if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new OrderNoticeException('流水记录写入失败');
            }

            if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new OrderNoticeException('流水记录写入失败');
            }

            if ($order->detail()->where('field_name', 'security_deposit')->value('field_value')) {        
                // 接单 退回安全保证金
                Asset::handle(new Income($orderDetails['security_deposit'], 8, $order->no, '安全保证金退回', $order->gainer_primary_user_id));

                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }

                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }
            }

            if ($order->detail()->where('field_name', 'efficiency_deposit')->value('field_value')) {        
                // 接单 退效率保证金
                Asset::handle(new Income($orderDetails['efficiency_deposit'], 9, $order->no, '效率保证金退回', $order->gainer_primary_user_id));

                if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }

                if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                    throw new OrderNoticeException('流水记录写入失败');
                }
            }
        } catch (OrderNoticeException $e) {
            DB::rollback();
            throw new OrderNoticeException($e->getMessage());
        }
        DB::commit();
    }

    public static function deleteFlows($orderNo)
    {
    	DB::beginTransaction();
        try {
            $order = Order::where('no', $orderNo)->first();
            // 发单 退回代练费
            Asset::handle(new Income($order->amount, 7, $order->no, '退回代练费', $order->creator_primary_user_id));

            if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new OrderNoticeException('流水记录写入失败');
            }

            if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new OrderNoticeException('流水记录写入失败');
            }
        } catch (OrderNoticeException $e) {
            DB::rollback();
            throw new OrderNoticeException($e->getMessage());
        }
        DB::commit();
    }
}