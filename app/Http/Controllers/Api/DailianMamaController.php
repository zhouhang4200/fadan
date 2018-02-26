<?php

namespace App\Http\Controllers\Api;

use Redis;
use App\Models\Order;
use App\Models\OrderNotice;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException;
use App\Http\Controllers\Controller;
use App\Extensions\Dailian\Controllers\DailianFactory;

class DailianMamaController extends Controller
{
	protected $userId = 1111;

	/**
     * 检查签名和订单号
     * @param  [type] $sign    [description]
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function checkOrder($orderNo)
    {
        $orderDetail = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->first();

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
     * 成功信息
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function success($message, $order)
    {
        if ($order) {
            $action = \Route::currentRouteAction();
            $this->checkAndAddOrderToRedis($order, '1-'.$action);
            // $this->checkOrderNotice($order);
        }

        return json_encode([
            'status' => 1,
            'message' => $message,
        ]);
    }

    /**
     * 失败信息
     * @param  [type] $message [description]
     * @param  [type] $order   [description]
     * @return [type]          [description]
     */
    public function fail($message, $order)
    {
        if ($order) {
            // $this->addOrderNotice($order);  
            $action = \Route::currentRouteAction();
            $this->checkAndAddOrderToRedis($order, '0-'.$action);
        }

        return json_encode([
            'status' => 0,
            'message' => $message,
        ]);
        \Log::info($message);
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
                $status = explode('-', $statusAndAction)[0];
            if ($status && $status == 1) {
                $orderNotice = OrderNotice::where('order_no', $order->no)->first();

                if ($orderNotice) {
                    $result = Redis::hSet('notice_orders', $order->no, $statusAndAction);
                    \Log::info('操作成功!记录正在写入redis，结果：'.$result, ['order_no' => $order->no, 'status' => $status]);
                } else {
                    \Log::info('操作成功!记录没有写入redis.', ['order_no' => $order->no, 'status' => $status]);
                }      
            } else {
                $result = Redis::hSet('notice_orders', $order->no, $statusAndAction);
                \Log::info('操作失败!记录正在写入redis，结果：'.$result, ['order_no' => $order->no, 'status' => $status]);
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
    		case '订单下架':
    			try {
		    		// 检查订单号存不存在
		    		$order = $this->checkOrder($datas['orderid']);
		    		// 接单操作
					DailianFactory::choose('offSale')->run($order->no, $this->userId, false);
					// 操作成功，看之前有没有失败的操作记录，有的话存到redis
					return $this->success('订单下架成功', $order);
		    	} catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '接单':
		    	try {
		    		// 检查订单号存不存在
		    		$order = $this->checkOrder($datas['orderid']);
		    		// 接单操作
					DailianFactory::choose('receive')->run($order->no, $this->userId, false);
					// 操作成功，看之前有没有失败的操作记录，有的话存到redis
					return $this->success('接单成功', $order);
		    	} catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '提交异常':
    			try {
		    		// 检查订单号存不存在
		    		$order = $this->checkOrder($datas['orderid']);
		    		// 接单操作
					DailianFactory::choose('abnormal')->run($order->no, $this->userId, false);
					// 操作成功，看之前有没有失败的操作记录，有的话存到redis
					return $this->success('提交异常成功', $order);
		    	} catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '取消异常':
    			try {
		    		// 检查订单号存不存在
		    		$order = $this->checkOrder($datas['orderid']);
		    		// 接单操作
					DailianFactory::choose('cancelAbnormal')->run($order->no, $this->userId, false);
					// 操作成功，看之前有没有失败的操作记录，有的话存到redis
					return $this->success('取消异常成功', $order);
		    	} catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '申请撤销':
    			DB::beginTransaction();
    			try {
		    		// 检查订单号存不存在
		    		$order = $this->checkOrder($datas['orderid']);
		    		// 订单详情
		    		$orderDetails = OrderDetail::where('order_no', $this->order->no)
	                    ->pluck('field_value', 'field_name')
	                    ->toArray();
		    		// 接收到的信息，写到申诉表
		    		$apiAmount = bcsub($order->amount, $request->price_pay); // 发单商家获得代练费, 
		    																//由于我们这里字段是接单获得的代练费，所以要减
		            $apiDeposit = $request->price_get; // 发单商家获得的双金
		            $content = $request->reason ?: '无'; // 理由
		            $apiService = $request->price_pay_fee; // 商家支付的手续费

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
		                'user_id' => $this->userId,
		                'order_no' => $order->no,
		                'amount' => $apiAmount,
		                'api_amount' => $apiAmount,
		                'api_deposit' => $apiDeposit,
		                'api_service' => $apiService,
		                'deposit' => $apiDeposit,
		                'consult' => 2,
		                'revoke_message' => $content,
		            ];
		            // 更新协商信息到协商表
		            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
		    		// 接单操作
					DailianFactory::choose('revoke')->run($order->no, $this->userId, false);
					// 操作成功，看之前有没有失败的操作记录，有的话存到redis
					return $this->success('申请撤销成功!', $order);
		    	} catch (DailianException $e) {
		    		DB::rollBack();
		            return $this->fail($e->getMessage(), $order);
		    	}
		    	DB::commit();
    			break;
    		case '取消撤销':
    			try {
		    		// 检查订单号存不存在
		    		$order = $this->checkOrder($datas['orderid']);
		    		// 接单操作
					DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, false);
					// 操作成功，看之前有没有失败的操作记录，有的话存到redis
					return $this->success('取消撤销成功!', $order);
		    	} catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '同意撤销':
    			 DB::beginTransaction();
		    	try {
		            $apiDeposit = $request->price_get; // 发单获得的双金
		            $apiService = $request->price_pay_fee;

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
						'complete' => 1,
					];

		            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);

		            DailianFactory::choose('agreeRevoke')->run($order->no, $this->userId, false);
		            // 手续费写到order_detail中
		            OrderDetail::where('field_name', 'poundage')
		                ->where('order_no', $order->no)
		                ->update(['field_value' => $apiService]);

		    	} catch (DailianException $e) {
		            DB::rollBack();
		            return $this->fail($e->getMessage(), $order);
		    	}
		        DB::commit();
        		return $this->success('已同意撤销', $order);
    			break;
    		case '申请验收':
    			try {
		            $order = $this->checkOrder($datas['orderid']);

					DailianFactory::choose('applyComplete')->run($order->no, $this->userId);

		            return $this->success('已申请验收', $order);
		    	} catch (DailianException $e) {
		    		return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '取消验收':
    			try {
		            $order = $this->checkOrder($datas['orderid']);

		            DailianFactory::choose('cancelComplete')->run($order->no, $this->userId);

		            return $this->success('已取消验收', $order);
		        } catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		        }
    			break;
    		case '验收完成':
    			try {
		            $order = $this->checkOrder($datas['orderid']);
		            
		            DailianFactory::choose('complete')->run($order->no, $this->userId, false);

		            return $this->success('验收完成', $order);
		        } catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		        }
    			break;
    		case '申请仲裁':
    			try {
		            DB::beginTransaction();
		            try {
		                myLog('exception-appeal', ['进入']);
		                // 原因
		                $content = $request->input('reason', '无');
		                // 验证订单号是否存在
		                $order = $this->checkOrder($datas['orderid']);

		                $data = [
		                    'user_id' => $this->userId,
		                    'complain' => 2,
		                    'complain_message' => $content,
		                ];

		                $result  = LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
		                // 写入日志
		                myLog('appeal', ['user' => $this->userId, 'message' => $content, 'no' => $order->no, 'result' => $result]);
		                DailianFactory::choose('applyArbitration')->run($order->no, $this->userId, 0);
		            } catch (DailianException $e) {
		                DB::rollBack();
		                myLog('exception-appeal', [$e->getMessage()]);
		                return $this->fail($e->getMessage(), $order);
		            }
		            DB::commit();
		            return $this->success('已申请申诉', $order);
		        } catch (\Exception $exception) {
		            myLog('exception-appeal', [$exception->getMessage()]);
		        }
    			break;
    		case '取消仲裁':
    			try {
		            $order = $this->checkOrder($datas['orderid']);

		            DailianFactory::choose('cancelArbitration')->run($order->no, $this->userId, false);

		            return $this->success('已取消申诉', $order);
		    	} catch (DailianException $e) {
		    		return $this->fail($e->getMessage(), $order);
		    	}
    			break;
    		case '仲裁完成':
    			DB::beginTransaction();
		    	try {
		            $apiAmount = bcsub($order->amount, $request->price_pay); // 发单商家获得代练费, 
		   			//由于我们这里字段是接单获得的代练费，所以要减
		            $apiDeposit = $request->price_get; // 发单商家获得的双金
		            $apiService = $request->price_pay_fee; // 商家支付的手续费
		            // 检查订单号是否存在
		            $order = $this->checkOrder($datas['orderid']);

					if (! is_numeric($apiDeposit) || ! is_numeric($apiService) || ! is_numeric($apiAmount)) {
		                throw new DailianException('回传双金、手续费和代练费必须是数字');
					}

		            if ($apiDeposit < 0 || $apiService < 0 || $apiAmount < 0) {
		                throw new DailianException('回传双金、手续费和代练费必须大于等于0');
		            }

					$data = [
						'api_amount' => $apiAmount,
						'api_deposit' => $apiDeposit,
						'api_service' => $apiService,
						'complete' => 2,
					];
		            // 更新代练协商申诉表
					LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
		            // 同意申诉
		            DailianFactory::choose('arbitration')->run($order->no, $this->userId, 0);
		            // 手续费写到order_detail中
		            OrderDetail::where('field_name', 'poundage')
		                ->where('order_no', $order->no)
		                ->update(['field_value' => $apiService]);

		    	} catch (DailianException $e) {
		            DB::rollBack();
		            return $this->fail($e->getMessage(), $order);
		    	} catch (\Exception $exception) {
		            DB::rollBack();
		            myLog('exception', $exception->getMessage());
		            return $this->fail($exception->getMessage(), $order);
		        }
		        DB::commit();
		        return $this->success('已同意申诉', $order);
    			break;
    		case '锁定账号':
    			try {
		            $order = $this->checkOrder($datas['orderid']);
		            
		            DailianFactory::choose('lock')->run($order->no, $this->userId, false);

		            return $this->success('已锁定账号', $order);
		        } catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		        }
    			break;
    		case '取消锁定':
    			try {
		            $order = $this->checkOrder($datas['orderid']);
		            
		            DailianFactory::choose('cancelLock')->run($order->no, $this->userId, false);

		            return $this->success('已取消锁定账号', $order);
		        } catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		        }
    			break;
    		case '自动验收':
    			try {
		            $order = $this->checkOrder($datas['orderid']);
		            
		            DailianFactory::choose('complete')->run($order->no, $this->userId, false);

		            return $this->success('已自动验收', $order);
		        } catch (DailianException $e) {
		            return $this->fail($e->getMessage(), $order);
		        }
    			break;
    		default:
    			throw new DailianException('不存在的第三方操作!');
    			break;
    	}
    }
}
