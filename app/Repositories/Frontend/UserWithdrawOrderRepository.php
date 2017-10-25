<?php
namespace App\Repositories\Frontend;

use DB;
use Auth;
use App\Exceptions\CustomException as Exception;
use App\Models\UserWithdrawOrder;
use Asset;
use App\Extensions\Asset\Freeze;

class UserWithdrawOrderRepository
{
    public function getList($timeStart, $timeEnd, $status, $pageSize = 20)
    {
        $dataList = UserWithdrawOrder::where('creator_primary_user_id', Auth::user()->getPrimaryUserId())
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd);
            })
            ->orderBy('id', 'desc')
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }

    /**
     * 申请提现
     * @return mixed
     */
    public function apply($fee, $remark)
    {
        DB::beginTransaction();

        $withdraw = new UserWithdrawOrder;
        $withdraw->no                      = generateOrderNo();
        $withdraw->status                  = 1;
        $withdraw->fee                     = $fee;
        $withdraw->creator_user_id         = Auth::user()->id;
        $withdraw->creator_primary_user_id = Auth::user()->getPrimaryUserId();
        $withdraw->remark                  = $remark;

        if (!$withdraw->save()) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        try {
            Asset::handle(new Freeze($fee, Freeze::TRADE_SUBTYPE_WITHDRAW, $withdraw->no, $remark, $withdraw->creator_primary_user_id));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        DB::commit();
    }
}
