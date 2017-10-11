<?php
namespace App\Extensions\Asset;

use App\Exceptions\AssetException as Exception;
use App\Models\UserAsset;
use App\Models\PlatformAsset;
use App\Extensions\Asset\Traits\UserAmountFlowTrait;
use App\Extensions\Asset\Traits\PlatformAmountFlowTrait;

// 消费
class Consume extends \App\Extensions\Asset\Base\Trade
{
    use UserAmountFlowTrait, PlatformAmountFlowTrait;

    const TRADE_SUBTYPE_BROKERAGE = 1; // 手续费

    protected $userAsset;
    protected $platformAsset;

    // 前置操作
    public function beforeUser() {
        $this->fee = -abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_CONSUME;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $this->userAsset = UserAsset::where('user_id', $this->userId)->lockForUpdate()->first();
        if (empty($this->userAsset)) {
            throw new Exception('用户资产不存在');
        }

        $afterBalance = bcadd($this->userAsset->balance, $this->fee);
        if ($afterBalance < 0) {
            throw new Exception('用户剩余金额不足');
        }

        $this->userAsset->balance       = $afterBalance;
        $this->userAsset->total_consume = bcadd($this->userAsset->total_consume, abs($this->fee));

        if (!$this->userAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }

    // 平台前置操作
    public function beforePlatform() {
        $this->fee = abs($this->fee);
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $this->platformAsset = PlatformAsset::where('id', 1)->lockForUpdate()->first();
        if (empty($this->platformAsset)) {
            throw new Exception('平台资产不存在');
        }

        $afterBalance = bcsub($this->platformAsset->balance, $this->fee);
        if ($afterBalance < 0) {
            throw new Exception('平台剩余金额不足');
        }

        $this->platformAsset->balance       = $afterBalance;
        $this->platformAsset->income        = bcadd($this->platformAsset->income, $this->fee);
        $this->platformAsset->total_consume = bcadd($this->platformAsset->total_consume, $this->fee);

        if (!$this->platformAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }
}
