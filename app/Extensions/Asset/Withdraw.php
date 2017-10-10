<?php
namespace App\Extensions\Asset;

use App\Exceptions\AssetException as Exception;
use App\Models\UserAsset;
use App\Models\PlatformAsset;
use App\Extensions\Asset\Traits\UserAmountFlowTrait;
use App\Extensions\Asset\Traits\PlatformAmountFlowTrait;

// 提现
class Withdraw extends \App\Extensions\Asset\Base\Trade
{
    use UserAmountFlowTrait, PlatformAmountFlowTrait;

    const TRADE_SUBTYPE_MANUAL = 1; // 手动提现

    protected $userAsset;
    protected $platformAsset;

    // 前置操作
    public function before() {
        // 数据验证
        if ($this->fee >= 0) {
            throw new Exception('金额必须是一个负数');
        }

        // 指定交易类型
        $this->type = self::TRADE_TYPE_WITHDRAW;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $this->userAsset = UserAsset::where('user_id', $this->userId)->lockForUpdate()->first();
        if (empty($this->userAsset)) {
            throw new Exception('用户资产不存在');
        }

        $afterFrozen = bcadd($this->userAsset->frozen, $this->fee);
        if ($afterFrozen < 0) {
            throw new Exception('用户冻结金额不足');
        }

        $this->userAsset->frozen         = $afterFrozen;
        $this->userAsset->total_withdraw = bcadd($this->userAsset->total_withdraw, abs($this->fee));

        if (!$this->userAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $this->platformAsset = PlatformAsset::where('id', 1)->lockForUpdate()->first();
        if (empty($this->platformAsset)) {
            throw new Exception('平台资产不存在');
        }

        $afterFrozen = bcadd($this->platformAsset->frozen, $this->fee);
        if ($afterFrozen < 0) {
            throw new Exception('平台冻结金额不足');
        }

        $this->platformAsset->frozen         = $afterFrozen;
        $this->platformAsset->total_withdraw = bcadd($this->platformAsset->total_withdraw, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }
}
