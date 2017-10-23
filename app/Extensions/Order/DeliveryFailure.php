<?php
namespace App\Extensions\Order;

// 发货失败
class DeliveryFailure extends \App\Extensions\Order\Base\Operation
{
    protected $acceptableStatus = [2];
    protected $handledStatus    = 4;
    protected $type             = 4;

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
