<?php

namespace App\Extensions\Weight\Algorithm;

use Carbon\Carbon;
use App\Models\Weight;

class OrderSuccess implements AlgorithmInterface
{
    public static function compute($users)
    {
        $startTime = Carbon::now()->subDays(3)->startOfDay()->toDateTimeString();

        $endTime = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

        $time = [$startTime, $endTime];

    	$ordersSuccessCount = 0;
        $ordersSuccessAvg = 0;
        $businessOrdersCount = [];
        $businessOrderSuccessAvg = [];

        // 所有商户订单总数
        $ordersCount = Weight::whereIn('gainer_user_id', $users)
            ->whereBetween('order_time', $time)
            ->count();

        // 所有商户成功订单总数
        $ordersSuccessCount = Weight::where('status', 1)
            ->whereIn('gainer_user_id', $users)
            ->whereBetween('order_time', $time)
            ->count();

        if ($ordersSuccessCount) {
            // 计算成功订单平均值
            $ordersSuccessAvg = bcdiv($ordersSuccessCount, $ordersCount, 4);
            // 获取商户的订单数,按商户ID分组
            $businessOrdersCount = Weight::select(\DB::raw('gainer_user_id, count(1) as orders_count'))
                ->whereIn('gainer_user_id', $users)
                ->whereBetween('order_time', $time)
                ->groupBy('gainer_user_id')
                ->pluck('orders_count', 'gainer_user_id')
                ->toArray();
            // 获取商户的成功订单数,按商户ID分组
            $businessSuccessOrdersCount = Weight::select(\DB::raw('gainer_user_id, count(1) as orders_success_count'))
                ->where('status', 1)
                ->whereIn('gainer_user_id', $users)
                ->whereBetween('order_time', $time)
                ->groupBy('gainer_user_id')
                ->pluck('orders_success_count', 'gainer_user_id')
                ->toArray();

            foreach ($businessOrdersCount as $business => $orderNumber) {
                $tempAvg = 0;
                if (isset($businessSuccessOrdersCount[$business])) {
                    $tempAvg = bcdiv($businessSuccessOrdersCount[$business], $orderNumber, 4);
                }
                $businessOrderSuccessAvg[$business] = $tempAvg;
            }
        }

        $data = ['ordersSuccessCount' => $ordersSuccessCount, 'ordersSuccessAvg' => $ordersSuccessAvg, 'businessOrderSuccessAvg' => $businessOrderSuccessAvg];

        foreach ($users as $user) {
            $succ = 0;
            // 如果商户的订单成功率大于平均值 或 新用户没有订单数据，则加权重 2
            if ((isset($data['businessOrderSuccessAvg'][$user])
                    && $data['businessOrderSuccessAvg'][$user] > $data['ordersSuccessAvg'])
                || !isset($data['businessOrderSuccessAvg'][$user])
            ) {
                $succ += 10;
            }
            $arr[$user] = $succ;
        }
        return $arr;
    }

    public static function data($users)
    {
        $startTime = Carbon::now()->subDays(3)->startOfDay()->toDateTimeString();

        $endTime = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

        $time = [$startTime, $endTime];

        $ordersSuccessCount = 0;
        $ordersSuccessAvg = 0;
        $businessOrdersCount = [];
        $businessOrderSuccessAvg = [];

        // 所有商户订单总数
        $ordersCount = Weight::whereIn('gainer_user_id', $users)
            ->whereBetween('order_time', $time)
            ->count();

        // 所有商户成功订单总数
        $ordersSuccessCount = Weight::where('status', 1)
            ->whereIn('gainer_user_id', $users)
            ->whereBetween('order_time', $time)
            ->count();

        if ($ordersSuccessCount) {
            // 计算成功订单平均值
            $ordersSuccessAvg = bcdiv($ordersSuccessCount, $ordersCount, 4);
            // 获取商户的订单数,按商户ID分组
            $businessOrdersCount = Weight::select(\DB::raw('gainer_user_id, count(1) as orders_count'))
                ->whereIn('gainer_user_id', $users)
                ->whereBetween('order_time', $time)
                ->groupBy('gainer_user_id')
                ->pluck('orders_count', 'gainer_user_id')
                ->toArray();
            // 获取商户的成功订单数,按商户ID分组
            $businessSuccessOrdersCount = Weight::select(\DB::raw('gainer_user_id, count(1) as orders_success_count'))
                ->where('status', 1)
                ->whereIn('gainer_user_id', $users)
                ->whereBetween('order_time', $time)
                ->groupBy('gainer_user_id')
                ->pluck('orders_success_count', 'gainer_user_id')
                ->toArray();

            foreach ($businessOrdersCount as $business => $orderNumber) {
                $tempAvg = 0;
                if (isset($businessSuccessOrdersCount[$business])) {
                    $tempAvg = bcdiv($businessSuccessOrdersCount[$business], $orderNumber, 4);
                }
                $businessOrderSuccessAvg[$business] = $tempAvg;
            }
        }

        return ['ordersSuccessCount' => $ordersSuccessCount, 'ordersSuccessAvg' => $ordersSuccessAvg, 'businessOrderSuccessAvg' => $businessOrderSuccessAvg];
    }
}
