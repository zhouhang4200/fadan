<?php

namespace App\Extensions\Weight\Algorithm;

use Carbon\Carbon;
use App\Models\MarketWeight;

class OrderSix implements AlgorithmInterface
{
    public static function compute($users)
    {
        
        $startTime = Carbon::now()->subDays(3)->startOfDay()->toDateTimeString();

        $endTime = Carbon::now()->subDays(1)->endOfDay()->toDateTimeString();

        $time = [$startTime, $endTime];

    	$sixOrders =  MarketWeight::select(\DB::raw('gainer_user_id,  count(1) as orders_equal_or_less_than_six'))
            ->where('status', 1)
            ->where('order_money', '<=', 6)
            ->whereIn('gainer_user_id', $users)
            ->whereBetween('order_time', $time)
            ->groupBy('gainer_user_id')
            ->pluck('orders_equal_or_less_than_six', 'gainer_user_id')
            ->toArray();

        foreach ($users as $user) {
            $six = 0;
            // 如果用户完成订单金额小于等于6元数量大于等于50单，则加权重 2
            if ((isset($sixOrders[$user])
                && $sixOrders[$user] > 50)
            ) {
                $six += 2;
            }
            $data[$user] = $six;
        }

        return $data;
    }
}
