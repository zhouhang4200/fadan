<?php
namespace App\Repositories\Api\App;

use App\Models\Order;
use Auth;

class OrderRepository
{
    /**
     * @param $status
     * @param int $perPage
     */
    public static function dataList($status, $page = 1, $perPage = 20)
    {
        $primaryUserId = Auth::guard('api')->user()->getPrimaryUserId(); // 当前账号的主账号

        $select = [
            'id',
            'no',
            'foreign_order_no',
            'source',
            'status',
            'goods_id',
            'goods_name',
            'service_id',
            'service_name',
            'game_id',
            'game_name',
            'original_price',
            'price',
            'quantity',
            'original_amount',
            'amount',
            'remark',
            'creator_user_id',
            'creator_primary_user_id',
            'gainer_user_id',
            'gainer_primary_user_id',
            'created_at',
        ];

        $dataList = Order::where('gainer_primary_user_id', $primaryUserId)
            ->where('status', $status)
            ->orderBy('id', 'desc')
            ->paginate($perPage, $select, 'page', $page);

        return $dataList;
    }

    /**
     * 订单详情
     * @param $orderNo
     */
    public function detail($orderNo)
    {
        $primaryUserId = Auth::guard('api')->user()->getPrimaryUserId();

        return Order::orWhere(function ($query) use ($orderNo, $primaryUserId) {
            $query->where(['creator_primary_user_id' => $primaryUserId, 'no' => $orderNo]);
        })->orWhere(function ($query)  use ($orderNo, $primaryUserId) {
            $query->where(['gainer_primary_user_id' => $primaryUserId, 'no' => $orderNo])->where('status', '>', 2);
        })->with(['detail', 'foreignOrder'])
        ->first();
    }
}
