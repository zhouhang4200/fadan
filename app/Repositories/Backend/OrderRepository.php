<?php
namespace App\Repositories\Backend;

use DB, Auth, Excel;
use Carbon\Carbon;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        return Order::filter($filter)->orderBy('id', 'desc')
            ->with(['gainerPrimaryUser'])
            ->paginate($pageSize);
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

        $response = new StreamedResponse(function () use ($order){
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
            fputcsv($out, [
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
                '渠道名',
                '状态',
                '原因',
                '下单时间',
            ]);
            $order->chunk(1000, function ($items) use ($out) {
                $orders = $items->toArray();

                foreach ($orders as $k => $v) {

                    $status = '';
                    if ($v['status']) {
                        $status = isset(config('order.status')[$v['status']]) ? config('order.status')[$v['status']] : config('order.status')[$v['status_leveling']];
                    }
                    if ($v['source']) {
                        $v['source'] = config('order.source')[$v['source']];
                    }
                    $reason = '';
                    if (isset($v['history'])) {
                        if (in_array($v['status'], [5, 10])) {
                            $count = count($v['history']) - 1;
                            $temp = explode('，', $v['history'][$count]['description']);
                            $reason = $temp[1] ?? '';
                        }
                    }
                    // 订单详情
                    $detail = collect($v['detail'])->pluck( 'field_value','field_name');
                    $data = [
                        $v['creator_primary_user_id'],
                        $v['gainer_primary_user_id'],
                        $v['gainer_user']['nickname'] ?? '',
                        $v['no']  . "\t",
                        $v['foreign_order_no']  . "\t",
                        $v['game_name'],
                        $v['goods_name'],
                        $detail['account'] ?? '',
                        $detail['version'] ?? '',
                        $v['quantity'],
                        $v['price'],
                        $v['amount'],
                        $v['original_price'],
                        $v['original_amount'],
                        $v['source'],
                        $v['foreign_order']['channel_name'] ?? '',
                        $status,
                        $reason,
                        $v['created_at']
                    ];
                    fputcsv($out, $data);
                }
            });
            fclose($out);
        },200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="平台订单导出.csv"',
        ]);
        $response->send();
    }
}
