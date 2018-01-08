<?php
namespace App\Repositories\Backend;

use App\Exceptions\CustomException;
use App\Models\OrderCharge;
use App\Models\OrderChargeRecord;

// 订单充值
class OrderChargeRepository
{
    public static function dataList($orderNo, $status)
    {
        $dataList = OrderCharge::orderBy('created_at', 'desc')
            ->when(!empty($orderNo), function ($query) use ($orderNo) {
                return $query->where('order_no', $orderNo);
            })
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->paginate(30);

        return $dataList;
    }

    public static function detail($orderNo)
    {
        $dataList = OrderChargeRecord::where('order_no', $orderNo)->paginate(30);

        return $dataList;
    }
}
