<?php
namespace App\Extensions\Asset\Traits;

use App\Models\UserAmountFlow;

trait UserAmountFlowTrait
{
    // 写用户流水
    public function writeUserAmountFlow()
    {
        $userAmountFlow                 = new UserAmountFlow;
        $userAmountFlow->user_id        = $this->userId;
        $userAmountFlow->admin_user_id  = $this->adminUserId;
        $userAmountFlow->trade_type     = $this->type;
        $userAmountFlow->trade_subtype  = $this->type . $this->subtype;
        $userAmountFlow->trade_no       = $this->no;
        $userAmountFlow->fee            = $this->fee;
        $userAmountFlow->remark         = $this->remark;
        $userAmountFlow->balance        = $this->userAsset->balance;
        $userAmountFlow->frozen         = $this->userAsset->frozen;
        $userAmountFlow->total_recharge = $this->userAsset->total_recharge;
        $userAmountFlow->total_withdraw = $this->userAsset->total_withdraw;
        $userAmountFlow->total_consume  = $this->userAsset->total_consume;
        $userAmountFlow->total_refund   = $this->userAsset->total_refund;
        $userAmountFlow->total_expend   = $this->userAsset->total_expend;
        $userAmountFlow->total_income   = $this->userAsset->total_income;
        $userAmountFlow->created_at     = date('Y-m-d H:i:s');

        if (!$userAmountFlow->save()) {
            throw new Exception('流水记录失败');
        }

        return true;
    }
}
