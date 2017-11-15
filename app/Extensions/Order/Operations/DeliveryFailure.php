<?php
namespace App\Extensions\Order\Operations;

use App\Exceptions\OrderException as Exception;
use App\Models\SiteInfo;
use App\Models\Weight;
use App\Services\KamenOrderApi;

// 发货失败
class DeliveryFailure extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [3];
    protected $handledStatus    = 5;
    protected $type             = 5;
    protected $remark;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo, $userId, $remark = '')
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
        $this->remark  = $remark;
    }

    public function setDescription()
    {
        $statusName = config('order.status')[$this->handledStatus];
        $this->description = "用户[{$this->userId}]设置订单为[$statusName]状态，原因：{$this->remark}";
    }

    public function saveWeight()
    {
        $weight = Weight::where('order_no', $this->order->no)->orderBy('id', 'desc')->first();
        $weight->order_end_time = $this->order->updated_at;
        $weight->order_use_time = ($this->order->updated_at->diffInSeconds($weight->order_in_time));
        $weight->is_time_out    = ($weight->order_use_time > config('order.max_use_time') ? 1 : 0);
        $weight->status         = 2;
        $weight->remark         = $this->remark;

        if (!$weight->save()) {
            throw new Exception('权重凭证保存失败');
        }
        $this->runAfter = true;
    }

    /**
     * 如果是千手自营还需向卡门发送失败通知
     */
    public function after()
    {
        if ($this->runAfter) {
            // 调用打款，删除自动打款哈希表中订单号
            $has = SiteInfo::where('user_id', $this->order->creator_primary_user_id)->first();

            if ($this->order->foreignOrder && $has) {
                KamenOrderApi::share()->fail($this->order->foreignOrder->kamen_order_no);
            }
        }
    }
}
