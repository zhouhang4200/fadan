<?php
namespace App\Repositories\Backend;

use App\Models\UserWithdrawOrder;

class UserWithdrawOrderRepository
{
    public function getList($timeStart, $timeEnd, $userId, $no, $status, $pageSize = 20)
    {
        $dataList = UserWithdrawOrder::orderBy('creator_primary_user_id')->orderBy('id')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('creator_primary_user_id', $userId);
            })
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(!empty($no), function ($query) use ($no) {
                return $query->where('no', $no);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd);
            })
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->with('user')
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }
}
