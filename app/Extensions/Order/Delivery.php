<?php
namespace App\Extensions\Order;

// 发货
class Delivery extends \App\Extensions\Order\Base\Operation
{
    protected $acceptableStatus = [2];
    protected $handledStatus    = 3;
    protected $type             = 3;

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
