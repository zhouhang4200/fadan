<?php

namespace App\Http\Controllers\Api;

use Exception, DB;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Models\LevelingConsult;

class LevelingController
{	
    protected $sign = 'a46ae5de453bfaadc8548a3e48c151db';

    public function checkSign($sign)
    {
        if ($sign != $this->sign) {
            return json_encode([
                'status' => 0,
                'message' => '验证失败',
            ]);
        }
    }

	/**
	 *     接单
	 * @param  [type] $orderNo [description]
	 * @param  [type] $status  [description]
	 * @return [type]          [description]
	 */
    public function receiveOrder(Request $request)
    {
    	try {
            $orderNo = $request->orderNo;
            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			DailianFactory::choose('receive')->run($order->no, 1, 0);

    			return json_encode([
    				'status' => 1,
    				'message' => '已接单',
    			]);
    		}
    	} catch (Exception $e) {
            return json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
    		throw new Exception($e->getMessage());
    	}
    }

    /**
     * 同意协商
     * @param  [type] $orderNo    [订单号]
     * @param  [type] $status     [状态]
     * @param  [type] $apiDeposit [回传双金]
     * @param  [type] $apiService [回传手续费]
     * @return [type]             [description]
     */
    public function agreeConsult(Request $request)
    {
        DB::beginTransaction();
    	try {
            $orderNo = $request->orderNo;
            $apiDeposit = $request->apiDeposit;
            $apiService = $request->apiService;
            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} elseif (!$apiDeposit) {
    			return json_encode([
    				'status' => 0,
    				'message' => '回传双金之和缺失',
    			]);
    		} elseif (!$apiService) {
    			return json_encode([
    				'status' => 0,
    				'message' => '回传手续费缺失',
    			]);
    		} else {
    			if (! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
    				return json_encode([
	    				'status' => 0,
	    				'message' => '回传双金和手续费必须是数字',
	    			]);
    			}
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			$data = [
					'api_deposit' => $apiDeposit,
					'api_service' => $apiService,
					'complete' => 1,
    			];
    			LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);

    			DailianFactory::choose('agreeRevoke')->run($order->no, $order->gainer_primary_user_id, 0);
                // 手续费写到order_detail中
                OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $no)
                ->update(['field_value' => $apiService]); 			
    		}
    	} catch (Exception $e) {
            DB::rollBack();
    		throw new Exception($e->getMessage());
            return json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
    	}
        DB::commit();
        return json_encode([
            'status' => 1,
            'message' => '已同意撤销',
        ]);
    }

   /**
    * 同意申诉
    * @param  [type] $orderNo    [订单号]
    * @param  [type] $status     [状态]
    * @param  [type] $apiAmount  [回传代练费]
    * @param  [type] $apiDeposit [回传双金之和]
    * @param  [type] $apiService [回传手续费]
    * @return [type]             [description]
    */
    public function agreeAppeal(Request $request)
    {
        DB::beginTransaction();
    	try {
            $orderNo = $request->orderNo;
            $apiAmount = $request->apiAmount;
            $apiDeposit = $request->apiDeposit;
            $apiService = $request->apiService;
            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} elseif (!$apiAmount) {
    			return json_encode([
    				'status' => 0,
    				'message' => '回传代练费缺失',
    			]);
    		} elseif (!$apiDeposit) {
    			return json_encode([
    				'status' => 0,
    				'message' => '回传双金之和缺失',
    			]);
    		} elseif (!$apiService) {
    			return json_encode([
    				'status' => 0,
    				'message' => '回传手续费缺失',
    			]);
    		} else {
    			if (! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
    				return json_encode([
	    				'status' => 0,
	    				'message' => '回传双金和手续费必须是数字',
	    			]);
    			}
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			$data = [
					'api_amount' => $apiAmount,
					'api_deposit' => $apiDeposit,
					'api_service' => $apiService,
					'complete' => 1,
    			];
                // 更新代练协商申诉表
    			LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
                // 同意申诉
                DailianFactory::choose('arbitration')->run($order->no, $order->gainer_primary_user_id, 0);
                // 手续费写到order_detail中
                OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $no)
                ->update(['field_value' => $apiService]);

    		}
    	} catch (Exception $e) {
            DB::rollBack();
    		throw new Exception($e->getMessage());
            return json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
    	}
        DB::commit();
        return json_encode([
            'status' => 1,
            'message' => '已同意申诉',
        ]);
    }

    /**
     * 协商
     * @param  [type] $orderNo [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function consult(Request $request)
    {
        DB::beginTransaction();
    	try {
            $orderNo = $request->orderNo;
            $apiAmount = $request->apiAmount;
            $apiDeposit = $request->apiDeposit;
            $content = $request->content;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} elseif(!$content) {
    			return json_encode([
    				'status' => 0,
    				'message' => '协商原因缺失',
    			]);
    		} else {
    			if (!is_numeric($apiAmount)) {
    				return json_encode([
	    				'status' => 0,
	    				'message' => '代练费必须是数字',
	    			]);
    			}

    			if (!is_numeric($apiDeposit)) {
    				return json_encode([
	    				'status' => 0,
	    				'message' => '双金必须是数字',
	    			]);
    			}

    			$orderDetail = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->first(); 

    			$order = Order::where('no', $orderDetail->order_no)->first();

    			$safeDeposit = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
	            $effectDeposit = $order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
	            $orderDeposit = bcadd($safeDeposit, $effectDeposit);
	            $isOverDeposit = bcsub($orderDeposit, $apiDeposit);
	            $isOverAmount = bcsub($order->amount, $apiAmount);
	            // 写入双金与订单双击比较
	            if ($isOverDeposit < 0) {
	                return json_encode([
		    			'status' => 0,
		    			'message' => '写入双金超过订单代练双金',
		    		]);
	            }
	            // 写入代练费与订单代练费比较
	            if ($isOverAmount < 0) {
	                return json_encode([
		    			'status' => 0,
		    			'message' => '写入代练费超过订单代练费',
		    		]);
	            }

    			$data = [
					'user_id' => $order->gainer_primary_user_id,
    				'order_no' => $order->no,
    				'amount' => $apiAmount,
    				'deposit' => $apiDeposit,
    				'consult' => 2,
    				'revoke_message' => $content,
    			];

                LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);

    			DailianFactory::choose('revoke')->run($order->no, $order->gainer_primary_user_id, 0);	
    		}
    	} catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    	}
        DB::commit();

        return json_encode([
            'status' => 1,
            'message' => '已申请协商',
        ]);
    }

    /**
     * 申诉
     * @param  [type] $orderNo [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function appeal(Request $request)
    {
    	DB::beginTransaction();
    	try {
            $orderNo = $request->orderNo;
            $content = $request->content;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} elseif (!$content) {
    			return json_encode([
    				'status' => 0,
    				'message' => '申诉原因缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			$data = [
					'user_id' => $order->gainer_primary_user_id,
    				'complain' => 2,
    				'complain_message' => $content,
    			];

    			LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);

    			DailianFactory::choose('applyArbitration')->run($order->no, $order->gainer_primary_user_id, 0);
    		}
    	} catch (Exception $e) {
    		DB::rollBack();
    		throw new Exception($e->getMessage());
            return json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
    	}
    	DB::commit();
        return json_encode([
            'status' => 1,
            'message' => '已申请申诉',
        ]);
    }

    /**
     * 取消协商
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function cancelConsult(Request $request)
    {
        DB::beginTransaction();
    	try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

                // 会变成锁定
                DailianFactory::choose('cancelRevoke')->run($order->no, $order->gainer_primary_user_id, 0);
                // 91的要解除锁定
    			DailianFactory::choose('cancelLock')->run($order->no, $order->gainer_primary_user_id);
    		}
    	} catch (Exception $e) {
            DB::rollBack();
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    		throw new Exception($e->getMessage());
    	}
        DB::commit();
        return json_encode([
            'status' => 1,
            'message' => '已取消协商',
        ]);
    }

     /**
     * 取消申诉
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function cancelAppeal(Request $request)
    {
    	try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

                DailianFactory::choose('cancelArbitration')->run($order->no, $order->gainer_primary_user_id, 0);
                DailianFactory::choose('cancelRevoke')->run($order->no, $order->gainer_primary_user_id, 0);
    			DailianFactory::choose('cancelLock')->run($order->no, $order->gainer_primary_user_id, 0);

    			return json_encode([
    				'status' => 1,
    				'message' => '已取消申诉',
    			]);
    		}
    	} catch (Exception $e) {
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    		throw new Exception($e->getMessage());
    	}
    }


    /**
     * 强制同意协商
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function forceConsult(Request $request)
    {
    	try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			DailianFactory::choose('forceRevoke')->run($order->no, $order->gainer_primary_user_id);

    			return json_encode([
    				'status' => 1,
    				'message' => '已强制协商',
    			]);
    		}
    	} catch (Exception $e) {
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    		throw new Exception($e->getMessage());
    	}
    }

    /**
     * 异常
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function unusualOrder(Request $request)
    {
    	try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			DailianFactory::choose('abnormal')->run($order->no, $order->gainer_primary_user_id);

    			return json_encode([
    				'status' => 1,
    				'message' => '已将订单标记为异常',
    			]);
    		}
    	} catch (Exception $e) {
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    		throw new Exception($e->getMessage());
    	}
    }


    /**
     * 取消异常
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function cancelUnusual(Request $request)
    {
    	try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			DailianFactory::choose('cancelAbnormal')->run($order->no, $order->gainer_primary_user_id);

    			return json_encode([
    				'status' => 1,
    				'message' => '已取消异常订单',
    			]);
    		}
    	} catch (Exception $e) {
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    		throw new Exception($e->getMessage());
    	}
    }

    /**
     * 申请验收
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function applyComplete(Request $request)
    {
    	try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

    		if (!$orderNo) {
    			return json_encode([
    				'status' => 0,
    				'message' => '订单号缺失',
    			]);
    		} else {
    			$no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
    			$order = Order::where('no', $no)->first();

    			DailianFactory::choose('applyComplete')->run($order->no, $order->gainer_primary_user_id);

    			return json_encode([
    				'status' => 1,
    				'message' => '已申请验收',
    			]);
    		}
    	} catch (Exception $e) {
    		return json_encode([
    			'status' => 0,
    			'message' => $e->getMessage(),
    		]);
    		throw new Exception($e->getMessage());
    	}
    }

    /**
     * 取消验收
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function cancelComplete(Request $request)
    {
        try {
            $orderNo = $request->orderNo;

            $this->checkSign($request->sign);

            if (!$orderNo) {
                return json_encode([
                    'status' => 0,
                    'message' => '订单号缺失',
                ]);
            } else {
                $no = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->value('order_no'); 
                $order = Order::where('no', $no)->first();

                DailianFactory::choose('cancelComplete')->run($order->no, $order->gainer_primary_user_id);

                return json_encode([
                    'status' => 1,
                    'message' => '已取消验收',
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                'status' => 0,
                'message' => $e->getMessage(),
            ]);
            throw new Exception($e->getMessage());
        }
    }
}