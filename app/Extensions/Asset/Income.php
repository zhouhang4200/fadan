<?php
namespace App\Extensions\Asset;

use App\Exceptions\AssetException as Exception;
use App\Models\UserAsset;
use App\Models\PlatformAsset;
use App\Extensions\Asset\Traits\UserAmountFlowTrait;
use App\Extensions\Asset\Traits\PlatformAmountFlowTrait;

// 交易收入（资金内部流动）
class Income extends \App\Extensions\Asset\Base\Trade
{
    use UserAmountFlowTrait, PlatformAmountFlowTrait;

    const TRADE_SUBTYPE_ORDER_MARKET     = 1; // 订单集市收入
    const TRADE_SUBTYPE_DELIVERY_FAILURE = 2; // 发货失败退款
    const TRADE_SUBTYPE_AFTER_SERVICE    = 3; // 售后退款
    const TRADE_SUBTYPE_CANCLE           = 4; // 取消订单退款

    const TRADE_SUBTYPE_GAME_LEVELING_CANCEL     = 5; // 代练撤消退款

    protected $userAsset;
    protected $platformAsset;

    // 前置操作
    public function beforeUser() {
        $this->fee = abs($this->fee);

        // 指定交易类型
        $this->type = self::TRADE_TYPE_INCOME;
    }

    // 更新用户余额
    public function updateUserAsset()
    {
        $this->userAsset = UserAsset::where('user_id', $this->userId)->lockForUpdate()->first();
        if (empty($this->userAsset)) {
            throw new Exception('用户资产不存在');
        }

        $this->userAsset->balance      = bcadd($this->userAsset->balance, $this->fee);
        $this->userAsset->total_income = bcadd($this->userAsset->total_income, $this->fee);

        if (!$this->userAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }

    // 平台前置操作
    public function beforePlatform() {
        $this->fee = -abs($this->fee);
    }

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $this->platformAsset = PlatformAsset::where('id', 1)->lockForUpdate()->first();
        if (empty($this->platformAsset)) {
            throw new Exception('平台资产不存在');
        }

        $afterManaged = bcadd($this->platformAsset->managed, $this->fee);
        if ($afterManaged < 0) {
            throw new Exception('平台资金不足');
        }

        $this->platformAsset->managed              = $afterManaged;
        $this->platformAsset->balance              = bcadd($this->platformAsset->balance, abs($this->fee));
        $this->platformAsset->total_trade_quantity = bcadd($this->platformAsset->total_trade_quantity, 1);
        $this->platformAsset->total_trade_amount   = bcadd($this->platformAsset->total_trade_amount, abs($this->fee));

        if (!$this->platformAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }
}
