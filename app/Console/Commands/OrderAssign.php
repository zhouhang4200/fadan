<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Order as OrderModel;
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
        for ($i = 1; $i<=60; $i++) {
            sleep(1);
            $carbon = new Carbon;

            // 获取所有待分配订单
            foreach (waitReceivingGet() as $orderNo => $data) {
                // 保存创建时间的json
                $data = json_decode($data);
                // 检测40分钟订单无人操作
                $time = Carbon::parse($data->date);

                $minutes = $carbon->diffInMinutes($time);

                if ($minutes >= 40) {

                    OrderModel::where('no', $orderNo)->update(['status' => 4]);

                    waitReceivingDel($orderNo);
                }

                // 检测是否有用户接单，及接单数是否达到平台设置的下限 是：进行下一步 否：检测下一个订单
                if (receivingUserLen($orderNo) >= config('order.assignLowerLimit')) {

                    // 取出所有用户, 获取所有接单用户的权重值
                    $userId = Weight::run(receivingUser($orderNo));

                    // 分配订单
                    try {
                        Order::handle(new Receiving($orderNo, $userId));
                        waitReceivingDel($orderNo);
                        // 待接单数量加1
                        waitReceivingQuantitySub();
                        // 待接单数量
                        event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
                    } catch (CustomException $exception) {
                        Log::alert($exception->getMessage());
                    }
                } else {
                    continue;
                }
            }
        }

    }
}
