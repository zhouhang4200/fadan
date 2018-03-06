<?php

namespace App\Console\Commands;

use App\Extensions\Order\Operations\Cancel;
use App\Extensions\Order\Operations\DeliveryFailure;
use App\Extensions\Order\Operations\GrabClose;
use App\Models\User;
use App\Repositories\Frontend\OrderDetailRepository;
use App\Services\RedisConnect;
use Carbon\Carbon;
use App\Models\Order as OrderModel;
use Illuminate\Console\Command;
use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Receiving;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
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


    public function handle()
    {
        while (orderAssignSwitchGet()) {
            // 获取所有待分配订单
            $carbon = new Carbon;
            foreach (waitReceivingGet() as $orderNo => $data) {
                // 保存创建时间的json
                $data = json_decode($data);
                $time = Carbon::parse($data->created_date);
                $minutes = $carbon->diffInMinutes($time);

                // 获取订单信息
                $orderInfo = OrderModel::where('no', $orderNo)->first();

                $sendUser = $data->creator_primary_user_id ?? 0;
                if ($minutes >= 40 && !in_array($sendUser, [8311, 8111])) {
                    try {
                        Order::handle(new Cancel($orderNo, 0));
                    } catch (CustomException $exception) {
                        waitReceivingDel($orderNo);
                        Log::alert($exception->getMessage() . '- 重复取消 -' . $orderNo);
                    }
                    continue;
                } else {
                    // 如果是房卡商品则写入充值队列
                    if (in_array($orderInfo->goods_id, [1906, 1907, 1912, 1913, 1914, 1908, 1909, 1910, 1911])) {
                        try {
                            // 获取商品中的数字
                            preg_match('|\d+|', $orderInfo->goods_name, $faceValue);
                            // 如果正则取出来的是数字则写入队列
                            if (isset($faceValue[0]) && is_numeric($faceValue[0])) {
                                $orderDetail  = OrderDetailRepository::getByOrderNo($orderNo);
                                preg_match('|\d+|', $orderDetail['account'], $chargeId);
                                if (isset($chargeId[0]) && is_numeric($chargeId[0])) {
                                    $redis = RedisConnect::order();
                                    $redis->lpush(config('redis.order.roomCardRecharge') . $orderInfo->game_id, $orderNo . '-'. $chargeId[0] .'-' . $orderInfo->quantity * $faceValue[0]. '-' . $orderInfo->goods_name);
                                } else {
                                    $this->assign($orderNo, 8017);
                                    $this->fail($orderNo, 8017);
                                    myLog('exception', ['order'=> $orderNo, '面值' => $faceValue[0], '没有ID']);
                                }
                            } else {
                                $this->assign($orderNo, 8017);
                                $this->fail($orderNo, 8017);
                                myLog('exception', ['order'=> $orderNo, '没有商品']);
                            }

                        } catch (\Exception $exception) {
                            $this->assign($orderNo, 8017);
                            $this->fail($orderNo, 8017);
                            myLog('exception', [$orderNo, $exception->getMessage()]);
                        }
                    }

                    $userId = 0;
                    // 如果该订单旺旺在三十分钟分内下过单则找出之前的订单分给哪个商户，直接将该单分给该商户
                    if ($data->wang_wang) {
                        $userId = wangWangGetUserId($data->wang_wang);

                        try {
                            if ($userId) {
                                // 如果当前用户不在线则不分单给他
                                $userInfo = User::find($userId);
                                if ($userInfo->online != 1) {
                                    $userId = 0;
                                }
                            }
                        } catch (Exception $exception) {

                        }
                    }

                    if ($userId) {
                        try {
                            // 将订单改为不可接单
                            Order::handle(new GrabClose($orderNo));
                        }catch (CustomException $exception) {
                            waitReceivingDel($orderNo);
                            Log::alert($exception->getMessage() . '更改状态失败');
                            continue;
                        }
                        // 分配订单
                        try {
                            Order::handle(new Receiving($orderNo, $userId));
                            continue;
                        } catch (CustomException $exception) {
                            waitReceivingDel($orderNo);
                            Log::alert($exception->getMessage() . ' 分配订单失败');
                            continue;
                        }
                    } else if (in_array($orderInfo->goods_id, [1906, 1907, 1912, 1913, 1914, 1908, 1909, 1910, 1911])) { // 如果是房卡直接分配到固定商家
                        $userId = 8017;
                        try {
                            // 将订单改为不可接单
                            Order::handle(new GrabClose($orderNo));
                        }catch (CustomException $exception) {
                            waitReceivingDel($orderNo);
                            Log::alert($exception->getMessage() . '更改状态失败');
                            continue;
                        }
                        // 分配订单
                        try {
                            Order::handle(new Receiving($orderNo, $userId));
                            // 记录相同旺旺的订单分配到了哪个商户
                            if ($data->wang_wang) {
                                wangWangToUserId($data->wang_wang, $userId);
                            }
                            continue;
                        } catch (CustomException $exception) {
                            waitReceivingDel($orderNo);
                            Log::alert($exception->getMessage() . ' 分配订单失败');
                            continue;
                        }
                    } else {
                        $currentTim = strtotime(date('Y-m-d H:i:s'));
                        $currentAfter = strtotime(date('Y-m-d H:i:s', $currentTim)) + 10;
                        // 可接单时间与当前时间的差
                        $minutes = bcsub(strtotime($data->receiving_date), $currentTim, 0);

                        // 如果有用户接单，则进行可接单时间判断，如果没有则增加可接单时间
                        if (receivingUserLen($orderNo)) {
                            if ($minutes <= 0) {
                                try {
                                    // 将订单改为不可接单
                                    Order::handle(new GrabClose($orderNo));
                                } catch (CustomException $exception) {
                                    waitReceivingDel($orderNo);
                                    Log::alert($exception->getMessage() . '- 关闭订单失败 -' . $orderNo);
                                    continue;
                                }

                                try {
                                    // 取出所有用户, 获取所有接单用户的权重值
                                    $userId = app('weight')->run(receivingUser($orderNo), $orderNo);
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
                            }
                        } else {
                            // 将可接单时间更新
                            waitReceivingAdd($orderNo,
                                date('Y-m-d H:i:s', $currentAfter),
                                $data->created_date,
                                $data->wang_wang,
                                $data->creator_primary_user_id ?? 0
                            );
                            continue;
                        }
                    }

                }
            }
            sleep(1);
        }
    }

    protected function convert($size)
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * 分配订单
     * @param $orderNo
     * @param $userId
     */
    public function assign($orderNo, $userId)
    {
        try {
            // 将订单改为不可接单
            Order::handle(new GrabClose($orderNo));
            // 分配订单
            Order::handle(new Receiving($orderNo, $userId));
        } catch (CustomException $exception) {
            waitReceivingDel($orderNo);
            myLog('exception', [ '单号' => $orderNo, '异常' => $exception->getMessage()]);
        }
    }

    /**
     * 直接失败订单
     * @param $orderNo
     * @param $userId
     */
    public function fail($orderNo, $userId)
    {
        try {
            // 订单
            Order::handle(new DeliveryFailure($orderNo, $userId, '充值失败'));
            // 商家失败后直接取消订单
            Order::handle(new Cancel($orderNo, 0, 0));
        } catch (\Exception  $exception) {
            myLog('exception', ['单号' => $orderNo, '异常' => $exception->getMessage()]);
        }
    }
}
