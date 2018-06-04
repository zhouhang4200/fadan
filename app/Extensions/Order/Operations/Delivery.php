<?php
namespace App\Extensions\Order\Operations;

use App\Exceptions\OrderException as Exception;
use App\Models\SiteInfo;
use App\Models\Weight;
use App\Services\KamenOrderApi;
use App\Services\SmSApi;

// 发货
class Delivery extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [3];
    protected $handledStatus    = 4;
    protected $type             = 4;

    /**
     * @param string $orderNo 订单编号
     * @param int $userId 操作人id
     */
    public function __construct($orderNo, $userId)
    {
        $this->orderNo = $orderNo;
        $this->userId  = $userId;
    }

    public function saveWeight()
    {
        $weight = Weight::where('order_no', $this->order->no)->orderBy('id', 'desc')->first();
        $weight->order_end_time = $this->order->updated_at;
        $weight->order_use_time = ($this->order->updated_at->diffInSeconds($weight->order_in_time));
        $weight->is_time_out    = ($weight->order_use_time > config('order.max_use_time') ? 1 : 0);
        $weight->status         = 1;

        if (!$weight->save()) {
            throw new Exception('权重凭证保存失败');
        }
    }

    public function after()
    {
        // 向卡门发送通知
        $has = SiteInfo::where('user_id', $this->order->creator_primary_user_id)->first();
        if ($this->order->foreignOrder && $has) {
            KamenOrderApi::share()->success($this->order->foreignOrder->kamen_order_no);
        }

        // 发送推广短信
        if (
            $this->order->foreignOrder
            && is_numeric($this->order->foreignOrder->tel)
            && strlen($this->order->foreignOrder->tel) == 11
            && $this->order->game_name == '王者荣耀'
        ) {
            SmSApi::sendTemplate($this->order->foreignOrder->tel);
        }
    }
}
