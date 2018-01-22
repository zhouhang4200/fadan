<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\OrderNotice;
use App\Services\Show91;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Exceptions\OrderNoticeException;

class LevelingController
{
    protected $sign = 'a46ae5de453bfaadc8548a3e48c151db';

    /**
     * 91平台在千手的用户ID
     * @var int
     */
    protected $userId = 8456;

    /**
     * LevelingController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        myLog('91request', [$request->all(), $request->url(), $request->header('Content-Type')]);
    }


    public function checkSignAndOrderNo($sign, $orderNo)
    {
        if ($sign != $this->sign) {
            throw new DailianException('验证失败');
        }
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

    public function success($message)
    {
        return json_encode([
            'status' => 1,
            'message' => $message,
        ]);
    }

    public function fail($message, $order)
    {
        // 异常写入order_notices 表
        $this->addOrderNotice($order);

        return json_encode([
            'status' => 0,
            'message' => $message,
        ]);
        \Log::info($message);
        throw new DailianException($message);
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

			DailianFactory::choose('receive')->run($order->no, $this->userId);

			return $this->success('接单成功');
    	} catch (DailianException $e) {
            return $this->fail($e->getMessage(), $order);
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

            DailianFactory::choose('agreeRevoke')->run($order->no, $this->userId, 0);
            // 手续费写到order_detail中
            OrderDetail::where('field_name', 'poundage')
                ->where('order_no', $order->no)
                ->update(['field_value' => $apiService]);

    	} catch (DailianException $e) {
            DB::rollBack();
            return $this->fail($e->getMessage(), $order);
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
            $content = $request->content ?: '无';
            $apiService = $request->apiService;

            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			if (! is_numeric($apiAmount) || ! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
                throw new DailianException('代练费和双金或手续费必须是数字');
			}

			if ($apiAmount < 0 || $apiDeposit < 0 || $apiService < 0) {
                throw new DailianException('代练费和双金或手续费必须大于0');
			}

            $safeDeposit = $order->detail()->where('field_name', 'security_deposit')->value('field_value');
            $effectDeposit = $order->detail()->where('field_name', 'efficiency_deposit')->value('field_value');
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

            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
			DailianFactory::choose('revoke')->run($order->no, $this->userId, 0);

    	} catch (DailianException $e) {
            DB::rollBack();
            return $this->fail($e->getMessage(), $order);
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
        try {
            DB::beginTransaction();
            try {
                myLog('exception-appeal', ['进入']);
                $content = $request->input('content', '无');

                $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

                $data = [
                    'user_id' => $this->userId,
                    'complain' => 2,
                    'complain_message' => $content,
                ];

                $result  = LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
                myLog('appeal', ['user' => $this->userId, 'message' => $content, 'no' => $order->no, 'result' => $result]);
                DailianFactory::choose('applyArbitration')->run($order->no, $this->userId, 0);

            } catch (DailianException $e) {
                DB::rollBack();
                myLog('exception-appeal', [$e->getMessage()]);
                return $this->fail($e->getMessage(), $order);
            }
            DB::commit();
            return $this->success('已申请申诉');
        } catch (\Exception $exception) {
            myLog('exception-appeal', [$exception->getMessage()]);
        }
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
            DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, 0);
            // 91的要解除锁定
			// DailianFactory::choose('cancelLock')->run($order->no, $this->userId);

    	} catch (DailianException $e) {
            DB::rollBack();
    		return $this->fail($e->getMessage(), $order);
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

            DailianFactory::choose('cancelArbitration')->run($order->no, $this->userId, 0);
            // DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, 0);
			// DailianFactory::choose('cancelLock')->run($order->no, $this->userId, 0);

            return $this->success('已取消申诉');
    	} catch (DailianException $e) {
    		return $this->fail($e->getMessage(), $order);
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

			DailianFactory::choose('forceRevoke')->run($order->no, $this->userId);

            return $this->success('已强制协商');
    	} catch (DailianException $e) {
    		return $this->fail($e->getMessage(), $order);
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

			DailianFactory::choose('abnormal')->run($order->no, $this->userId);

            return $this->success('已将订单标记为异常');
    	} catch (DailianException $e) {
    		return $this->fail($e->getMessage(), $order);
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

			DailianFactory::choose('cancelAbnormal')->run($order->no, $this->userId);

            return $this->success('已取消异常订单');
    	} catch (DailianException $e) {
    		return $this->fail($e->getMessage(), $order);
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

			DailianFactory::choose('applyComplete')->run($order->no, $this->userId);

            return $this->success('已申请验收');
    	} catch (DailianException $e) {
    		return $this->fail($e->getMessage(), $order);
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

            DailianFactory::choose('cancelComplete')->run($order->no, $this->userId);

            return $this->success('已取消验收');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage(), $order);
        }
    }

    public function addOrderNotice($order)
    {
        DB::beginTransaction();
        try {
            $data = [];
            $data['creator_user_id'] = $order->creator_user_id;
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;
            $data['gainer_user_id'] = $order->gainer_user_id;
            $data['creator_user_name'] = $order->creatorUser->name;
            $data['order_no'] = $order->no;
            $data['third_order_no'] = $order->detail()->where('field_name', 'third_order_no')->value('field_value');
            $data['third'] = $order->detail()->where('field_name', 'third')->value('field_value');
            $data['status'] = $order->status;
            $data['third_status'] = $this->getThirdOrderStatus($data['third_order_no']);
            $data['create_order_time'] = $order->created_at;
            $data['complete'] = 0;

            OrderNotice::updateOrCreate(['order_no' => $order->no], $data);
        } catch (OrderNoticeException $e) {
            DB::rollback();
            myLog('order-notice-e', [$e->getMessage()]);
        }
        DB::commit();
        return true;
    }

    public function getThirdOrderStatus($orderNo)
    {
        if (! $orderNo) {
            throw new OrderNoticeException('第三方订单号不存在');
        }

        $options = [
            'oid' => $orderNo,
        ]; 

        $res = Show91::orderDetail($options);

        return $res['data']['order_status'];
    }
}
