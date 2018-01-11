<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException as Exception;
use App\Extensions\Dailian\Controllers\DailianFactory;

class LevelingController
{	
    protected $sign = 'a46ae5de453bfaadc8548a3e48c151db';

    public function checkSignAndOrderNo($sign, $orderNo)
    {
        if ($sign != $this->sign) {
            throw new Exception('验证失败');
        }
        $orderDetail = OrderDetail::where('field_name', 'third_order_no')->where('field_value', $orderNo)->first();

        if (! $orderDetail) {
            throw new Exception('订单号缺失或错误');   
        } else {
            $order = Order::where('no', $orderDetail->order_no)->first();

            if (! $order) {
                throw new Exception('内部订单号缺失,请联系我们');   
            }
            return $order;
        }
    }

    public function success($message)
    {
        return json_encode([
            'status' => 1,
            'message' => $message,
        ]);
    }

    public function fail($message)
    {
         return json_encode([
            'status' => 0,
            'message' => $message,
        ]);
        throw new Exception($e->getMessage());
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('receive')->run($order->no, 29, 0);

			return $this->success('发接单');
    	} catch (Exception $e) {
            return $this->fail($e->getMessage());
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
            $apiDeposit = $request->apiDeposit;
            $apiService = $request->apiService;
            
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			if (! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
                throw new Exception('回传双金和手续费必须是数字');
			}

            if ($apiDeposit < 0 || $apiService < 0) {
                throw new Exception('回传双金和手续费必须大于或等于0');
            }

			$data = [
				'api_deposit' => $apiDeposit,
				'api_service' => $apiService,
				'complete' => 1,
			];
  
            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);

            DailianFactory::choose('agreeRevoke')->run($order->no, 29, 0);
            // 手续费写到order_detail中
            OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $order->no)
                ->update(['field_value' => $apiService]); 			

    	} catch (Exception $e) {
            DB::rollBack();
            return $this->fail($e->getMessage());
    	}
        DB::commit();
        return $this->success('已同意撤销');
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
            $apiAmount = $request->apiAmount;
            $apiDeposit = $request->apiDeposit;
            $apiService = $request->apiService;
            
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			if (! is_numeric($apiDeposit) || ! is_numeric($apiService) || ! is_numeric($apiAmount)) {
                throw new Exception('回传双金、手续费和代练费必须是数字');
			}

            if ($apiDeposit < 0 || $apiService < 0 || $apiAmount < 0) {
                throw new Exception('回传双金、手续费和代练费必须大于等于0');
            }

			$data = [
				'api_amount' => $apiAmount,
				'api_deposit' => $apiDeposit,
				'api_service' => $apiService,
				'complete' => 1,
			];
            // 更新代练协商申诉表
			LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
            // 同意申诉
            DailianFactory::choose('arbitration')->run($order->no, 29, 0);
            // 手续费写到order_detail中
            OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $order->no)
                ->update(['field_value' => $apiService]);

    	} catch (Exception $e) {
            DB::rollBack();
            return $this->fail($e->getMessage());
    	}
        DB::commit();
        return $this->success('已同意申诉');
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
            $apiAmount = $request->apiAmount;
            $apiDeposit = $request->apiDeposit;
            $content = $request->content ?? '无';
            
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

            if(! $content || strlen($content) > 200 || strlen($content) < 1) {
                throw new Exception('协商原因为字符串且长度不超过200');
    		} else {
    			if (! is_numeric($apiAmount) || ! is_numeric($apiDeposit)) {
                    throw new Exception('代练费和双金必须是数字');
    			}

    			if ($apiAmount < 0 || $apiDeposit < 0) {
                    throw new Exception('代练费和双金必须大于0');
    			}

                $safeDeposit = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
                $effectDeposit = $order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
                $orderDeposit = bcadd($safeDeposit, $effectDeposit);
                $isOverDeposit = bcsub($orderDeposit, $apiDeposit);
                $isOverAmount = bcsub($order->amount, $apiAmount);
                // 写入双金与订单双击比较
                if ($isOverDeposit < 0) {
                    throw new Exception('传入双金超过订单代练双金');
                }
                // 写入代练费与订单代练费比较
                if ($isOverAmount < 0) {
                    throw new Exception('传入代练费超过订单代练费');
                }

                $data = [
                    'user_id' => 29,
                    'order_no' => $order->no,
                    'amount' => $apiAmount,
                    'deposit' => $apiDeposit,
                    'consult' => 2,
                    'revoke_message' => $content,
                ];

                LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
    			DailianFactory::choose('revoke')->run($order->no, 29, 0);	
    		}
    	} catch (Exception $e) {
            DB::rollBack();
            return $this->fail($e->getMessage());
    	}
        DB::commit();
        return $this->success('已申请协商');
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
            $content = $request->content ?? '';
            
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

    		if (! $content || strlen($content) > 200 || strlen($content) < 1) {
                throw new Exception('申诉原因为字符串且长度不超过200');
    		} else {
    			$data = [
					'user_id' => 29,
    				'complain' => 2,
    				'complain_message' => $content,
    			];

    			LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
    			DailianFactory::choose('applyArbitration')->run($order->no, 29, 0);
    		}
    	} catch (Exception $e) {
    		DB::rollBack();
            return $this->fail($e->getMessage());
    	}
    	DB::commit();
        return $this->success('已申请申诉');
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

            // 会变成锁定
            DailianFactory::choose('cancelRevoke')->run($order->no, 29, 0);
            // 91的要解除锁定
			DailianFactory::choose('cancelLock')->run($order->no, 29);

    	} catch (Exception $e) {
            DB::rollBack();
    		return $this->fail($e->getMessage());
    	}
        DB::commit();
        return $this->success('已取消协商');
    }

     /**
     * 取消申诉
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function cancelAppeal(Request $request)
    {
    	try {    
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

            DailianFactory::choose('cancelArbitration')->run($order->no, 29, 0);
            DailianFactory::choose('cancelRevoke')->run($order->no, 29, 0);
			DailianFactory::choose('cancelLock')->run($order->no, 29, 0);

            return $this->success('已取消申诉');
    	} catch (Exception $e) {
    		return $this->fail($e->getMessage());
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('forceRevoke')->run($order->no, 29);

            return $this->success('已强制协商');
    	} catch (Exception $e) {
    		return $this->fail($e->getMessage());
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('abnormal')->run($order->no, 29);

            return $this->success('已将订单标记为异常');
    	} catch (Exception $e) {
    		return $this->fail($e->getMessage());
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('cancelAbnormal')->run($order->no, 29);

            return $this->success('已取消异常订单');
    	} catch (Exception $e) {
    		return $this->fail($e->getMessage());
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('applyComplete')->run($order->no, 29);

            return $this->success('已申请验收');
    	} catch (Exception $e) {
    		return $this->fail($e->getMessage());
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
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

            DailianFactory::choose('cancelComplete')->run($order->no, 29);

            return $this->success('已取消验收');
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
