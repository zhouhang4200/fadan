<?php
namespace App\Extensions\Order\Operations;

use Asset;
use App\Extensions\Asset\Income;

// 发单后取消订单
class Cancel extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [1, 5];
    protected $handledStatus    = 10;
    protected $type             = 10;

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
        Asset::handle(new Income($this->order->amount, Income::TRADE_SUBTYPE_CANCLE, $this->order->no, '取消订单退款', $this->order->creator_primary_user_id));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('操作失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('操作失败');
        }
    }
}
