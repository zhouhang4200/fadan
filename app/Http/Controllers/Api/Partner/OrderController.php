<?php
namespace App\Http\Controllers\Api\Partner;

use Order, DB, Exception;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\LevelingConsult;
use App\Models\Order as OrderModel;
use App\Http\Controllers\Controller;
use App\Exceptions\DailianException;
use App\Extensions\Dailian\Controllers\DailianFactory;

/**
 * Class OrderController
 * @package App\Http\Controllers\Api\Partner
 */
class OrderController extends Controller
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
        $orderDetail = OrderDetail::where('field_name', 'show91_order_no')->where('field_value', $orderNo)->first();

        if (! $orderDetail) {
            throw new DailianException('订单号缺失或错误');
        } else {
            $order = OrderModel::where('no', $orderDetail->order_no)->first();

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
    public function success($message)
    {
        return json_encode([
            'status'  => 1,
            'message' => $message,
            'data'    => '',
        ]);
    }

    /**
     * 失败信息
     * @param  [type] $message [description]
     * @param  [type] $order   [description]
     * @return [type]          [description]
     */
    public function fail($message)
    {
        return json_encode([
            'status' => 0,
            'message' => $message,
        ]);
    }

    /**
     * 查询订单信息
     * @param Request $request
     */
    public function query(Request $request)
    {
    }

    /**接单
     * @param Request $request
     */
    public function receive(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('receive')->run($order->no, $this->userId, true);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 申请验收
     * @param Request $request
     */
    public function applyComplete(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('applyComplete')->run($order->no, $this->userId);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 取消验收
     * @param Request $request
     */
    public function cancelComplete(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('cancelComplete')->run($order->no, $this->userId);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 撤销
     * @param Request $request
     */
    public function revoke(Request $request)
    {
        DB::beginTransaction();
        try {
            // 接口传过来的参数
            $apiAmount  = $request->api_amount;  // 回传代练费
            $apiDeposit = $request->api_deposit; // 回传双金
            $apiService = $request->api_service; // 回传手续费
            $content    = $request->input('content', '无'); // 回传的撤销说明
            // 判断传入的金额是否合法
            if (! is_numeric($apiAmount) || ! is_numeric($apiDeposit) || ! is_numeric($apiService)) {
                throw new DailianException('代练费和双金或手续费必须是数字');
            }

            if ($apiAmount < 0 || $apiDeposit < 0 || $apiService < 0) {
                throw new DailianException('代练费和双金或手续费必须大于0');
            }
            // 订单信息
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);
            // 订单详情
            $orderDetails = OrderDetail::where('order_no', $order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();
            // 写入撤销记录
            $safeDeposit   = $orderDetails['security_deposit'];
            $effectDeposit = $orderDetails['efficiency_deposit'];
            $orderDeposit  = bcadd($safeDeposit, $effectDeposit);
            $isOverDeposit = bcsub($orderDeposit, $apiDeposit);
            $isOverAmount  = bcsub($order->amount, $apiAmount);
            // 写入双金与订单双击比较
            if ($isOverDeposit < 0) {
                throw new DailianException('传入双金超过订单代练双金');
            }
            // 写入代练费与订单代练费比较
            if ($isOverAmount < 0) {
                throw new DailianException('传入代练费超过订单代练费');
            }
            // 数据
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
            // 更新协商信息到协商表
            LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);
            // 调用工厂模式协商操作
            DailianFactory::choose('revoke')->run($order->no, $this->userId, false);
        } catch (DailianException $e) {
            DB::rollBack();
            return $this->fail($e->getMessage());
        }
        DB::commit();
        return $this->success('成功');
    }

    /**
     * 取消撤销
     * @param Request $request
     */
    public function cancelRevoke(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);
            // 会变成锁定
            DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, false);
        } catch (DailianException $e) {
            DB::rollBack();
            return $this->fail($e->getMessage());
        }
        DB::commit();
        return $this->success('成功');
    }

    /**
     * 不同意撤销
     * @param Request $request
     */
    public function refuseRevoke(Request $request)
    {
        DB::beginTransaction();
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);
            // 会变成锁定
            DailianFactory::choose('cancelRevoke')->run($order->no, $this->userId, false);
            // 取消撤销，将之前产生的撤销记录清除
            // LevelingConsult::where('order_no', $order->no)->update(['consult' => 0]);
        } catch (DailianException $e) {
            DB::rollBack();
            return $this->fail($e->getMessage());
        }
        DB::commit();
        return $this->success('成功');
    }

    /**
     * 同意撤销
     * @param Request $request
     */
    public function agreeRevoke(Request $request)
    {
        DB::beginTransaction();
        try {
            $apiDeposit = $request->api_deposit;
            $apiService = $request->api_service;

            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);
             // 订单详情
            $orderDetails = OrderDetail::where('order_no', $order->no)
                ->pluck('field_value', 'field_name')
                ->toArray();
            // 写入撤销记录
            $safeDeposit   = $orderDetails['security_deposit'];
            $effectDeposit = $orderDetails['efficiency_deposit'];
            $orderDeposit  = bcadd($safeDeposit, $effectDeposit);
            $isOverDeposit = bcsub($orderDeposit, $apiDeposit);
            // 写入双金与订单双击比较
            if ($isOverDeposit < 0) {
                throw new DailianException('传入双金超过订单代练双金');
            }

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
            return $this->fail($e->getMessage());
        }
        DB::commit();
        return $this->success('成功');
    }

    /**
     * 强制撤销
     * @param Request $request
     */
    public function forceRevoke(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('forceRevoke')->run($order->no, $this->userId);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 申请仲裁
     * @param Request $request
     */
    public function applyArbitration(Request $request)
    {
        try {
            DB::beginTransaction();
            try {
                $content = $request->input('content', '无');

                $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

                $data = [
                    'user_id' => $this->userId,
                    'complain' => 2,
                    'complain_message' => $content,
                ];

                $result  = LevelingConsult::updateOrCreate(['order_no' => $order->no], $data);

                myLog('appeal', ['user' => $this->userId, 'message' => $content, 'no' => $order->no, 'result' => $result]);

                DailianFactory::choose('applyArbitration')->run($order->no, $this->userId, false);
            } catch (DailianException $e) {
                DB::rollBack();
                myLog('exception-appeal', [$e->getMessage()]);
                return $this->fail($e->getMessage());
            }
            DB::commit();
            return $this->success('成功');
        } catch (\Exception $exception) {
            myLog('exception-appeal', [$exception->getMessage()]);
        }
    }

    /**
     * 取消仲裁
     * @param Request $request
     */
    public function cancelArbitration(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('cancelArbitration')->run($order->no, $this->userId, false);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 强制仲裁
     * @param Request $request
     */
    public function forceArbitration(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('arbitration')->run($order->no, $this->userId, false);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 异常
     * @param Request $request
     */
    public function abnormal(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('abnormal')->run($order->no, $this->userId, false);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 取消异常
     * @param Request $request
     */
    public function cancelAbnormal(Request $request)
    {
        try {
            $order = $this->checkSignAndOrderNo($request->sign, $request->order_no);

            DailianFactory::choose('cancelAbnormal')->run($order->no, $this->userId, false);

            return $this->success('成功');
        } catch (DailianException $e) {
            return $this->fail($e->getMessage());
        }
    }
}
