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
            ->where('service_id', 1)
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->select('no', 'source', 'status', 'goods_name', 'service_name', 'game_name', 'quantity', 'amount', 'remark', 'created_at', 'updated_at')
            ->orderBy('id', 'desc')
            ->with(['detail' => function ($query) {
                $query->select('order_no', 'field_value', 'field_name');
            }])
            ->paginate($perPage, $select, 'page', $page)
            ->toArray();

        $dataFormat = [
            'current_page'   => $dataList['current_page'],
            'data'           => $dataList['data'],
            'last_page'      => $dataList['last_page'],
            'per_page'       => $dataList['per_page'],
            'total'          => $dataList['total'],
        ];

        foreach ($dataFormat['data'] as $key => $value) {
            // 构造详情
            $details = [];

            foreach ($value['detail'] as $v) {
                $details[$v['field_name']] = $v['field_value'];
            }

            $dataFormat['data'][$key]['detail'] = $details;
        }

        return $dataFormat;
    }

    /**
     * 订单详情
     * @param $orderNo
     */
    public static function detail($orderNo)
    {
        $primaryUserId = Auth::guard('api')->user()->getPrimaryUserId();

        $order = Order::where('no', $orderNo)
            ->where('gainer_primary_user_id', $primaryUserId)
            ->where('service_id', 1)
            ->whereIn('status', [3, 4, 5, 6, 7, 8])
            ->select('no', 'source', 'status', 'goods_name', 'service_name', 'game_name', 'quantity', 'amount', 'remark', 'created_at', 'updated_at')
            ->first();

        if ($order) {
            $data = clone $order;

            // 构造详情
            $details = [];
            foreach ($order->detail as $detail) {
                $details[$detail->field_name] = $detail->field_value;
            }

            $data->details = collect($details);
        } else {
            $data = null;
        }

        return $data;
    }
}
