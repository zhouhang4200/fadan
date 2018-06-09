<?php

namespace App\Console\Commands;

use App\Exceptions\DailianException;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Console\Command;

class SyncOrderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncOrderData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // 找出所有没有接单订单
        $allOrder = Order::where('status', 1)->where('service_id', 4)->get();

        foreach ($allOrder as $item) {
            $order = Order::where('no', $item->no)->first();

            $orderDetails = $this->getOrderData($order);

            $this->sync($order, $order, $orderDetails);
        }
    }

    public function getOrderData($order)
    {
        $orderArr = $order->toArray();

        $orderDetails = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();

        return array_merge($orderDetails, $orderArr);
    }

    public function sync($datas, $order, $orderDetails)
    {
        // 加价 其他平台通用
        if (config('leveling.third_orders')) {
            // 遍历代练平台
            foreach (config('leveling.third_orders') as $third => $thirdOrderNoName) {
                // 如果订单详情里面存在某个代练平台的订单号，撤单此平台订单
                if (isset($orderDetails[$thirdOrderNoName]) && ! empty($orderDetails[$thirdOrderNoName])) {
                    try {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['updateOrder']], [$orderDetails]);
                        myLog('markup-hour-success', ['订单号' => isset($order) ? $order->no : '', '结果' => '平台'.$third.'加价成功', '加价后金额' => isset($order) ? $order->amount : '']);
                    } catch (DailianException $e) {
                        // 控制器-》方法-》参数
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDetails]);
                        $this->deleteThirdOrderNo($order->no, $third);
                        myLog('markup-hour-dailian-error', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败,已调用'.$third.'撤单，已删除本地订单', '原因' => $e->getMessage()]);
                    } catch (\Exception $e) {
                        call_user_func_array([config('leveling.controller')[$third], config('leveling.action')['delete']], [$orderDetails]);
                        $this->deleteThirdOrderNo($order->no, $third);
                        myLog('markup-hour-dailian-error', ['订单号' => isset($order) ? $order->no : '', '结果' => '失败,已调用'.$third.'撤单，已删除本地订单', '原因' => $e->getMessage()]);
                    }
                }
            }
        }
    }

    /**
     * 删除第三方订单号
     * @param $orderNo
     * @param $third
     */
    protected function deleteThirdOrderNo($orderNo, $third)
    {
        try {
            OrderDetail::where('order_no', $orderNo)->where('field_name', config('leveling.third_orders')[$third])
                ->update(['field_value' => '']);
        } catch (\Exception $exception) {
        }
    }
}
