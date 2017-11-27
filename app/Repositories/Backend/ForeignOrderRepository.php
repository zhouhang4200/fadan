<?php
namespace App\Repositories\Backend;

use App\Models\ForeignOrder;
use DB, Auth;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 外部订单
 * Class ForeignOrderRepository
 * @package App\Repositories\Backend
 */
class ForeignOrderRepository
{
    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $source
     * @param string $channelName
     * @param string $kamenOrderNo
     * @param string $foreignGoodsId
     * @param string $foreignOrderNo
     * @param int $pageSize
     * @return mixed
     */
    public function dataList($startDate = '',
                             $endDate = '',
                             $source = '',
                             $channelName = '',
                             $kamenOrderNo = '',
                             $foreignGoodsId = '',
                             $foreignOrderNo = '',
                             $pageSize = 20)
    {
        $query = ForeignOrder::when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        })->when(!empty($endDate), function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        })->when(!empty($source), function ($query) use ($source) {
            return $query->where('channel', $source);
        })->when(!empty($channelName), function ($query) use ($channelName) {
            return $query->where('channel_name', $channelName);
        })->when(!empty($kamenOrderNo), function ($query) use ($kamenOrderNo) {
            return $query->where('kamen_order_no', $kamenOrderNo);
        })->when(!empty($foreignGoodsId), function ($query) use ($foreignGoodsId) {
            return $query->where('foreign_goods_id', $foreignGoodsId);
        })->when(!empty($foreignOrderNo), function ($query) use ($foreignOrderNo) {
            return $query->where('foreign_order_no', $foreignOrderNo);
        });
        $query->orderBy('id', 'desc');
        return $query->paginate($pageSize);
    }
}
