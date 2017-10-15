<?php
namespace App\Extensions\Asset\Base;

// 交易
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

    protected $fee;     // 交易金额
    protected $type;    // 交易类型
    protected $subtype; // 交易子类型
    protected $no;      // 交易单号
    protected $remark;  // 备注
    protected $userId;  // 用户ID

    public function __construct($userId, $fee, $subtype, $no = '', $remark  = '')
    {
        $this->userId  = $userId;
        $this->fee     = $fee;
        $this->subtype = $subtype;
        $this->no      = $no;
        $this->remark  = $remark;
    }

    // 用户前置操作
    public function beforeUser() {}

    // 更新用户余额
    abstract public function updateUserAsset();

    // 写用户流水
    abstract public function writeUserAmountFlow();

    // 平台前置操作
    public function beforePlatform() {}

    // 更新平台资金
    abstract public function updatePlatformAsset();

    // 写平台流水
    abstract public function writePlatformAmountFlow();

    // 后置操作
    public function after() {}
}
