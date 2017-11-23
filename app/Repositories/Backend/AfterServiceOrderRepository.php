<?php
namespace App\Repositories\Backend;

use DB, Auth;
use Carbon\Carbon;
use App\Models\AfterService;

/**
 * 售后订单
 * Class AfterServiceRepository
 * @package App\Repositories\Backend
 */
class AfterServiceRepository
{
    /**
     * @param string $status
     * @param string $orderNo
     * @param string $startDate
     * @param string $endDate
     * @param int $pageSize
     * @return mixed
     */
    public function dataList($status = '', $orderNo = '',$startDate = '', $endDate = '',  $pageSize = 20)
    {
        $query = AfterService::when(!empty($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        })->when(!empty($endDate), function ($query) use ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        })->when(!empty($orderNo), function ($query) use ($orderNo) {
            return $query->where('order_no', $orderNo);
        });
        return $query->paginate($pageSize);
    }
}
