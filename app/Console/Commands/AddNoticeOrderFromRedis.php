<?php

namespace App\Console\Commands;

use DB;
use Redis;
use App\Models\Order;
use App\Services\Show91;
use App\Models\OrderNotice;
use App\Models\OrderDetail;
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

        foreach ($orderNos as $orderNo => $statusAndAction) {
            $status = explode('-', $statusAndAction)[0];
            $action = explode('-', $statusAndAction)[1];

            $this->checkOrderNotice($orderNo, $status, $action);
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
    public function checkOrderNotice($orderNo, $status, $action)
    {
        $order = Order::where('no', $orderNo)->first();
        if ($order) {
            if ($status == 1) {
                $orderDetail = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();

                if (! $orderDetail['third_order_no']) {
                    throw new Exception('第三方订单号不存在');
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
                            $this->addOrderNotice($order, $status, $action);
                        }
                        return true;
                    break;
                    case 2:
                        if ($order->status != 14) {
                            $this->addOrderNotice($order, $status, $action); 
                        }
                        return true;
                    break;
                    default:
                        return true;
                }
            } else {
                $this->addOrderNotice($order, $status, $action);
            }
        }
    }

    /**
     * 添加订单报警
     * @param [type] $order [description]
     */
    public function addOrderNotice($order, $status, $action)
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
                $data['child_third_status'] = 100;
            }

            OrderNotice::create($data);
            $this->delRedisNoticeOrder($order->no);
        } catch (Exception $e) {
            DB::rollback();
            myLog('order-notice-e', [$e->getMessage()]);
        }
        DB::commit();
        return true;
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
}
