<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Receiving;

use Log, Config, Weight, Order;

/**
 * 订单分配
 * Class OrderAssign
 * @package App\Console\Commands
 */
class OrderAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:Assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order assign.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 获取所有待分配订单
        foreach (waitReceivingGet() as $orderNo) {
            // 检测是否有用户接单，及接单数是否达到平台设置的下线 是：进行下一步 否：检测下一个订单
            if (receivingUserLen($orderNo) >= config('order.assignLowerLimit')) {
                // 取出所有用户, 获取所有接单用户的权重值
                $userId = Weight::run(receivingUser($orderNo));
                // 分配订单
                try {
                    Order::handle(new Receiving($orderNo, $userId));
                    // 发送通知
                    event(new NotificationEvent('NewOrderNotification', ['a' => 1]));
                } catch (CustomException $exception) {
                    Log::alert($exception);
                }
            } else {
                continue;
            }
        }
    }
}
