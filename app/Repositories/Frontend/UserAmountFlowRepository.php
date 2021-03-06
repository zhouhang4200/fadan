<?php
namespace App\Repositories\Frontend;

use Exception;
use Carbon\Carbon;
use Auth;
use DB;
use App\Models\Order;
use App\Models\UserAmountFlow;

class UserAmountFlowRepository
{
    public function getList($tradeNo, $tradeType, $tradeSubType, $timeStart, $timeEnd, $foreignOrderNo, $pageSize = 20)
    {
        $dataList = UserAmountFlow::where('user_id', Auth::user()->getPrimaryUserId())
            ->when(!empty($tradeNo), function ($query) use ($tradeNo) {
                return $query->where('trade_no', $tradeNo);
            })
            ->when(!empty($tradeType), function ($query) use ($tradeType) {
                if ($tradeType == 7) {
                    return $query->whereIn('trade_type', [5, 7]);
                } else if ($tradeType == 8) {
                    return $query->whereIn('trade_type', [6, 8]);
                } else {
                    return $query->where('trade_type', $tradeType);
                }
            })
            ->when(!empty($tradeSubType), function ($query) use ($tradeSubType) {
                return $query->where('trade_subtype', $tradeSubType);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd . ' 23:59:59');
            })
            ->when(!empty($foreignOrderNo), function ($query) use ($foreignOrderNo) {
                $orderNo = Order::where('foreign_order_no', $foreignOrderNo)->value('no') ?? '';
                return $query->where('trade_no', $orderNo);
            })
            ->with('order')
            ->orderBy('id', 'desc')
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }
}
