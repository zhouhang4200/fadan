<?php
namespace App\Repositories\Api\App;

use App\Models\Order;
use App\Exceptions\CustomException;
use Auth;
use Order as OrderForm;
use App\Extensions\Order\Operations\TurnBack;
use App\Extensions\Order\Operations\Delivery;
use App\Extensions\Order\Operations\DeliveryFailure;

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
            ->whereIn('service_id', [1, 3])
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
            ->whereIn('service_id', [1, 3])
            ->whereIn('status', [3, 4, 5, 6, 7, 8])
            ->select('no', 'source', 'status', 'goods_name', 'service_name', 'game_name', 'quantity', 'amount', 'remark', 'created_at', 'updated_at')
            ->with('orderCharge')
            ->first();

        if (empty($order)) {
            throw new CustomException('订单不存在');
        }

        $data = clone $order;

        // 构造详情
        $details = [];
        foreach ($order->detail as $detail) {
            $details[$detail->field_name] = $detail->field_value;
        }

        $data->details = collect($details);

        // 挂上充值信息
        if ($data->orderCharge) {
            $data->orderCharge->orderChargeRecords;
        }

        return $data;
    }

    // 返回集市
    public static function turnBack($orderNo, $remark)
    {
        self::checkAuth($orderNo);
        OrderForm::handle(new TurnBack($orderNo, Auth::guard('api')->user()->id, $remark));
        return true;
    }

    // 发货
    public static function delivery($orderNo)
    {
        self::checkAuth($orderNo);
        OrderForm::handle(new Delivery($orderNo, Auth::guard('api')->user()->id));
        return true;
    }

    // 发货失败
    public static function deliveryFailure($orderNo, $remark)
    {
        self::checkAuth($orderNo);
        OrderForm::handle(new DeliveryFailure($orderNo, Auth::guard('api')->user()->id, $remark));
        return true;
    }

    // 订单权限验证
    public static function checkAuth($orderNo)
    {
        $userId = Auth::guard('api')->user()->id;
        $primaryUserId = Auth::guard('api')->user()->getPrimaryUserId();

        $gainerPrimaryUserId = Order::where('no', $orderNo)->value('gainer_primary_user_id');
        if ($gainerPrimaryUserId != $primaryUserId) {
            throw new CustomException('订单不存在');
        }

        return true;
    }
}
