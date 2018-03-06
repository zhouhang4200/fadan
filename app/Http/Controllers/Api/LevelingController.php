<?php

namespace App\Http\Controllers\Api;

use DB;
use Redis;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\OrderNotice;
use App\Services\Show91;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException;
use App\Extensions\Dailian\Controllers\DailianFactory;
use App\Exceptions\OrderNoticeException;

/**
 * 我们提供给91的代练接口
 */
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

    /**
     * 检查签名和订单号
     * @param  [type] $sign    [description]
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
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

    /**
     * 成功信息
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function success($message, $order)
    {
        if ($order) {
            $action = \Route::currentRouteAction();
            $this->checkAndAddOrderToRedis($order, '1-1-'.$action);
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
            $this->checkAndAddOrderToRedis($order, '0-1-'.$action);
        }

        return json_encode([
            'status' => 0,
            'message' => $message,
        ]);
        \Log::info($message);
        throw new DailianException($message);
    }

	/**
	 *     接单操作
	 * @param  [type] $orderNo [description]
	 * @param  [type] $status  [description]
	 * @return [type]          [description]
	 */
    public function receiveOrder(Request $request)
    {
        $order = null;
    	try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('receive')->run($order->no, $this->userId);

			return $this->success('接单成功', $order);
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
        $order = null;
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
        return $this->success('已同意撤销', $order);
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
        $order = null;
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
        return $this->success('已同意申诉', $order);
    }

    /**
     * 协商
     * @param  [type] $orderNo [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function consult(Request $request)
    {
        $order = null;
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
            // 更新协商信息到协商表
            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
            // 调用工厂模式协商操作
			DailianFactory::choose('revoke')->run($order->no, $this->userId, 0);

    	} catch (DailianException $e) {
            DB::rollBack();
            return $this->fail($e->getMessage(), $order);
    	}
        DB::commit();
        return $this->success('已申请协商', $order);
    }

    /**
     * 申诉
     * @param  [type] $orderNo [description]
     * @param  [type] $status  [description]
     * @return [type]          [description]
     */
    public function appeal(Request $request)
    {
        $order = null;
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
            return $this->success('已申请申诉', $order);
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
        $order = null;
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
        return $this->success('已取消协商', $order);
    }

     /**
     * 取消申诉
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function cancelAppeal(Request $request)
    {
        $order = null;
    	try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

            DailianFactory::choose('cancelArbitration')->run($order->no, $this->userId, 0);
            // DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, 0);
			// DailianFactory::choose('cancelLock')->run($order->no, $this->userId, 0);

            return $this->success('已取消申诉', $order);
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
        $order = null;
    	try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('forceRevoke')->run($order->no, $this->userId);

            return $this->success('已强制协商', $order);
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
        $order = null;
    	try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('abnormal')->run($order->no, $this->userId);

            return $this->success('已将订单标记为异常', $order);
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
        $order = null;
    	try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('cancelAbnormal')->run($order->no, $this->userId);

            return $this->success('已取消异常订单', $order);
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
        $order = null;
    	try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

			DailianFactory::choose('applyComplete')->run($order->no, $this->userId);

            return $this->success('已申请验收', $order);
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
        $order = null;
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->orderNo);

            DailianFactory::choose('cancelComplete')->run($order->no, $this->userId);

            return $this->success('已取消验收', $order);
        } catch (DailianException $e) {
            return $this->fail($e->getMessage(), $order);
        }
    }

    /**
     * 添加订单报警
     * @param [type] $order [description]
     */
    public function addOrderNotice($order, $bool = false)
    {
        DB::beginTransaction();
        try {
            $orderDetail = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();
            $data = [];
            $data['creator_user_id'] = $order->creator_user_id;
            $data['creator_primary_user_id'] = $order->creator_primary_user_id;
            $data['gainer_user_id'] = $order->gainer_user_id;
            $data['creator_user_name'] = $order->creatorUser->name;
            $data['order_no'] = $order->no;
            $data['third_order_no'] = $orderDetail['third_order_no'];
            $data['third'] = $orderDetail['third'];
            $data['status'] = $order->status;
            $data['create_order_time'] = $order->created_at;
            $data['complete'] = 0;
            $data['amount'] = $order->amount;
            $data['security_deposit'] = $orderDetail['security_deposit'];
            $data['efficiency_deposit'] = $orderDetail['efficiency_deposit'];
            $twoStatus = $this->getThirdOrderStatus($data['third_order_no']);
            // 操作
            $action = \Route::currentRouteAction();
            $actionName = preg_replace('~.*@~', '', $action, -1);
            if ($actionName) {
                if ($bool) {
                    $data['operate'] = config('ordernotice.operate')[$actionName] ? config('ordernotice.operate')[$actionName].'@' : '';
                } else {
                    $data['operate'] = config('ordernotice.operate')[$actionName] ?: '';
                }
            } else {
                $data['operate'] = '';
            }
            if (count($twoStatus) == 2) {
                $data['third_status'] = $twoStatus[0];
                $data['child_third_status'] = $twoStatus[1];
            } else {
                $data['third_status'] = $twoStatus;
                $data['child_third_status'] = 100;
            }

            OrderNotice::create($data);
        } catch (OrderNoticeException $e) {
            DB::rollback();
            myLog('order-notice-e', [$e->getMessage()]);
        }
        DB::commit();
        return true;
    }

    /**
     * 获取第三方平台状态和子状态
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function getThirdOrderStatus($orderNo)
    {
        if (! $orderNo) {
            throw new OrderNoticeException('第三方订单号不存在');
        }

        $options = [
            'oid' => $orderNo,
        ]; 
        sleep(3);
        $res = Show91::orderDetail($options);
        // 91平台订单状态
        $thirdStatus =  $res['data']['order_status'];

        // 如果状态为代练中，需要详细区分到底是哪个状态
        // 此处有可能同时存在，会有分不清情况出现
        if ($res['data']['inAppeal'] && empty($res['data']['inSelfCancel'])) {
            $childThirdStatus = 14; // 申诉中
        } elseif ($res['data']['inSelfCancel'] && empty($res['data']['inAppeal'])) {
            $childThirdStatus = 13; // 协商中
        } elseif ($res['data']['inSelfCancel'] && $res['data']['inAppeal']) {
            $childThirdStatus = 15;
        }

        if (isset($childThirdStatus)) {
            return [$thirdStatus, $childThirdStatus];
        }
        return $thirdStatus;
    }

    /**
     * 检查order_notices 表订单状态，一样的话不走处理，不一样再生成一条报警
     */
    public function checkOrderNotice($order)
    {
        $orderDetail = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();

        if (! $orderDetail['third_order_no']) {
            throw new OrderNoticeException('第三方订单号不存在');
        }

        $options = [
            'oid' => $orderDetail['third_order_no'],
        ]; 

        $res = Show91::orderDetail($options);
        // 91平台订单状态
        $thirdStatus =  $res['data']['order_status'];

        switch ($thirdStatus) {
            case 1:
                if ($order->status != 13) {
                    $this->addOrderNotice($order, true);
                }
                return true;
            break;
            case 2:
                if ($order->status != 14) {
                    $this->addOrderNotice($order, true); 
                }
                return true;
            break;
            default:
                return true;
        }
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
}
