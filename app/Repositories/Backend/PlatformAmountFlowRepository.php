<?php
namespace App\Repositories\Backend;

use Exception;
use Carbon\Carbon;
use Auth;
use DB;
use App\Models\PlatformAmountFlow;

class PlatformAmountFlowRepository
{
    public function getList($userId, $tradeNo, $tradeType, $tradeSubType, $timeStart, $timeEnd, $pageSize = 20)
    {
        $dataList = PlatformAmountFlow::orderBy('id', 'desc')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when(!empty($tradeNo), function ($query) use ($tradeNo) {
                return $query->where('trade_no', $tradeNo);
            })
            ->when(!empty($tradeType), function ($query) use ($tradeType) {
                return $query->where('trade_type', $tradeType);
            })
            ->when(!empty($tradeSubType), function ($query) use ($tradeSubType) {
                return $query->where('trade_type', $tradeSubType);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd);
            })
            ->orderBy('id', 'desc')
            ->paginate($pageSize);

        return $dataList;
    }
}
