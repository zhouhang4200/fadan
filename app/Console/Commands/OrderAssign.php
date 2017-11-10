<?php

namespace App\Console\Commands;

use App\Extensions\Order\Operations\Cancel;
use App\Extensions\Order\Operations\GrabClose;
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
        \Log::alert(date('Y-m-d H:i:s'));
        for ($i = 1; $i<=60; $i++) {
            sleep(1);
            $carbon = new Carbon;

            // 获取所有待分配订单
            foreach (waitReceivingGet() as $orderNo => $data) {

                // 保存创建时间的json
                $data = json_decode($data);
                $time = Carbon::parse($data->created_date);
                $minutes = $carbon->diffInMinutes($time, false);

                if ($minutes >= 40) {
                    Order::handle(new Cancel($orderNo, 0));
                    waitReceivingQuantitySub();
                    waitReceivingDel($orderNo);
                } else {
                    // 可接单时间与当前时间的差
                    $minutes = $carbon->diffInMinutes(Carbon::parse($data->receiving_date), false);
                    // 检测是否有人接单并且可接单时间大于等于了一分钟了: 是则将订单改为不可接单，然后分配订单。否有则加一分钟，重新写入hash表中
                    if (receivingUserLen($orderNo) && $minutes >= 1) {
                        // 将订单改为不可接单
                        Order::handle(new GrabClose($orderNo));
                        // 取出所有用户, 获取所有接单用户的权重值y
                        $userId = Weight::run(receivingUser($orderNo));
                        // 分配订单
                        try {
                            Order::handle(new Receiving($orderNo, $userId));
                            waitReceivingDel($orderNo);
                            // 待接单数量减1
                            waitReceivingQuantitySub();
                            // 待接单数量
                            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
                        } catch (CustomException $exception) {
                            Log::alert($exception->getMessage());
                        }
                    } else {
                        // 将接单时间更新
                        waitReceivingAdd($orderNo, json_encode(['receiving_date' => Carbon::now('Asia/Shanghai')->addMinutes(1)->toDateTimeString(), 'created_date' => $data->created_date]));
                        continue;
                    }
                }
            }
        }

    }
}
