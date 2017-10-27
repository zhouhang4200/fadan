<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\Asset\Withdraw;
use App\Extensions\Asset\Unfreeze;
use DB;
use Asset;
use Auth;

class UserWithdrawOrder extends Model
{
    // 提现完成
    public function complete()
    {
        DB::beginTransaction();

        // 提现
        try {
            Asset::handle(new Withdraw($this->fee, Withdraw::TRADE_SUBTYPE_MANUAL, $this->no, '提现成功', $this->creator_primary_user_id, Auth::user()->id));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->status = 2;

        if (!$this->save()) {
            DB::rollback();
            throw new Exception('操作失败');
        }

        // 写多态关联
        if (!$this->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$this->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        DB::commit();
    }

    // 拒绝提现
    public function refuse()
    {
        DB::beginTransaction();

        // 解冻
        try {
            Asset::handle(new Unfreeze($this->fee, Unfreeze::TRADE_SUBTYPE_WITHDRAW, $this->no, '拒绝提现解冻', $this->creator_primary_user_id, Auth::user()->id));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $this->status = 3;

        if (!$this->save()) {
            DB::rollback();
            throw new Exception('操作失败');
        }

        // 写多态关联
        if (!$this->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$this->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        DB::commit();
    }

    public function userAmountFlows()
    {
        return $this->morphMany(UserAmountFlow::class, 'flowable');
    }

    public function platformAmountFlows()
    {
        return $this->morphMany(PlatformAmountFlow::class, 'flowable');
    }
}
