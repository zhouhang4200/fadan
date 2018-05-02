<?php
namespace App\Extensions\Order\Operations;

use App\Events\NotificationEvent;
use App\Models\RefundsRecord;
use App\Models\SiteInfo;
use App\Services\KamenOrderApi;
use Asset;
use App\Extensions\Asset\Income;
use App\Exceptions\OrderException as Exception;

// 发单后取消订单
class Cancel extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [1, 5, 11];
    protected $handledStatus    = 10;
    protected $type             = 10;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     * @param int $adminUserId 操作管理员 id
     */
    public function __construct($orderNo, $userId, $adminUserId = 0)
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
        $this->adminUserId = $adminUserId;
    }

    public function updateAsset()
    {
        // 如果订单初始状态不是11(待支付)，则已支付过，需要退款
        if (unserialize($this->orderHistory->before)['status'] != 11) {
            Asset::handle(new Income($this->order->amount, Income::TRADE_SUBTYPE_CANCLE, $this->order->no, '取消订单退款', $this->order->creator_primary_user_id));

            // 写多态关联
            if (!$this->order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
                throw new Exception('操作失败');
            }

            if (!$this->order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
                throw new Exception('操作失败');
            }
        }
        $this->runAfter = true;
    }

    /**
     * 取消之后的操作
     */
    public function after()
    {
        if ($this->runAfter) {
            // 待接单数量减1
            waitReceivingQuantitySub();
            // 通知前端待接单数量的变化
            event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
            // 删除待分配中订单
            waitReceivingDel($this->order->no);
            // 失败卡门站点
            $has = SiteInfo::where('user_id', $this->order->creator_primary_user_id)->first();
            if ($this->order->foreignOrder && $has) {
                KamenOrderApi::share()->fail($this->order->foreignOrder->kamen_order_no);
            }
            // 创建退款单
            try {
                RefundsRecord::create([
                   'order_no' => $this->order->no,
                   'amount' => $this->order->amount,
                   'auditor' => 1,
                ]);
            } catch (\Exception $exception) {

            }
        }
    }
}
