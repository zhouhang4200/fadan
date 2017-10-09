<?php
namespace App\Extensions\Amount\Base;

use Auth;

// 交易
abstract class Transaction
{
    protected $amount;
    protected $type;
    protected $subtype;
    protected $number;
    protected $remark;
    protected $userId;

    public function __construct(
        $amount  = null,
        $type    = null,
        $subtype = null,
        $number  = null,
        $remark  = null,
        $userId  = null
    )
    {
        $this->amount  = $amount;
        $this->type    = $type;
        $this->subtype = $subtype;
        $this->number  = $number;
        $this->remark  = $remark;
        $this->userId  = $userId ?: Auth::user()->id;
    }

    // 前置操作
    public function before() {}

    // 更新用户余额
    abstract public function updateUserAmount();

    // 写用户流水
    abstract public function writeUserFlow();

    // 更新平台资金
    abstract public function updatePlatformAmount();

    // 写平台流水
    abstract public function writePlatformFlow();

    // 后置操作
    public function after() {}
}
