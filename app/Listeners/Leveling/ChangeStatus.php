<?php

namespace App\Listeners\Leveling;

use App\Services\Show91;
use App\Models\OrderDetail;
use App\Services\DailianMama;
use App\Events\AutoRequestInterface;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 监听根据第三方平台自动调相应第三方平台接口
 */
class ChangeStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  autoRequestInterface  $event
     * @return void
     */
    public function handle(AutoRequestInterface $event)
    {
        $this->requestApiInterface($event->order, $event->name, $event->bool);
    }

    /**
     * 根据传过来的订单号，查找对应平台，根据平台找对应的接口
     * @param  [type] $order [订单模型]
     * @param  [type] $name  [方法名称]
     * @param  [type] $bool  [ 这里有的接口有的参数需要传和不传，bool为true表示要传某个参数，此
     *                       时会得到不同的结果，比如下单和修改订单是一个接口]
     * @return [null]        [根据代练平台，选择对应代练类里面的方法执行对应操作]
     */
    public function requestApiInterface($order, $name, $bool = false) 
    {
        // 订单详情，接单后third_order_no会有对应的相应接单平台的订单号
        $orderDetails = OrderDetail::where('order_no', $order->no)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

        switch ($orderDetails['third']) {
            case 1: // 91平台
                call_user_func_array([Show91::class, $name], [$order, $bool]);
                break;
            case 2: // 代练妈妈,由于代练妈妈类里面的方法与91不同所以根据传进来的方法名对应代练妈妈方法名
                switch ($name) {
                    case 'addPrice':
                        $operate = 22002; // 补款
                        $name = 'operationOrder';
                        call_user_func_array([DailianMama::class, $name], [$order, $operate, $bool]);
                        break;
                    case 'editOrderAccPwd':
                        $operate = 22003; // 修改账号密码
                        $name = 'operationOrder';
                        call_user_func_array([DailianMama::class, $name], [$order, $operate, $bool]);
                        break;
                    case 'addLimitTime':
                        $operate = 22001; // 增加代练时间
                        $name = 'operationOrder';
                        call_user_func_array([DailianMama::class, $name], [$order, $operate, $bool]);
                        break;
                    case 'addOrder':
                        $name = 'releaseOrder'; // 修改订单
                        call_user_func_array([DailianMama::class, $name], [$order, $bool]);
                        break;
                }
                break;
            case 3:
                return true;
                break;
            case 4:
                return true;
                break;
        }

        // 测试环境，都不存在第三方的时候，调用默认91代练
        if (! $orderDetails['third']) {
            call_user_func_array([Show91::class, $name], [$order, $bool]);
        }
    }
}
