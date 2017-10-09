<?php
namespace App\Extensions\Amount;

use App\Exceptions\AmountException as Exception;

// 加款
class Recharge extends \App\Extensions\Amount\Base\Transaction
{
    // 更新用户余额
    public function updateUserAmount()
    {
        throw new Exception("asdfasf", 1);

        echo 'updateUserAmount<br />';
    }

    // 写用户流水
    public function writeUserFlow()
    {
        echo 'writeUserFlow<br />';
    }

    // 更新平台资金
    public function updatePlatformAmount()
    {
        echo 'updatePlatformAmount<br />';
    }

    // 写平台流水
    public function writePlatformFlow()
    {
        echo 'writePlatformFlow<br />';
    }
}
