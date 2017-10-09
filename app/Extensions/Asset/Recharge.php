<?php
namespace App\Extensions\Asset;

use App\Exceptions\AssetException as Exception;

use App\Models\UserAsset;
use App\Models\UserAmountFlow;
use App\Models\PlatformAsset;
use App\Models\PlatformAmountFlow;

// 加款
class Recharge extends \App\Extensions\Asset\Base\Trade
{
    const TRADE_SUBTYPE_AUTO   = 1; // 自动加款
    const TRADE_SUBTYPE_MANUAL = 2; // 手动加款

    protected $userAsset;
    protected $platformAsset;

    // 更新用户余额
    public function updateUserAsset()
    {
        $this->userAsset = UserAsset::where('user_id', $this->userId)->lockForUpdate()->first();
        if (empty($this->userAsset)) {
            throw new Exception('用户资产不存在');
        }

        $this->userAsset->balance        = bcadd($this->userAsset->balance, $this->fee);
        $this->userAsset->total_recharge = bcadd($this->userAsset->total_recharge, $this->fee);

        if (!$this->userAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }

    // 写用户流水
    public function writeUserAmountFlow()
    {
        $userAmountFlow = new UserAmountFlow;
        $userAmountFlow->user_id       = $this->userId;
        $userAmountFlow->trade_type    = self::TRADE_TYPE_RECHARGE;
        $userAmountFlow->trade_subtype = self::TRADE_TYPE_RECHARGE . $this->subtype;
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

    // 更新平台资金
    public function updatePlatformAsset()
    {
        $this->platformAsset = PlatformAsset::where('id', 1)->lockForUpdate()->first();
        if (empty($this->platformAsset)) {
            throw new Exception('平台资产不存在');
        }

        $this->platformAsset->balance        = bcadd($this->platformAsset->balance, $this->fee);
        $this->platformAsset->total_recharge = bcadd($this->platformAsset->total_recharge, $this->fee);

        if (!$this->platformAsset->save()) {
            throw new Exception('数据更新失败');
        }

        return true;
    }

    // 写平台流水
    public function writePlatformAmountFlow()
    {
        echo 'writePlatformAmountFlow<br />';
    }
}
