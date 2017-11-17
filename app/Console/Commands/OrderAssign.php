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

use League\Flysystem\Exception;
use Log, Config, Weight, Order;
use Symfony\Component\Console\Helper\Helper;

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
    protected $description = '订单分配任务';

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
                $minutes = $carbon->diffInMinutes($time);

                if ($minutes >= 40) {
                    Order::handle(new Cancel($orderNo, 0));
                    continue;
                } else {
                    // 可接单时间与当前时间的差
//                    $minutes = $carbon->diffInMinutes(Carbon::parse($data->receiving_date), false);
                    // 检测是否有人接单并且可接单时间大于等于了一分钟了: 是则将订单改为不可接单，然后分配订单。否有则加一分钟，重新写入hash表中
//                    if (receivingUserLen($orderNo) && $minutes >= 1) {
                    $userId = 0;
                    // 如果该订单旺旺在三十分钟分内下过单则找出之前的订单分给哪个商户，直接将该单分给该商户
                    if ($data->wang_wang) {
                        $userId = wangWangGetUserId($data->wang_wang);
                    }

                    if ($userId) {
                        // 将订单改为不可接单
                        Order::handle(new GrabClose($orderNo));
                        // 分配订单
                        try {
                            Order::handle(new Receiving($orderNo, $userId));
                            continue;
                        } catch (CustomException $exception) {
                            Log::alert($exception->getMessage());
                            continue;
                        }
                    }

                    // 检测是否有用户接单，及接单数是否达到平台设置的下限 是：进行下一步 否：检测下一个订单
                    if (receivingUserLen($orderNo) >= config('order.assignLowerLimit')) {

                        try {
                            // 将订单改为不可接单
                            Order::handle(new GrabClose($orderNo));
                        } catch (CustomException $exception) {
                            waitReceivingDel($orderNo);
                            Log::alert($exception->getMessage() . '- 关闭订单失败 -' . $orderNo);
                            continue;
                        }

                        try {
                            // 取出所有用户, 获取所有接单用户的权重值y
                            $userId = Weight::run(receivingUser($orderNo), $orderNo);
                            // 分配订单
                            Order::handle(new Receiving($orderNo, $userId));
                            // 记录相同旺旺的订单分配到了哪个商户
                            if ($data->wang_wang) {
                                wangWangToUserId($data->wang_wang, $userId);
                            }
                            continue;
                        } catch (CustomException $exception) {
                            waitReceivingDel($orderNo);
                            Log::alert($exception->getMessage() . '- 分配订单失败 -' . $orderNo);
                            continue;
                        }
                    } else {
                        // 将接单时间更新
                        waitReceivingAdd($orderNo,
                            Carbon::now('Asia/Shanghai')->addMinutes(1)->toDateTimeString(),
                            $data->created_date,
                            $data->wang_wang
                        );
                        continue;
                    }
                }
            }
        }

    }
}
