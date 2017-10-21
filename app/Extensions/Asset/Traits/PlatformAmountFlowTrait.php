<?php
namespace App\Extensions\Asset\Traits;

use App\Models\PlatformAmountFlow;
use App\Exceptions\AssetException as Exception;

trait PlatformAmountFlowTrait
{
    // 写平台流水
    public function createPlatformAmountFlow()
    {
        $platformAmountFlow = new PlatformAmountFlow;
        $platformAmountFlow->user_id              = $this->userId;
        $platformAmountFlow->admin_user_id        = $this->adminUserId;
        $platformAmountFlow->trade_type           = $this->type;
        $platformAmountFlow->trade_subtype        = $this->type . $this->subtype;
        $platformAmountFlow->trade_no             = $this->no;
        $platformAmountFlow->fee                  = $this->fee;
        $platformAmountFlow->remark               = $this->remark;
        $platformAmountFlow->amount               = $this->platformAsset->amount;
        $platformAmountFlow->managed              = $this->platformAsset->managed;
        $platformAmountFlow->balance              = $this->platformAsset->balance;
        $platformAmountFlow->frozen               = $this->platformAsset->frozen;
        $platformAmountFlow->total_recharge       = $this->platformAsset->total_recharge;
        $platformAmountFlow->total_withdraw       = $this->platformAsset->total_withdraw;
        $platformAmountFlow->total_consume        = $this->platformAsset->total_consume;
        $platformAmountFlow->total_refund         = $this->platformAsset->total_refund;
        $platformAmountFlow->total_trade_quantity = $this->platformAsset->total_trade_quantity;
        $platformAmountFlow->total_trade_amount   = $this->platformAsset->total_trade_amount;
        $platformAmountFlow->created_at           = date('Y-m-d H:i:s');

        if (!$platformAmountFlow->save()) {
            throw new Exception('流水记录失败');
        }

        return true;
    }
}
