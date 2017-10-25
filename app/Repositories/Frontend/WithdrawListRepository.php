<?php
namespace App\Repositories\Frontend;

use DB;
use Auth;
use App\Exceptions\CustomException as Exception;
use App\Models\WithdrawList;
use Asset;
use App\Extensions\Asset\Freeze;

class WithdrawListRepository
{
    /**
     * 申请提现
     * @return mixed
     */
    public function apply($fee, $remark)
    {
        DB::beginTransaction();

        $withdraw = new WithdrawList;
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
