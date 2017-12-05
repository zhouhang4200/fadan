<?php
namespace App\Extensions\Order\Operations;

use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Exceptions\OrderException as Exception;
use App\Models\User;
use App\Models\Weight;
use App\Services\FuluAppApi;

// 接单
class Receiving extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [2];
    protected $handledStatus    = 3;
    protected $type             = 3;

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
            throw new Exception('不存在的接单人');
        }

        $this->order->gainer_user_id         = $this->userId;
        $this->order->gainer_primary_user_id = $user->getPrimaryUserId();
    }

    public function setDescription()
    {
        $this->description = "订单已分配到用户[{$this->userId}]";
    }

    public function saveWeight()
    {
        $weight = new Weight;
        $weight->order_no                = $this->order->no;
        $weight->order_money             = $this->order->amount;
        $weight->creator_user_id         = $this->order->creator_user_id;
        $weight->creator_primary_user_id = $this->order->creator_primary_user_id;
        $weight->order_time              = $this->order->created_at;
        $weight->gainer_user_id          = $this->order->gainer_user_id;
        $weight->gainer_primary_user_id  = $this->order->gainer_primary_user_id;
        $weight->order_in_time           = $this->order->updated_at;

        if (!$weight->save()) {
            throw new Exception('权重凭证保存失败');
        }
        $this->runAfter = true;
    }

    /**
     * 后置操作
     */
    public function after()
    {
        if ($this->runAfter) {
            waitReceivingDel($this->order->no);
            // 待接单数量减1
            waitReceivingQuantitySub();
            // 待接单数量
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));

            // 删除接单
            try {
                receivingUserDel($this->order->no);
            } catch (CustomException $exception) {
                \Log::alert($exception->getMessage() . '删除接单队列');
            }

            // 如果是王者皮肤订单者并是APP订单则发送QQ号
            if ($this->order->game_id == 21 && $this->order->creator_primary_user_id == 8111) {
                try {
                    FuluAppApi::sendOrderAndQq($this->order->gainer_primary_user_id, $this->order->foreign_order_no);
                } catch(CustomException $exception) {
                    \Log::alert($exception->getMessage() . '给福禄APP发送QQ号异常，单号：' . $this->order->no);
                }
            }
        }
    }
}
