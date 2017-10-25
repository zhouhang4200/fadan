<?php

namespace App\Http\Controllers\Frontend;

use Redis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\MarketWeight;
use App\Http\Controllers\Controller;
use App\Extensions\Weight\Algorithm\OrderSix;
use App\Extensions\Weight\Algorithm\OrderUseTime;
use App\Extensions\Weight\Algorithm\OrderSuccess;

class MarketWeightData extends Controller
{
    public static function marketUserDatas($users)
    {
        $startTime = Carbon::now()->subDays(3)->startOfDay()->toDateTimeString();

        $endTime = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

        $time = [$startTime, $endTime];

        // $orderCounts = MarketWeight::whereBetween('order_time', $time)->count('order_no');

        // $users = MarketWeight::whereBetween('order_time', $time)->where('status', 1)->pluck('gainer_user_id')->unique();

        // 根据商户前三天订单，3项加成权重值
        return $userWeight = static::weightComputeByOrders($users, $time);
        // 3天平均订单和个人订单耗时
        $threeDaysTimeWaste = OrderUseTime::compute($users, $time, $orderCounts);
        // 3天平均订单和个人成功率
        $threeDaysSuccessOrders = OrderSuccess::compute($users, $time); 
        // 小于6元的订单数
        $threeDaysSixOrders = OrderSix::compute($users, $time); 
    }

    /*
     * 根据商户前三天订单信息订算用户动态权重
     * @param array $users 用户ID 数组
     */
    public static function weightComputeByOrders($users, $time)
    {
        // 商户权重
        $businessWeight = [];

        $ordersSuccessAvgAndBusinessOrderSuccessCount = OrderSuccess::compute($users, $time); 
        $ordersUseTimeAvgAndBusinessOrderUseTimeAvg = OrderUseTime::compute($users, $time, $ordersSuccessAvgAndBusinessOrderSuccessCount['ordersSuccessCount']);
        $ordersMoneyEqualOrLessThanSix = OrderSix::compute($users, $time); 

        foreach ($users as $user) {
            $succ = 0; $time = 0; $six = 0;
            // 如果商户的订单成功率大于平均值 或 新用户没有订单数据，则加权重 2
            if ((isset($ordersSuccessAvgAndBusinessOrderSuccessCount['businessOrderSuccessAvg'][$user])
                    && $ordersSuccessAvgAndBusinessOrderSuccessCount['businessOrderSuccessAvg'][$user] > $ordersSuccessAvgAndBusinessOrderSuccessCount['ordersSuccessAvg'])
                || !isset($ordersSuccessAvgAndBusinessOrderSuccessCount['businessOrderSuccessAvg'][$user])
            ) {
                $succ += 2;
            }
            // 如果用户订单平均耗时，小于总平均耗时， 或 新用户没有订单数据，则加权重 2
            if ((isset($ordersUseTimeAvgAndBusinessOrderUseTimeAvg['businessOrdersUseTimeAvg'][$user])
                    && $ordersUseTimeAvgAndBusinessOrderUseTimeAvg['businessOrdersUseTimeAvg'][$user] < $ordersUseTimeAvgAndBusinessOrderUseTimeAvg['orderUseTimeAvg'])
                || !isset($ordersSuccessAvgAndBusinessOrderSuccessCount['businessOrderSuccessAvg'][$user])
            ) {
                $time += 2;
            }
            // 如果用户完成订单金额小于等于6元数量大于等于50单，则加权重 2
            if ((isset($ordersMoneyEqualOrLessThanSix['ordersMoneyEqualOrLessThanSix'][$user])
                && $ordersMoneyEqualOrLessThanSix['ordersMoneyEqualOrLessThanSix'][$user] > 50)
            ) {
                $six += 2;
            }
            $userWeight['time'][$user] = $time;
            $userWeight['succ'][$user] = $succ;
            $userWeight['six'][$user] = $six;
            // $userWeight[$user] = ['time' => $time, 'succ' => $succ, 'six' => $six];
        }
dd($userWeight);
        foreach ($userWeight as $user => $weight) {
            $key1 = "market:orders:user:weight:succ:$user";
            $key2 = "market:orders:user:weight:time:$user";
            $key3 = "market:orders:user:weight:six:$user";
            Redis::set($key1, $weight['succ']);
            Redis::set($key2, $weight['time']);
            Redis::set($key3, $weight['six']);
        }
        return $userWeight;
    }
}
