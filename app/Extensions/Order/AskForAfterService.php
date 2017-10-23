<?php
namespace App\Extensions\Order;

// 申请售后服务
class AskForAfterService extends \App\Extensions\Order\Base\Operation
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
