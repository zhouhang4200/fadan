<?php

namespace App\Listeners\OrderRevoking;

use App\Events\OrderFinish;
use App\Events\OrderRevoking;
use App\Models\SmsTemplate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 订单撤销中发送短信通知，只有订单类型为代练是才发送
 * Class tb
 * @package App\Listeners\OrderRevoking
 */
class SendSms
{
    /**
     * Handle the event.
     *
     * @param  OrderFinish  $event
     * @return void
     */
    public function handle(OrderRevoking $event)
    {
        // 如果订单类型为代练，则找出订单客户订单号找出商户设置的模版发送短信
        if ($event->order->service_id == 4) {
            // 获取商户设置的模板
            $template = SmsTemplate::where('user_id', $event->order->creator_primary_user_id)
                ->where('status', 1)
                ->where('purpose', 4)
                ->first();
            if ($template) {
                $detail = $event->order->detail->pluck('field_value', 'field_name');
                if (isset($detail['client_phone']) && $detail['client_phone']) {
                    // 发送短信
                    sendSms($event->order->creator_primary_user_id,
                        $event->order->no,
                        $detail['client_phone'],
                        $template->contents,
                        '代练订单撤销短信',
                        $detail['source_order_no'],
                        $detail['third_order_no'],
                        $detail['third']
                    );
                }
            }
        }
    }
}
