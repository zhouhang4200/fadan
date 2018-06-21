<?php
namespace App\Repositories\Backend;

use App\Models\ProcessOrder;

class ProcessOrderRepository
{
    public static function getList($orderNo, $userId)
    {
        $dataList = ProcessOrder::orderBy('id')
            ->when($orderNo, function ($query) use ($orderNo) {
                return $query->where('order_no', $orderNo);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->paginate(20);

        return $dataList;
    }
}
