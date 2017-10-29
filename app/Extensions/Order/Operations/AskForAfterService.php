<?php
namespace App\Extensions\Order\Operations;

// 申请售后服务
class AskForAfterService extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [4];
    protected $handledStatus    = 6;
    protected $type             = 6;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo, $userId, $description = '')
    {
        $this->orderNo     = $orderNo;
        $this->userId      = $userId;
        $this->description = $description;
    }

    public function setDescription()
    {
        $this->description = "用户[{$this->userId}]发起售后。原因：{{ $this->description }}";
    }
}
