<?php
namespace App\Extensions\Order;

// 完成售后
class AfterServiceComplete extends \App\Extensions\Order\Base\Operation
{
    protected $acceptableStatus = [5];
    protected $handledStatus    = 6;
    protected $type             = 6;

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
