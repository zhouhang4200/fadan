<?php

namespace App\Console\Commands;

use Redis;
use Exception;
use App\Models\Order;
use App\Services\Show91;
use App\Models\OrderDetail;
use App\Models\OrderNotice;
use App\Services\DailianMama;
use Illuminate\Console\Command;
use App\Exceptions\DailianException;

class AddOurNoticeOrderFromRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从redis里面获取我们平台操作失败的报警订单';
    /**
     * 第三方订单子状态，show91独有，代练妈妈默认为100
     * @var [type]
     */
    public $thirdChildStatus;
    /**
     *  第三方订单状态
     * @var [type]
     */
    public $thirdStatus;

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
             // 获取所有的 我们平台操作失败的报警订单号
            $hashOrderNos = Redis::hGetAll('our_notice_orders');
            // 遍历订单
            foreach ($hashOrderNos as $orderNo => $operateAndOrderStatus) {
                // 第三方平台 1 ， 2
                $third = explode('-', $operateAndOrderStatus)[0] ?? ''; 
                // 我们平台失败操作编号
                $orderOperate = explode('-', $operateAndOrderStatus)[1] ?? ''; 
                // 我们平台订单目前状态编号
                $orderStatus = explode('-', $operateAndOrderStatus)[2] ?? ''; 
                // 获取我们的订单
                $order = Order::where('no', $orderNo)->first();

                if (! $order) {
                    $this->deleteOperateFailOrderFromRedis($orderNo);
                    continue;
                }
                // 获取我们的订单详情
                $orderDetails = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();
                try {
                    // 获取第三方订单状态
                    $this->setThirdStatusAndThirdChildStatus($third, $order, $orderDetails['third_order_no']);
                    // 写入订单报警表
                    if ($this->thirdStatus) {
                        $this->addDatasToOrderNotice($order, $orderDetails, $orderOperate);
                    }
                    // 写完记录，删除redis记录
                    $this->deleteOperateFailOrderFromRedis($orderNo);
                } catch (\Exception $e) {
                    myLog('order-notice', ['message' => $e->getMessage(), 'no' => $order->no]);
                } catch (DailianException $e) {
                    myLog('order-notice', ['message' => $e->getMessage(), 'no' => $order->no]);
                }
            } 
    }

    /**
     * 获取第三方平台订单当前状态
     * @param  [type] $third   [description]
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public function setThirdStatusAndThirdChildStatus($third, $order, $thirdOrderNo)
    {
        // 根据平台，获取第三方订单目前的状态
        switch ($third) {
            // 91代练
            case 1: 
                $datas = $this->getShow91OrderStatus($thirdOrderNo);

                if (isset($datas) && is_array($datas)) {
                    $this->thirdStatus = isset($datas['data']['order_status']) ? config('leveling.show91.status')[$datas['data']['order_status']] : '';
                } else {
                    $this->thirdStatus = '';
                }
                // 大状态里面可能有消状态（协商，申诉)
                // $thirdConsult = $datas['data']['inSelfCancel'] ? 13 : false;
                // $thirdComplain = $datas['data']['inAppeal'] ? 14 : false;
                // // 如果状态为代练中，需要详细区分到底是哪个状态
                // if ($thirdComplain && empty($thirdConsult)) {
                //     $thirdChildStatus = 14; // 申诉中
                // } elseif ($thirdConsult && empty($thirdComplain)) {
                //     $thirdChildStatus = 13; // 协商中
                // } elseif ($thirdConsult && $thirdComplain) {
                //     $thirdChildStatus = 15;
                // } else {
                //     $thirdChildStatus = 100; // 表示没有子状态
                // }
                // // 给子状态赋值
                // $this->thirdChildStatus = $thirdChildStatus;
                // 91要检查是否是真的报警，只有两个状态都对不上我们的状态才会记录报警，不然删除redis报警信息
                // switch ($datas['data']['order_status']) {
                //     // 代练中
                //     case 1: 
                //         // 91订单此时在代练中，我们也在代练中,则删除redis报警订单
                //         if (! $thirdComplain && ! $thirdConsult && $order->status == 13) { 
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         // 91在申诉中，我们也在申诉中,则删除redis报警订单
                //         } elseif ($thirdComplain && ! $thirdConsult && $order->status == 16) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         // 91在撤销中，我们也在撤销， 则删除redis报警订单
                //         } elseif (! $thirdComplain && $thirdConsult && $order->status == 15) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $datas['data']['order_status'];
                //         }
                //         break;
                //     // 待验收
                //     case 2: 
                //         if (! $thirdComplain && ! $thirdConsult && $order->status == 14) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } elseif ($thirdComplain && ! $thirdConsult && $order->status == 16) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } elseif (! $thirdComplain && $thirdConsult && $order->status == 15) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $datas['data']['order_status'];
                //         }
                //         break;
                //     default:
                //         return $this->thirdStatus = false;
                // }
            break;
            // 代练妈妈
            case 2:
                // $this->thirdChildStatus = 100;
                // 代练妈妈订单详情
                $datas = DailianMama::orderinfo($order);

                if (isset($datas) && is_array($datas)) {
                    $this->thirdStatus = $datas['data']['info']['orderstatusname'] ?? '';
                } else {
                    $this->thirdStatus = '';
                }
                // 代练妈妈订单目前状态
                // $thirdStatus = $datas['data']['info']['orderstatusname'];
                // // 第三方订单名称
                // switch ($thirdStatus) {
                //     case '代练中': // 如果代练妈妈状态在 撤销 中，看我们前一个状态
                //         if ($order->status == 13) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '待验收': 
                //         if ($order->status == 14) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '已结算': 
                //         if ($order->status == 20) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '撤销中': 
                //         if ($order->status == 15) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '已撤销': 
                //         if ($order->status == 19) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '仲裁中': 
                //         if ($order->status == 16) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '已仲裁': 
                //         if ($order->status == 21) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '异常': 
                //         if ($order->status == 17) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     case '已锁定': 
                //         if ($order->status == 18) {
                //             $this->deleteOperateFailOrderFromRedis($order->no);
                //         } else {
                //             // 91订单的大状态
                //             $this->thirdStatus = $thirdStatus;
                //         }
                //         break;
                //     default:
                //         $this->thirdStatus = false;
                //         break;
                // }
            break;
            case 3: // 蚂蚁
                // 其他代练平台, 订单必须在接单后，有对应的第三方平台
                if (config('leveling.third_orders')) {
                    $orderDatas['mayi_order_no'] = $thirdOrderNo;
                    // 控制器-》方法-》参数 查找订单详情
                    $thirdOrder = call_user_func_array([config('leveling.controller')[3], config('leveling.action')['orderDetail']], [$orderDatas]);
                    if (isset($thirdOrder) && is_array($thirdOrder)) {
                        $this->thirdStatus = $thirdOrder['data']['status_type'] ?? '';
                    } else {
                        $this->thirdStatus = '';
                    }
                } else {
                    $this->thirdStatus = '';
                }
            break;
            case 4: // dd373
                // 其他代练平台, 订单必须在接单后，有对应的第三方平台
                if (config('leveling.third_orders')) {
                    $orderDatas['dd373_order_no'] = $thirdOrderNo;
                    // 控制器-》方法-》参数 查找订单详情
                    $thirdOrder = call_user_func_array([config('leveling.controller')[4], config('leveling.action')['orderDetail']], [$orderDatas]);
                    if (isset($thirdOrder) && is_array($thirdOrder)) {
                        $this->thirdStatus = isset($thirdOrder['data']['orderStatus']) ? config('leveling.dd373.status')[$thirdOrder['data']['orderStatus']] : '';
                    } else {
                        $this->thirdStatus = '';
                    }
                } else {
                    $this->thirdStatus = '';
                }
            break;
        }
    }

    /**
     * 获取91代练订单目前状态
     * @return [type] [description]
     */
    public function getShow91OrderStatus($thirdOrderNo)
    {
        $options = ['oid' => $thirdOrderNo,]; 

        return Show91::orderDetail($options);
    }

    /**
     * 报警写入订单报警表
     * @param [type] $order [description]
     */
    public function addDatasToOrderNotice($order, $orderDetails, $orderOperate)
    {
        // 报警参数
        $data                            = [];
        $data['creator_user_id']         = $order->creator_user_id;
        $data['creator_primary_user_id'] = $order->creator_primary_user_id;
        $data['gainer_user_id']          = $order->gainer_user_id;
        $data['creator_user_name']       = $order->creatorUser->name;
        $data['order_no']                = $order->no;
        $data['third_order_no']          = $orderDetails['third_order_no'];
        $data['third']                   = $orderDetails['third'];
        $data['status']                  = $order->status;
        $data['create_order_time']       = $order->created_at;
        $data['complete']                = 0;
        $data['amount']                  = $order->amount;
        $data['security_deposit']        = $orderDetails['security_deposit'];
        $data['efficiency_deposit']      = $orderDetails['efficiency_deposit'];
        $data['operate']                 = '';
        $data['our_operate']             = config('order.operation_type')[$orderOperate] ?? '';
        $data['third_status']            = $this->thirdStatus ?? '';
        $data['child_third_status']      = $this->thirdChildStatus ?? 100; 
        // 写入数据
        OrderNotice::create($data);
    }

    /**
     * 删除redis哈希key
     * @return [type] [description]
     */
    public function deleteOperateFailOrderFromRedis($orderNo)
    {
        Redis::hDel('our_notice_orders', $orderNo);
    }
}
