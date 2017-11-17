<?php
namespace App\Extensions\Order\Operations;

// 关闭抢单
class GrabClose  extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [1];
    protected $handledStatus    = 2;
    protected $type             = 2;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo)
    {
        $this->orderNo = $orderNo;
    }
}
