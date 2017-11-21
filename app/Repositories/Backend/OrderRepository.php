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
            'creator_primary_user_id',
            'gainer_primary_user_id',
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
            'created_at',
        ])
            ->filter($filters)
            ->with(['gainerUser', 'detail', 'history', 'foreignOrder']);

        return Excel::create('订单数据', function ($excel) use($order) {
            $excel->sheet('Sheet1', function ($sheet) use ($order) {
                $sheet->setAutoSize(true);
                $sheet->row(1, array(
                    '买家',
                    '卖家',
                    '卖家ID备注',
                    '订单号',
                    '外部订单号',
                    '游戏',
                    '商品名',
                    '账号',
                    '版本',
                    '数量',
                    '单价',
                    '总价',
                    '天猫单价',
                    '天猫总价',
                    '渠道',
                    '状态',
                    '原因',
                    '下单时间',
                ));
                $order->chunk(1000, function ($items) use ($sheet) {
                    $orders = $items->toArray();
                    $data = [];
                    foreach ($orders as $k => $v) {
                        $status = '';
                        if ($v['status']) {
                            $status = config('order.status')[$v['status']];
                        }
                        if ($v['source']) {
                            $v['source'] = config('order.source')[$v['source']];
                        }
                        $reason = '';
                        if (isset($v['history']['description'])) {
                            if (in_array($v['status'], [5, 10])) {
                                $count = count($v['history']);
                                $temp = explode(',', $v['history'][$count]['description']);
                                $reason = $temp[1] ?? '-';
                            }
                        }
                        // 订单详情
                        $detail = collect($v['detail'])->pluck( 'field_value','field_name');
                        $data[] = [
                            $v['creator_primary_user_id'],
                            $v['gainer_primary_user_id'],
                            $v['gainerUser']['nickname'] ?? '',
                            $v['no'],
                            $v['foreign_order_no'],
                            $v['game_name'],
                            $v['goods_name'],
                            $detail['account'] ?? '',
                            $detail['version'] ?? '',
                            $v['quantity'],
                            $v['price'],
                            $v['amount'],
                            $v['original_price'],
                            $v['original_amount'],
                            $v['foreignOrder']['channel_name'] ?? '',
                            $status,
                            $reason,
                            $v['created_at']
                        ];
                    }
                    $sheet->fromArray($data, null, 'A2', false, false);
                });
            });
        })->export('xls');
    }
}
