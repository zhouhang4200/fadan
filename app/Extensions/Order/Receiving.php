<?php
namespace App\Extensions\Order;

use DB;
use App\Exceptions\OrderException as Exception;
use App\Models\User;

// 接单
class Receiving extends \App\Extensions\Order\Base\Operation
{
    protected $acceptableStatus = [1];
    protected $handledStatus    = 2;
    protected $type             = 2;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 接单人id
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

        $this->order->gainer_user_id         = $this->userId;
        $this->order->gainer_primary_user_id = $user->getPrimaryUserId();
    }

    public function setDescription()
    {
        $this->description = "订单已分配到用户[{$this->userId}]";
    }
}
