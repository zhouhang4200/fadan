<?php
namespace App\Extensions\Order\Operations;

// 接单后，转回集市
class TurnBack extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [3];
    protected $handledStatus    = 1;
    protected $type             = 8;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo, $userId)
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
    }

    public function setAttributes()
    {
        $user = User::find($this->userId);
        if (empty($user)) {
            DB::rollback();
            throw new Exception('不存在的创建者');
        }

        $this->order->gainer_user_id         = null;
        $this->order->gainer_primary_user_id = null;
    }
}
