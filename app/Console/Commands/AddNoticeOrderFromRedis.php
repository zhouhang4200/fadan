<?php

namespace App\Console\Commands;

use DB;
use RedisFacade;
use Exception;
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
        $orderNos = RedisFacade::hGetAll('notice_orders');

        // 操作成功状态为1， 操作失败状态为0
        foreach ($orderNos as $orderNo => $statusAndAction) {
            $status = explode('-', $statusAndAction)[0]; // 操作成功 1 与失败 0
            $third = explode('-', $statusAndAction)[1]; // 第三方平台 1 ， 2
            $action = explode('-', $statusAndAction)[2]; // 第三方操作方法,91根据方法判断操作，代练妈妈直接传的操作
            $thirdStatus = explode('-', $statusAndAction)[3] ?? ''; // 第三方操作后的状态，91需要再调接口，代练妈妈直接获得
            // 写入记录到报警表
            $this->checkOrderNotice($orderNo, $status, $action, $third, $thirdStatus);
            // 删除redis里面的记录
            $this->delRedisNoticeOrder($orderNo);
        }
    }

     /**
     * 获取91平台状态和子状态
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function getThirdOrderStatus($orderNo)
    {
        if (! $orderNo) {
            throw new Exception('第三方订单号不存在');
        }

        $options = ['oid' => $orderNo,]; 

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
    public function checkOrderNotice($orderNo, $status, $action, $third, $thirdStatus)
    {
        DB::beginTransaction();
        try {
            // $this->delRedisNoticeOrder($orderNo);
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
                            // 查找91平台目前订单的状态
                            $res = Show91::orderDetail($options);
                            // 91平台订单状态, 具体看 show91 config 文件
                            $thirdStatus =  $res['data']['order_status'];
                            $thirdConsult = $res['data']['inSelfCancel'] ? 13 : false;
                            $thirdComplain = $res['data']['inAppeal'] ? 14 : false;
                            // 我们的订单上一个状态，当订单在撤销中或者仲裁中的时候需要用到
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
                        if ($status == 1) { // 第三方做了成功的操作
                            if (! $orderDetail['third_order_no']) {
                                throw new Exception('第三方订单号不存在');
                            }
                            // 我们平台订单的上一个状态
                            $beforeStatus = $this->getBeforeStatus($orderNo);

                            switch ($thirdStatus) {
                                case '代练中': // 如果代练妈妈状态在 撤销 中，看我们前一个状态
                                    if ($order->status == 13) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '待验收': 
                                    if ($order->status == 14) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '已结算': 
                                    if ($order->status == 20) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '撤销中': 
                                    if ($order->status == 15) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '已撤销': 
                                    if ($order->status == 19) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '仲裁中': 
                                    if ($order->status == 16) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '已仲裁': 
                                    if ($order->status == 21) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '异常': 
                                    if ($order->status == 17) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                case '已锁定': 
                                    if ($order->status == 18) {
                                        $this->delRedisNoticeOrder($orderNo);
                                    } else {
                                        $this->addOrderNotice($order, $status, $action, $thirdStatus);
                                    }
                                    break;
                                default:
                                    return true;
                            }
                        } else {
                            $this->addOrderNotice($order, $status, $action, $thirdStatus);
                        }
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
    public function addOrderNotice($order, $status, $action, $thirdStatus = '')
    {
        // 订单详情
        $orderDetail = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();
        // 报警参数
        $data                            = [];
        $data['creator_user_id']         = $order->creator_user_id;
        $data['creator_primary_user_id'] = $order->creator_primary_user_id;
        $data['gainer_user_id']          = $order->gainer_user_id;
        $data['creator_user_name']       = $order->creatorUser->name;
        $data['order_no']                = $order->no;
        $data['third_order_no']          = $orderDetail['third_order_no'];
        $data['third']                   = $orderDetail['third'];
        $data['status']                  = $order->status;
        $data['create_order_time']       = $order->created_at;
        $data['complete']                = 0;
        $data['amount']                  = $order->amount;
        $data['security_deposit']        = $orderDetail['security_deposit'];
        $data['efficiency_deposit']      = $orderDetail['efficiency_deposit'];

        // 区分平台
        switch ($orderDetail['third']) {
            case 1:
                // 91里面很特殊，需要看不同的状态
                $twoStatus = $this->getThirdOrderStatus($data['third_order_no']);
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
                break;
            case 2:
                $data['operate'] = $action;
                $data['third_status'] = $thirdStatus;
                $data['child_third_status'] = 100; // 表示没有子状态
                break;
            default:
                throw new Exception('不存在的代练平台!');
                break;
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
        RedisFacade::hDel('notice_orders', $orderNo);
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

                if (isset($status['status']) && ! in_array($status['status'], [15, 16, 18])) {
                    $arr[$key] = $status['status'];
                }
            }
            return current($arr);
        } else {
            return $beforeStatus;
        }
    }
}
