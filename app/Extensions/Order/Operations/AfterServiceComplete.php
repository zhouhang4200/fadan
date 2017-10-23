<?php
namespace App\Extensions\Order\Operations;

// 完成售后
class AfterServiceComplete extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [6];
    protected $handledStatus    = 7;
    protected $type             = 7;

    /**
     * @param string $orderNo 订单编号
     * @param int $adminUserId 管理员id
     */
    public function __construct($orderNo, $adminUserId, $description = '')
    {
        $this->orderNo = $orderNo;
        $this->adminUserId = $adminUserId;
        $this->description = $description;
    }

    public function setDescription()
    {
        $statusName = config('order.status')[$this->handledStatus];
        $this->description = "管理员[{$this->adminUserId}]设置订单为[{$statusName}]状态。" . $this->description;
    }
}
