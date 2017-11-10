<?php
namespace App\Extensions\Order\Operations;

use App\Exceptions\OrderException as Exception;
use App\Models\Weight;

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
}
