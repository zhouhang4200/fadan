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
            case 2: // 代练妈妈,由于代练妈妈类里面的方法与91不同所以根据传进来的91方法名找对应代练妈妈方法名
                switch ($name) {
                    case 'addPrice':
                        $operate = 22002; // 补款
                        $name = 'operationOrder';
                        // 后面的参数 true 没有任何意义，可以不写，也可以写 $bool，方法里面用不到这个参数
                        call_user_func_array([DailianMama::class, $name], [$order, $operate, true]);
                        break;
                    case 'editOrderAccPwd':
                        $operate = 22003; // 修改账号密码
                        $name = 'operationOrder';
                        // 后面的参数 true 没有任何意义，可以不写，也可以写 $bool，方法里面用不到这个参数
                        call_user_func_array([DailianMama::class, $name], [$order, $operate, true]);
                        break;
                    case 'addLimitTime':
                        $operate = 22001; // 增加代练时间
                        $name = 'operationOrder';
                        // 后面的参数 true 没有任何意义，可以不写，也可以写 $bool，方法里面用不到这个参数
                        call_user_func_array([DailianMama::class, $name], [$order, $operate, true]);
                        break;
                    case 'addOrder':
                        $name = 'releaseOrder'; // 修改订单
                        // 后面的参数 true 没有任何意义，可以不写，也可以写 $bool，方法里面用不到这个参数
                        call_user_func_array([DailianMama::class, $name], [$order, true]);
                        break;
                }
                break;
            case 3: // 未定的代练平台
                return true;
                break;
            case 4: // 未定的代练平台
                return true;
                break;
            default: // 如果此单还没被接单，那么third值为空，此时同时调用所有平台发送修改订单接口
                // 如果此单还没被接单，那么third值为空，此时同时调用所有平台发送修改订单接口 
                // 向91发送
                if ($orderDetails['show91_order_no']) {
                    call_user_func_array([Show91::class, $name], [$order, $bool]);
                }
                // 向代练妈妈发送修改订单, 根据$name-》91的方法名，转换为代练妈妈的方法名，代练妈妈的方法修改订单方法
                // 只有一个，所以需要传递第二个参数，操作序号$operate
                if ($orderDetails['dailianmama_order_no']) {
                    switch ($name) {
                        case 'addPrice':
                            $operate = 22002; // 补款
                            $name = 'operationOrder';
                            call_user_func_array([DailianMama::class, $name], [$order, $operate, true]);
                            break;
                        case 'editOrderAccPwd':
                            $operate = 22003; // 修改账号密码
                            $name = 'operationOrder';
                            call_user_func_array([DailianMama::class, $name], [$order, $operate, true]);
                            break;
                        case 'addLimitTime':
                            $operate = 22001; // 增加代练时间
                            $name = 'operationOrder';
                            call_user_func_array([DailianMama::class, $name], [$order, $operate, true]);
                            break;
                        case 'addOrder':
                            $name = 'releaseOrder'; // 修改订单
                            call_user_func_array([DailianMama::class, $name], [$order, true]);
                            break;
                    }
                }
                break;
        }
    }
}
