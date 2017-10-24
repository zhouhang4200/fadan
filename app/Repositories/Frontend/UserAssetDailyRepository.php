<?php
namespace App\Repositories\Frontend;

use Exception;
use Auth;
use DB;
use App\Models\UserAssetDaily;

class UserAssetDailyRepository
{
    public function getList($dateStart, $dateEnd, $pageSize = 20)
    {
        $dataList = UserAssetDaily::where('user_id', Auth::user()->getPrimaryUserId())
            ->when(!empty($dateStart), function ($query) use ($dateStart) {
                return $query->where('date', '>=', $dateStart);
            })
            ->when(!empty($dateEnd), function ($query) use ($dateEnd) {
                return $query->where('date', '<=', $dateEnd);
            })
            ->orderBy('date', 'desc')
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }
}
