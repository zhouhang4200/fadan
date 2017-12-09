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
     * @param int $condition
     * @param int $pageSize
     * @return mixed
     */
    public function dataList($condition, $pageSize = 20)
    {
        return AfterService::filter($condition)->paginate($pageSize);
    }
}
