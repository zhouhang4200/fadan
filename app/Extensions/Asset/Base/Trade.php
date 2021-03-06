<?php
namespace App\Extensions\Asset\Base;

// 交易
use App\Exceptions\AssetException;
use App\Exceptions\CustomException;

abstract class Trade
{
    // 1.加款 2.提现 3.冻结 4.解冻 5.消费 6.退款 7.支出 8.收入
    const TRADE_TYPE_RECHARGE = 1;
    const TRADE_TYPE_WITHDRAW = 2;
    const TRADE_TYPE_FREEZE   = 3;
    const TRADE_TYPE_UNFREEZE = 4;
    const TRADE_TYPE_CONSUME  = 5;
    const TRADE_TYPE_REFUND   = 6;
    const TRADE_TYPE_EXPEND   = 7;
    const TRADE_TYPE_INCOME   = 8;

    protected $userId;  // 用户ID
    protected $fee;     // 交易金额
    protected $type;    // 交易类型
    protected $subtype; // 交易子类型
    protected $no;      // 交易单号
    protected $remark;  // 备注

    public function __construct($fee, $subtype, $no = '', $remark  = '', $userId, $adminUserId = 0)
    {
        if ($fee > -0.0001 && $fee < 0.0001) {
            throw new AssetException('金额太小');
        }

        // 判断是否存在科学计数法
        if (strpos(strtolower($fee), 'e')) {
            throw new AssetException('金额范围不正确');
        }

        $this->userId      = $userId;
        $this->adminUserId = $adminUserId;
        $this->fee         = $fee;
        $this->subtype     = $subtype;
        $this->no          = $no;
        $this->remark      = $remark;
    }

    // 用户前置操作
    public function beforeUser() {}

    // 更新用户余额
    abstract public function updateUserAsset();

    // 写用户流水
    abstract public function createUserAmountFlow();

    // 平台前置操作
    public function beforePlatform() {}

    // 更新平台资金
    abstract public function updatePlatformAsset();

    // 写平台流水
    abstract public function createPlatformAmountFlow();
}
