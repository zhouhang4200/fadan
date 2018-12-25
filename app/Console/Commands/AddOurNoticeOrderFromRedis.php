<?php

namespace App\Console\Commands;

use RedisFacade;
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
            $hashOrderNos = RedisFacade::hGetAll('our_notice_orders');
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
                    $this->thirdStatus = isset($datas['data']['order_status']) ?? '';
                } else {
                    $this->thirdStatus = '';
                }
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
            break;
            case 3: // 蚂蚁
                // 其他代练平台, 订单必须在接单后，有对应的第三方平台
                if (config('leveling.third_orders')) {
                    if (isset($orderDatas['mayi_order_no'])) {
                        $orderDatas['mayi_order_no'] = $thirdOrderNo;
                        // 控制器-》方法-》参数 查找订单详情
                        $thirdOrder = call_user_func_array([config('leveling.controller')[3], config('leveling.action')['orderDetail']], [$orderDatas]);

                        if (isset($thirdOrder) && is_array($thirdOrder)) {
                            $this->thirdStatus = $thirdOrder['data']['status_type'] ?? '';
                        } else {
                            $this->thirdStatus = '';
                        }
                    }
                } else {
                    $this->thirdStatus = '';
                }
            break;
            case 4: // dd373
                // 其他代练平台, 订单必须在接单后，有对应的第三方平台
                if (config('leveling.third_orders')) {
                    if (isset($orderDatas['dd373_order_no'])) {
                        $orderDatas['dd373_order_no'] = $thirdOrderNo;
                        // 控制器-》方法-》参数 查找订单详情
                        $thirdOrder = call_user_func_array([config('leveling.controller')[4], config('leveling.action')['orderDetail']], [$orderDatas]);

                        if (isset($thirdOrder) && is_array($thirdOrder)) {
                            $this->thirdStatus = isset($thirdOrder['data']['orderStatus']) ? config('leveling.dd373.status')[$thirdOrder['data']['orderStatus']] : '';
                        } else {
                            $this->thirdStatus = '';
                        }
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
        RedisFacade::hDel('our_notice_orders', $orderNo);
    }
}
