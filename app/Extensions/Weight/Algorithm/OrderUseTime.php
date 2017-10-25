<?php

namespace App\Extensions\Weight\Algorithm;

use Carbon\Carbon;
use App\Models\MarketWeight;

class OrderUseTime implements AlgorithmInterface
{
    public static function compute($users)
    {
        $startTime = Carbon::now()->subDays(3)->startOfDay()->toDateTimeString();

        $endTime = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();
  
        $time = [$startTime, $endTime];

        $orderSuccess = OrderSuccess::data($users);

        $orderCounts = $orderSuccess['ordersSuccessCount'];

        // $orderCounts = MarketWeight::whereBetween('order_time', $time)->count('order_no');

        $orderUseTimeAvg = 0;

        $businessOrdersUseTimeAvg = [];

        if ($orderCounts) {
            // 商户所有成功订单的总耗时
            $ordersUseTimeCount = MarketWeight::where('status', 1)
                ->whereIn('gainer_user_id', $users)
                ->whereBetween('order_time', $time)
                ->sum('order_use_time');
            // 计算订单的平均耗时
            $orderUseTimeAvg = bcdiv($ordersUseTimeCount, $orderCounts);
            // 获取商户的订单的耗时,按商户ID分组
            $businessOrdersUseTimeAvg = MarketWeight::select(\DB::raw('gainer_user_id, (sum(order_use_time) / count(1)) as order_use_time_avg'))
                ->where('status', 1)
                ->whereIn('gainer_user_id', $users)
                ->whereBetween('order_time', $time)
                ->groupBy('gainer_user_id')
                ->pluck('order_use_time_avg', 'gainer_user_id')
                ->toArray();
        }

        $data =  ['orderUseTimeAvg' => $orderUseTimeAvg, 'businessOrdersUseTimeAvg' => $businessOrdersUseTimeAvg];

        foreach ($users as $user) {
            $time = 0; $succ = 0;
            // 如果商户的订单成功率大于平均值 或 新用户没有订单数据，则加权重 2
            if ((isset($orderSuccess['businessOrderSuccessAvg'][$user])
                    && $orderSuccess['businessOrderSuccessAvg'][$user] > $orderSuccess['ordersSuccessAvg'])
                || !isset($orderSuccess['businessOrderSuccessAvg'][$user])
            ) {
                $succ += 2;
            }

            // 如果用户订单平均耗时，小于总平均耗时， 或 新用户没有订单数据，则加权重 2
            if ((isset($data['businessOrdersUseTimeAvg'][$user])
                    && $data['businessOrdersUseTimeAvg'][$user] < $data['orderUseTimeAvg'])
                || !isset($orderSuccess['businessOrderSuccessAvg'][$user])
            ) {
                $time += 2;
            }
            $datas[$user] = $time;
        }
        return $datas;
    }
}
