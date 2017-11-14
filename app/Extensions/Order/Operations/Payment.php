<?php
namespace App\Extensions\Order\Operations;

use App\Events\NotificationEvent;
use App\Exceptions\AssetException as Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Api\GoodsRepository;
use Asset;
use App\Extensions\Asset\Expend;

// 下单付款（用于下单时余额不足的情况）
class Payment extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [11];
    protected $handledStatus    = 1;
    protected $type             = 11;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo, $userId)
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
    }

    public function updateAsset()
    {
        Asset::handle(new Expend($this->order->amount, Expend::TRADE_SUBTYPE_ORDER_MARKET, $this->order->no, '下订单', $this->order->creator_primary_user_id));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new Exception('申请失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new Exception('申请失败');
        }
        $this->runAfter = true;
    }

    // 设置描述
    public function setDescription()
    {
        $this->description = "用户[{$this->userId}]支付了待付款订单";
    }

    /**
     * 付款成功后向所有用推送新订单通知
     */
    public function after()
    {
        if ($this->runAfter) {
            // 给所有用户推送新订单消息
            event(new NotificationEvent('NewOrderNotification', $this->order->toArray()));
            // 待接单数量加1
            waitReceivingQuantityAdd();
            // 更新前台待接单数量
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
        }
    }
}
