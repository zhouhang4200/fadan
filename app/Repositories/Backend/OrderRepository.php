<?php
namespace App\Repositories\Backend;

use DB, Auth, Excel;
use Carbon\Carbon;
use App\Models\Order;

/**
 * Class OrderRepository
 * @package App\Repositories\Frontend
 */
class OrderRepository
{
    /**
     * @param int $pageSize
     */
    public function dataList($filter, $pageSize = 15)
    {
        return Order::filter($filter)->orderBy('id', 'desc')->paginate($pageSize);
    }

    /**
     * 订单详情
     * @param $orderNo
     */
    public function detail($orderNo)
    {

    }

    /**
     * 订单数据导出
     * @param $filters
     * @return mixed
     */
    public function export($filters)
    {
        $order = Order::select([
            'no',
            'foreign_order_no',
            'source',
            'status',
            'service_name',
            'game_name',
            'goods_name',
            'quantity',
            'original_price',
            'original_amount',
            'price',
            'amount',
            'creator_primary_user_id',
            'gainer_primary_user_id',
            'created_at',
        ])->filter($filters);

        return Excel::create('订单数据', function ($excel) use($order) {
            $excel->sheet('Sheet1', function ($sheet) use ($order) {
                $sheet->setAutoSize(true);
                $sheet->row(1, array(
                    '集市订单号',
                    '外部订单号',
                    '来源',
                    '状态',
                    '服务',
                    '游戏',
                    '商品',
                    '数量',
                    '原单价',
                    '原总价',
                    '单价',
                    '总价',
                    '发单',
                    '接单',
                    '下单时间',
                ));
                $order->chunk(1000, function ($items) use ($sheet) {
                    $orders = $items->toArray();
                    foreach ($orders as $k => &$v) {
                        if ($v['status']) {
                            $v['status'] = config('order.status')[$v['status']];
                        }
                        if ($v['source']) {
                            $v['source'] = config('order.source')[$v['source']];
                        }
                    }
                    $sheet->fromArray($orders, null, 'A2', false, false);
                });
            });
        })->export('xls');
    }
}
