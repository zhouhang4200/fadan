<?php

namespace App\Console\Commands;

use DB;
use Redis;
use App\Models\Order;
use App\Services\Show91;
use App\Models\OrderNotice;
use App\Models\OrderDetail;
use App\Models\OrderHistory;
use Illuminate\Console\Command;

class AddNoticeOrderFromRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每分钟将报警订单写到订单报警表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderNos = Redis::hGetAll('notice_orders');

        // 操作成功状态为1， 操作失败状态为0
        foreach ($orderNos as $orderNo => $statusAndAction) {
            $status = explode('-', $statusAndAction)[0];
            $third = explode('-', $statusAndAction)[1];
            $action = explode('-', $statusAndAction)[2];
            $this->checkOrderNotice($orderNo, $status, $action, $third);
        }
    }

     /**
     * 获取第三方平台状态和子状态
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function getThirdOrderStatus($orderNo)
    {
        if (! $orderNo) {
            throw new Exception('第三方订单号不存在');
        }

        $options = [
            'oid' => $orderNo,
        ]; 
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
    public function checkOrderNotice($orderNo, $status, $action, $third)
    {
        DB::beginTransaction();
        try {
            $this->delRedisNoticeOrder($orderNo);
            $order = Order::where('no', $orderNo)->first();
            if ($order) {
                // 获取游戏详情，看是哪个平台的订单
                $orderDetail = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();

                switch ($third) {
                    case 1:
                        if ($status == 1) { // 第三方做了成功的操作
                            if (! $orderDetail['third_order_no']) {
                                throw new Exception('第三方订单号不存在');
                            }

                            $options = [
                                'oid' => $orderDetail['third_order_no'],
                            ]; 

                            $res = Show91::orderDetail($options);
                            // 91平台订单状态
                            $thirdStatus =  $res['data']['order_status'];
                            $thirdConsult = $res['data']['inSelfCancel'] ? 13 : false;
                            $thirdComplain = $res['data']['inAppeal'] ? 14 : false;

                            $beforeStatus = $this->getBeforeStatus($orderNo);

                            switch ($thirdStatus) {
                                case 1: // 91平台状态，代练中
                                    if (!$thirdComplain && !$thirdConsult && $order->status == 13) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } elseif ($thirdComplain && !$thirdConsult && $beforeStatus == 13 && $order->status == 16) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } elseif (!$thirdComplain && $thirdConsult && $beforeStatus == 13 && $order->status == 15) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action);
                                    }
                                break;
                                case 2: // 91平台代练状态，待验收
                                    if (!$thirdComplain && !$thirdConsult && $order->status == 14) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } elseif ($thirdComplain && !$thirdConsult && $beforeStatus == 14 && $order->status == 16) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } elseif (!$thirdComplain && $thirdConsult && $beforeStatus == 14 && $order->status == 15) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action);
                                    }
                                break;
                                default:
                                    return true;
                            }
                        } else {
                            $this->addOrderNotice($order, $status, $action);
                        }
                        break;
                    case 2:
                        break;
                    default:
                        # code...
                        break;
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-notice-e', [$e->getMessage()]);
        }
        DB::commit();
    }

    /**
     * 添加订单报警
     * @param [type] $order [description]
     */
    public function addOrderNotice($order, $status, $action)
    {
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
        // $action = \Route::currentRouteAction();
        $actionName = preg_replace('~.*@~', '', $action, -1);
        if ($actionName) {
            if ($status) {
                $data['operate'] = config('ordernotice.operate')[$actionName].'@' ?: '';
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
            $data['child_third_status'] = 100; // 表示没有子状态
        }
        OrderNotice::create($data);
    }

    /**
     * 删除Redis里面的订单号
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function delRedisNoticeOrder($orderNo)
    {
        Redis::hDel('notice_orders', $orderNo);
    }

    // 我们订单的前一个状态
    public function getBeforeStatus($orderNo)
    {
        $beforeStatus = unserialize(OrderHistory::where('order_no', $orderNo)->latest('id')->value('before'))['status'];
        // 获取上一条操作记录，如果上一条为仲裁中，则取除了仲裁中和撤销中的最早的一条状态
        if ($beforeStatus == 16 || $beforeStatus == 18) {
            $orderHistories = OrderHistory::where('order_no', $orderNo)->latest('id')->get();
            $arr = [];
            foreach ($orderHistories as $key => $orderHistory) {
                $status = unserialize($orderHistory->before);

                if (isset($status['status']) && !in_array($status['status'], [15, 16, 18])) {
                    $arr[$key] = $status['status'];
                }
            }
            return current($arr);
        } else {
            return $beforeStatus;
        }
    }
}
