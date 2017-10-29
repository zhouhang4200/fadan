<?php
namespace App\Extensions\Order\Operations;

use Asset;
use App\Extensions\Asset\Income;

// 发货失败
class DeliveryFailure extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [3];
    protected $handledStatus    = 5;
    protected $type             = 5;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo, $userId)
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
    }
}
