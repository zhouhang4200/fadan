<?php
namespace App\Repositories\Backend;

use App\Models\UserRechargeOrder;
use Carbon\Carbon;

class UserRechargeOrderRepository
{
    public static function getList($timeStart, $timeEnd, $userId, $no, $type, $pageSize = 20)
    {
        $dataList = UserRechargeOrder::orderBy('id', 'desc')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('creator_primary_user_id', $userId);
            })
            ->when(!empty($type), function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when(!empty($no), function ($query) use ($no) {
                return $query->where('no', $no);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', Carbon::parse($timeEnd)->endOfDay());
            })
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }
}
