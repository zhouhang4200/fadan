<?php
namespace App\Repositories\Frontend;

use App\Models\OrderHistory;
use Auth;
use App\Models\Order;

/**
 * Class OrderRepository
 * @package App\Repositories\Frontend
 */
class OrderHistoryRepository
{
    public static function dataList($orderNo)
    {
        $primaryUserId = Auth::user()->getPrimaryUserId();
        $dataList = OrderHistory::where('order_no', $orderNo)
            ->where('creator_primary_user_id', $primaryUserId)
            ->with('user')
            ->get();

        return $dataList;
    }
}
