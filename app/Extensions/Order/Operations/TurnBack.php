<?php
namespace App\Extensions\Order\Operations;

use App\Events\NotificationEvent;
use App\Exceptions\OrderException as Exception;
use App\Models\SiteInfo;
use App\Models\User;
use App\Models\Weight;
use App\Services\KamenOrderApi;
use Carbon\Carbon;
use Order;
use Illuminate\Contracts\Logging\Log;

// 接单后，转回集市
class TurnBack extends \App\Extensions\Order\Operations\Base\Operation
{
    protected $acceptableStatus = [3];
    protected $handledStatus    = 1;
    protected $type             = 8;
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

    public function setAttributes()
    {
        $user = User::find($this->userId);
        if (empty($user)) {
            throw new Exception('用户不存在');
        }

        $this->order->gainer_user_id         = 0;
        $this->order->gainer_primary_user_id = 0;
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
        $weight->status         = 3;
        $weight->remark         = $this->remark;

        if (!$weight->save()) {
            throw new Exception('权重凭证保存失败');
        }
        $this->runAfter = true;
    }

    /**
     * 返回集市，如果超过40分钟自动失败
     */
    public function after()
    {
        if ($this->runAfter) {

            $carbon = new Carbon;
            $minutes = $carbon->diffInMinutes($this->order->created_at);

            if ($minutes >= 40) {
                // 超过40分钟失败
                Order::handle(new Cancel($this->order->no, 0));
                $has = SiteInfo::where('user_id', $this->order->creator_primary_user_id)->first();

                if ($this->order->foreignOrder && $has) {
                    KamenOrderApi::share()->fail($this->order->foreignOrder->kamen_order_no);
                }
                waitReceivingQuantitySub();
            } else {
                // 待接单数量加1
                waitReceivingQuantityAdd();
                // 待接单数量刷新
                event(new NotificationEvent('MarketOrderQuantity', ['quantity' => marketOrderQuantity()]));
                // 给所有用户推送新订单消息
                event(new NotificationEvent('NewOrderNotification', $this->order->toArray()));
                // 重写放入订单集市
                waitReceivingAdd($this->order->no,
                    Carbon::now('Asia/Shanghai')->addMinutes(1)->toDateTimeString(),
                    $this->order->created_at->toDateTimeString()
                );
            }
            // 如果订单存旺旺并关取了商户ID则删除关联关系
            if ($this->order->foreignOrder && $this->order->foreignOrder->wang_wang) {
                wangWangDeleteUserId($this->order->foreignOrder->wang_wang);
            }
        }
    }
}
