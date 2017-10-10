<?php
namespace App\Extensions\Asset\Traits;

use App\Models\PlatformAmountFlow;

trait PlatformAmountFlowTrait
{
    // 写平台流水
    public function writePlatformAmountFlow()
    {
        $platformAmountFlow = new PlatformAmountFlow;
        $platformAmountFlow->user_id       = $this->userId;
        $platformAmountFlow->trade_type    = $this->type;
        $platformAmountFlow->trade_subtype = $this->type . $this->subtype;
        $platformAmountFlow->trade_no      = $this->no;
        $platformAmountFlow->fee           = $this->fee;
        $platformAmountFlow->balance       = $this->userAsset->balance;
        $platformAmountFlow->frozen        = $this->userAsset->frozen;
        $platformAmountFlow->remark        = $this->remark;
        $platformAmountFlow->created_at    = date('Y-m-d H:i:s');

        if (!$platformAmountFlow->save()) {
            throw new Exception('流水记录失败');
        }

        return true;
    }
}
