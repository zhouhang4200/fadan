<?php

namespace App\Http\Controllers\Api;

use Redis, Log, DB;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderNotice;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException;
use App\Http\Controllers\Controller;
use App\Extensions\Dailian\Controllers\DailianFactory;

class DailianMamaController extends Controller
{
	// protected $userId = 8556;

	public function __construct()
	{
		$this->userId = config('dailianmama.qs_user_id');
	}

	/**
     * 订单号
     * @param  [type] $sign    [description]
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function checkOrder($orderNo)
    {
        $orderDetail = OrderDetail::where('field_name', 'dailianmama_order_no')
	        ->where('field_value', $orderNo)
	        ->first();

        if (! $orderDetail) {
            throw new DailianException('订单号缺失或错误');
        } else {
            $order = Order::where('no', $orderDetail->order_no)->first();

            if (! $order) {
                throw new DailianException('内部订单号缺失,请联系我们');
            } 
            return $order;
        }
    }

     /**
     * 成功信息,操作成功之后，给一个值为1的状态
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function success($message, $order, $operate, $orderStatus)
    {
        if ($order) {
            // 查看redis是否有之前操作失败的记录
            $this->checkAndAddOrderToRedis($order, '1-2-'.$operate.'-'.$orderStatus);
        }
        // 返回成功信息给代练妈妈
        return 'success';
    }

    /**
     * 失败信息，操作成功之后，给一个值为0的状态
     * @param  [type] $message [description]
     * @param  [type] $order   [description]
     * @return [type]          [description]
     */
    public function fail($message, $order, $operate, $orderStatus)
    {
        if ($order) { 
            // 操作失败，写入redis，记录操作状态为0
            $this->checkAndAddOrderToRedis($order, '0-2-'.$operate.'-'.$orderStatus);
        }

        Log::info($message);
        return json_encode([
            'status' => 0,
            'message' => $message,
        ]);
        throw new DailianException($message);
    }

     /**
     * 操作成功的时候，检查订单报警有没有此单，有的话再次写入redis
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public function checkAndAddOrderToRedis($order, $statusAndAction)
    {
        if ($order) {
        		// 获取操作状态，为0表示失败操作，为1表示成功操作，作用主要是后台订单报警里面操作动作的颜色
        		// 绿色为成功操作，红色为失败操作
                $status = explode('-', $statusAndAction)[0];
            if ($status && $status == 1) { // 为1表示操作成功,从success方法里面出来的
                $orderNotice = OrderNotice::where('order_no', $order->no)->first();

                if ($orderNotice) {
                    $result = Redis::hSet('notice_orders', $order->no, $statusAndAction);
                    Log::info('操作成功!记录正在写入redis，结果：'.$result, ['order_no' => $order->no, 'status' => $status]);
                } else {
                    Log::info('操作成功!记录没有写入redis.', ['order_no' => $order->no, 'status' => $status]);
                }      
            } else { // 表示操作失败,从fail方法里面出来的
                $result = Redis::hSet('notice_orders', $order->no, $statusAndAction);
                Log::info('操作失败!记录正在写入redis，结果：'.$result, ['order_no' => $order->no, 'status' => $status]);
            }
        }
        return true;
    }

    /**
     * 订单状态改变回调接口
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function orderChange(Request $request)
    {
    	$order = null;
    	$datas = $request->all();

    	switch ($datas['operationinfo']) {
    		case '订单下架': // 我们的状态 22
		    	// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
				if ($request->operationuserid != 100308582) {
	    			try {
				    	// 检查订单号存不存在
				    	$order = $this->checkOrder($datas['orderid']);
			    		// 接单操作
						DailianFactory::choose('offSale')->run($order->no, $this->userId, false);
						// 操作成功，看之前有没有失败的操作记录，有的话存到redis
						return $this->success('订单下架成功', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
	    		}
    			break;
    		case '接单': // 我们的状态 13
	    		// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
			    	try {
			    		// 检查订单号存不存在
			    		$order = $this->checkOrder($datas['orderid']);
			    		// 接单操作
						DailianFactory::choose('receive')->run($order->no, $this->userId, true);
						// 操作成功，看之前有没有失败的操作记录，有的话存到redis
						return $this->success('接单成功', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
			    }
    			break;
    		case '提交异常': // 我们的状态17
				// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
    				try {
			    		// 检查订单号存不存在
			    		$order = $this->checkOrder($datas['orderid']);
			    		// 接单操作
						DailianFactory::choose('abnormal')->run($order->no, $this->userId, false);
						// 操作成功，看之前有没有失败的操作记录，有的话存到redis
						return $this->success('提交异常成功', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
		    	}
    			break;
    		case '取消异常':
				// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
    				try {
			    		// 检查订单号存不存在
			    		$order = $this->checkOrder($datas['orderid']);
			    		// 接单操作
						DailianFactory::choose('cancelAbnormal')->run($order->no, $this->userId, false);
						// 操作成功，看之前有没有失败的操作记录，有的话存到redis
						return $this->success('取消异常成功', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
				}
    			break;
    		case '申请撤销':
				// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
    				try {
    					// DB::beginTransaction();
			    		// 检查订单号存不存在
			    		$order = $this->checkOrder($datas['orderid']);
			    		// 订单详情
			    		$orderDetails = OrderDetail::where('order_no', $order->no)
		                    ->pluck('field_value', 'field_name')
		                    ->toArray();
			    		// 接收到的信息，写到申诉表
			    		$apiAmount = $request->price_pay; // 发单商家获得代练费, 
			            $apiDeposit = $request->price_get; // 发单商家获得的双金
			            $content = $request->reason; // 理由
			            $apiService = $request->price_pay_fee ?? 0; // 发单商家支付的手续费

			            // 获取手续费
			            switch ($order->game_id) { 
			            	case 1: // 王者荣耀
			            		// 根据双金来算, 这里如果传的值很小或很大，会变成科学计数, 手续费最高20
			            		$apiService = bcmul(ceil(bcmul($apiDeposit, 0.05)*10), 0.1) < 20 ?
			            			bcmul(ceil(bcmul($apiDeposit, 0.05)*10), 0.1) : 20;
			            		break;
			            	case 78: //英雄联盟
			            		// 根据双金来算
			            		switch ($apiDeposit) {
			            			case $apiDeposit > 0 && $apiDeposit <= 1:
			            				$apiService = 0;
			            				break;
			            			case $apiDeposit > 1 && $apiDeposit < 10:
			            				$apiService = 1;
			            				break;
			            			case $apiDeposit >= 10 && $apiDeposit < 20:
			            				$apiService = 2;
			            				break;
			            			case $apiDeposit >= 20 && $apiDeposit <= 50:
			            				$apiService = 3;
			            				break;
			            			case $apiDeposit > 50 && $apiDeposit <= 100:
			            				$apiService = 5;
			            				break;
			            			case $apiDeposit > 100 && $apiDeposit <= 150:
			            				$apiService = 6;
			            				break;
			            			case $apiDeposit > 150 && $apiDeposit <= 200:
			            				$apiService = 7;
			            				break;
			            			case $apiDeposit > 200 && $apiDeposit <= 250:
			            				$apiService = 8;
			            				break;
			            			case $apiDeposit > 250 && $apiDeposit <= 300:
			            				$apiService = 9;
			            				break;
			            			case $apiDeposit > 300 && $apiDeposit <= 350:
			            				$apiService = 10;
			            				break;
			            			case $apiDeposit > 350 && $apiDeposit <= 400:
			            				$apiService = 11;
			            				break;
			            			case $apiDeposit > 400 && $apiDeposit <= 450:
			            				$apiService = 12;
			            				break;
			            			case $apiDeposit > 450 && $apiDeposit <= 500:
			            				$apiService = 13;
			            				break;
			            			case $apiDeposit > 500 && $apiDeposit <= 550:
			            				$apiService = 14;
			            				break;
			            			case $apiDeposit > 550 && $apiDeposit <= 600:
			            				$apiService = 15;
			            				break;
			            			case $apiDeposit > 600 && $apiDeposit <= 650:
			            				$apiService = 16;
			            				break;
			            			case $apiDeposit > 650 && $apiDeposit <= 700:
			            				$apiService = 17;
			            				break;
			            			case $apiDeposit > 700 && $apiDeposit <= 750:
			            				$apiService = 18;
			            				break;
			            			case $apiDeposit > 750 && $apiDeposit <= 800:
			            				$apiService = 19;
			            				break;
			            			case $apiDeposit > 800 && $apiDeposit <= 850:
			            				$apiService = 20;
			            				break;
			            			case $apiDeposit > 850:
			            				$apiService = 20;
			            				break;
			            			default:
			            				$apiService = 0;
			            				break;
			            		}
			            	case 81: // QQ飞车手游
			            		$apiService = 0;
			            		break;
			            	case 79: // 决战平安京
			            		$apiService = 0;
			            		break;
			            	case 80: // cf枪战王者
			            	 	$apiService = 0;
			            	 	break;
			            	case 86: // DNF
			            		$apiService = 0;
			            		break;
			            	case 95: // 刺激战场
			            		$apiService = 0;
			            		break;
			            	case 96: // 全军出击
			            		$apiService = 0;
			            		break;
			            	default:
			            		$apiService = 0;
			            		break;
			            }

						if (! is_numeric($apiAmount) || ! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
			                throw new DailianException('代练费和双金或手续费必须是数字');
						}

						if ($apiAmount < 0 || $apiDeposit < 0 || $apiService < 0) {
			                throw new DailianException('代练费和双金或手续费必须大于等于0');
						}

			            $safeDeposit = $orderDetails['security_deposit'];
			            $effectDeposit = $orderDetails['efficiency_deposit'];
			            $orderDeposit = bcadd($safeDeposit, $effectDeposit);
			            $isOverDeposit = bcsub($orderDeposit, $apiDeposit);
			            $isOverAmount = bcsub($order->amount, $apiAmount);
			            // 写入双金与订单双击比较
			            if ($isOverDeposit < 0) {
			                throw new DailianException('传入双金超过订单代练双金');
			            }
			            // 写入代练费与订单代练费比较
			            if ($isOverAmount < 0) {
			                throw new DailianException('传入代练费超过订单代练费');
			            }

			            $data = [
							'user_id'        => $this->userId,
							'order_no'       => $order->no,
							'amount'         => $apiAmount,
							'api_amount'     => $apiAmount,
							'api_deposit'    => $apiDeposit,
							'api_service'    => $apiService,
							'deposit'        => $apiDeposit,
							'consult'        => 2,
							'revoke_message' => $content,
			            ];
						
						// 如果操作人是 发单人，那么 操作人是发单主ID，发起撤销人为 发单人（1）
			            if ($request->operationuserid == $order->creator_user_id) {
			            	$data['user_id'] = $order->creator_user_id;
			            	$data['consult'] = 1;
			            }
			    		// 接单操作
						DailianFactory::choose('revoke')->run($order->no, $this->userId, false);
			            // 更新协商信息到协商表
			            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
						// 操作成功，看之前有没有失败的操作记录，有的话存到redis
						return $this->success('申请撤销成功!', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			    		// DB::rollback();
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (\Exception $e) {
			    		// DB::rollback();
			    		return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
			    	// DB::commit();
				}
    			break;
    		case '取消撤销':
				// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
    				try {
			    		// 检查订单号存不存在
			    		$order = $this->checkOrder($datas['orderid']);
			    		// 接单操作
						DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, false);
						// 操作成功，看之前有没有失败的操作记录，有的话存到redis
						return $this->success('取消撤销成功!', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
				}
    			break;
    		case '同意撤销':
	    		// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			// DB::beginTransaction();
			    	try {
			            $apiDeposit = $request->price_get; // 发单获得的双金
			            $apiService = $request->price_pay_fee; //发单支出的手续费
			            // 判断订单是否存在
			            $order = $this->checkOrder($datas['orderid']);

						if (! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
			                throw new DailianException('回传双金和手续费必须是数字');
						}

			            if ($apiDeposit < 0 || $apiService < 0) {
			                throw new DailianException('回传双金和手续费必须大于或等于0');
			            }

						$data = [
							'api_deposit' => $apiDeposit,
							'api_service' => $apiService,
							'complete'    => 1,
						];
						// 写入到 协商仲裁 表
			            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
			            // 同意撤销
			            DailianFactory::choose('agreeRevoke')->run($order->no, $this->userId, false);
			            // 手续费写到order_detail中
			            OrderDetail::where('field_name', 'poundage')
			                ->where('order_no', $order->no)
			                ->update(['field_value' => $apiService]);
			    	} catch (DailianException $e) {
			            // DB::rollback();
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
			        // DB::commit();
	        		return $this->success('已同意撤销', $order, $datas['operationinfo'], $datas['orderstatusname']);
	        	}
    			break;
    		case '申请验收':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 判断订单是否存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 申请验收 操作
						DailianFactory::choose('applyComplete')->run($order->no, $this->userId);

			            return $this->success('已申请验收', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			    		return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
			    }
    			break;
    		case '取消验收':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 判断订单是否存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 取消验收 操作
			            DailianFactory::choose('cancelComplete')->run($order->no, $this->userId);

			            return $this->success('已取消验收', $order, $datas['operationinfo'], $datas['orderstatusname']);
			        } catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			        }
			    }
    			break;
    		case '验收完成':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 判断订单是否存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 验收完成 操作
			            DailianFactory::choose('complete')->run($order->no, $this->userId, false);

			            return $this->success('验收完成', $order, $datas['operationinfo'], $datas['orderstatusname']);
			        } catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			        }
			    }
    			break;
    		case '申请仲裁':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
			            // DB::beginTransaction();
			            try {
			            	// 记录日志
			                myLog('exception-appeal', ['进入']);
			                // 原因
			                $content = $request->reason;
			                // 验证订单号是否存在
			                $order = $this->checkOrder($datas['orderid']);

			                $data = [
			                    'user_id' => $this->userId,
			                    'complain' => 2,
			                    'complain_message' => $content,
			                ];
			               	// 申请仲裁 操作
			                DailianFactory::choose('applyArbitration')->run($order->no, $this->userId, false);
			                // 记录写入 协商仲裁 表
			                $result  = LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
			                // 写入日志
			                myLog('appeal', ['user' => $this->userId, 'message' => $content, 'no' => $order->no, 'result' => $result]);
			            } catch (DailianException $e) {
			                // DB::rollback();
			                myLog('exception-appeal', [$e->getMessage()]);
			                return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			            }
			            // DB::commit();
			            return $this->success('已申请申诉', $order, $datas['operationinfo'], $datas['orderstatusname']);
			        } catch (\Exception $exception) {
			            myLog('exception-appeal', [$exception->getMessage()]);
			        }
		       	}
    			break;
    		case '取消仲裁':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 判断订单收存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 取消仲裁 操作
			            DailianFactory::choose('cancelArbitration')->run($order->no, $this->userId, false);

			            return $this->success('已取消申诉', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (DailianException $e) {
			    		return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	}
			   	}
    			break;
    		case '仲裁完成':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			// DB::beginTransaction();
			    	try {
			            $apiAmount = $request->price_pay ?? 0; // 发单商家获得代练费
			            $apiDeposit = $request->price_get ?? 0; // 发单商家获得的双金
			            $apiService = $request->price_pay_fee ?? 0; // 发单商家支付的手续费
			            // 检查订单号是否存在
			            $order = $this->checkOrder($datas['orderid']);

						$data = [
							'api_amount' => $apiAmount,
							'api_deposit' => $apiDeposit,
							'api_service' => $apiService,
							'complete' => 2,
						];
			            // 更新代练协商申诉表
						$res = LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
			            // 同意申诉
			            DailianFactory::choose('arbitration')->run($order->no, $this->userId, false);
			            // 手续费写到order_detail中
			            OrderDetail::where('field_name', 'poundage')
			                ->where('order_no', $order->no)
			                ->update(['field_value' => $apiService]);
			    	} catch (DailianException $e) {
			            // DB::rollback();
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			    	} catch (\Exception $exception) {
			            // DB::rollback();
			            myLog('exception', $exception->getMessage());
			            return $this->fail($exception->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			        }
			        // DB::commit();
			        return $this->success('已同意申诉', $order, $datas['operationinfo'], $datas['orderstatusname']);
			    }
    			break;
    		case '锁定账号':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 判断订单是否存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 锁定账号 操作
			            DailianFactory::choose('lock')->run($order->no, $this->userId, false);

			            return $this->success('已锁定账号', $order, $datas['operationinfo'], $datas['orderstatusname']);
			        } catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			        }
			    }
    			break;
    		case '取消锁定':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 判断 订单是否存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 取消锁定 操作
			            DailianFactory::choose('cancelLock')->run($order->no, $this->userId, false);

			            return $this->success('已取消锁定账号', $order, $datas['operationinfo'], $datas['orderstatusname']);
			        } catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $datas['orderstatusname']);
			        }
			    }
    			break;
    		case '自动验收':
    			// 订单不是发单方操作才调我接口，如果是自己操作，不掉自己接口
	    		if ($request->operationuserid != 100308582) {
	    			try {
	    				// 检查 订单是否存在
			            $order = $this->checkOrder($datas['orderid']);
			            // 自动完成 操作 和 完成 操作一样，我们没有自动完成这个操作
			            DailianFactory::choose('complete')->run($order->no, $this->userId, false);

			            return $this->success('已自动验收', $order, $datas['operationinfo'], $datas['orderstatusname']);
			        } catch (DailianException $e) {
			            return $this->fail($e->getMessage(), $order, $datas['operationinfo'], $data['orderstatusname']);
			        }
			    }
    			break;
    		default:
    			throw new DailianException('不存在的第三方操作!');
    			break;
    	}
    }
}
