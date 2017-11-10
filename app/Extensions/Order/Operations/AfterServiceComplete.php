<?php
namespace App\Extensions\Order\Operations;

use Asset;
use App\Extensions\Asset\Income;

// 完成售后
class AfterServiceComplete extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [6];
    protected $handledStatus    = 7;
    protected $type             = 7;

    /**
     * @param string $orderNo 订单编号
     * @param int $adminUserId 管理员id
     * @param int $refundFee 退款金额
     * @param int $description 备注
     */
    public function __construct($orderNo, $adminUserId, $refundFee, $description = '')
    {
        $this->orderNo     = $orderNo;
        $this->adminUserId = $adminUserId;
        $this->refundFee   = $refundFee;
        $this->description = $description;
    }

    public function updateAsset()
    {
        // 退款给买家
        Asset::handle(new Income($this->refundFee, Income::TRADE_SUBTYPE_AFTER_SERVICE, $this->order->no, '下单售后退款', $this->order->creator_primary_user_id, $this->adminUserId));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new Exception('操作失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new Exception('操作失败');
        }

        // 退款给卖家
        $sellerRefundFee = bcsub($this->order->amount, $this->refundFee);
        Asset::handle(new Income($sellerRefundFee, Income::TRADE_SUBTYPE_AFTER_SERVICE, $this->order->no, '接单售后退款', $this->order->gainer_primary_user_id, $this->adminUserId));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            throw new Exception('操作失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            throw new Exception('操作失败');
        }
    }

    public function setDescription()
    {
        $statusName = config('order.status')[$this->handledStatus];
        $this->description = "管理员[{$this->adminUserId}]设置订单为[{$statusName}]状态。" . $this->description;
    }
}
