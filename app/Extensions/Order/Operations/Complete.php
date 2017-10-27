<?php
namespace App\Extensions\Order\Operations;

use Asset;
use App\Extensions\Asset\Income;

// 设置完成
class Complete extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [4];
    protected $handledStatus    = 8;
    protected $type             = 9;

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
        Asset::handle(new Income($this->order->amount, Income::TRADE_SUBTYPE_ORDER_MARKET, $this->order->no, '订单完成', $this->order->gainer_primary_user_id));

        // 写多态关联
        if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }
    }
}
