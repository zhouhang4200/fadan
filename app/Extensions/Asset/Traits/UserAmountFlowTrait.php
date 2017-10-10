<?php
namespace App\Extensions\Asset\Traits;

use App\Models\UserAmountFlow;

trait UserAmountFlowTrait
{
    // 写用户流水
    public function writeUserAmountFlow()
    {
        $userAmountFlow = new UserAmountFlow;
        $userAmountFlow->user_id       = $this->userId;
        $userAmountFlow->trade_type    = self::TRADE_TYPE_WITHDRAW;
        $userAmountFlow->trade_subtype = self::TRADE_TYPE_WITHDRAW . $this->subtype;
        $userAmountFlow->trade_no      = $this->no;
        $userAmountFlow->fee           = $this->fee;
        $userAmountFlow->balance       = $this->userAsset->balance;
        $userAmountFlow->frozen        = $this->userAsset->frozen;
        $userAmountFlow->remark        = $this->remark;
        $userAmountFlow->created_at    = date('Y-m-d H:i:s');

        if (!$userAmountFlow->save()) {
            throw new Exception('流水记录失败');
        }

        return true;
    }
}
