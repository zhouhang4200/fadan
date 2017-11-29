<?php

namespace App\Console\Commands;

use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Cancel;
use App\Extensions\Order\Operations\Complete;
use Illuminate\Console\Command;
use Log, Config, Order;

/**
 * 订单确认
 * Class OrderConfirm
 * @package App\Console\Commands
 */
class OrderConfirm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:Confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单自动确认';


    public function handle()
    {
        while (1) {
            // 获取所有待确认收货的订单
            foreach (waitConfirm() as $orderNo => $deliveryDate) {
                // 如果当前时间大于或等于发货时间 则自动确认收货
                if(time() >= strtotime('+2 day', $deliveryDate)) {
                    try {
                        Order::handle(new Complete($orderNo, 0));
                    } catch (CustomException $exception) {
                        waitConfirmDel($orderNo);
                        Log::alert([$orderNo, '自动确认收货失败，原因：' . $exception->getMessage()]);
                    }
                    // 删除
                    waitConfirmDel($orderNo);
                }
            }
            sleep(1);
        }
    }
}
