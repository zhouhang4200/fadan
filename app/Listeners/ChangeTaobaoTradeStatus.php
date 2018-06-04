<?php

namespace App\Listeners;

use App\Models\TaobaoTrade;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 变更淘宝订单状态
 * 已撤销、已仲裁、已撤单、强制撤销订单
 * 如果对应淘宝订单没有变为“已退款/交易成功”
 * 则该淘宝订单变为“待发单”状态
 * Class ChangeTaobaoTradeStatus
 * @package App\Listeners
 */
class ChangeTaobaoTradeStatus
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // 订单状态为: 已撤销、已仲裁、已撤单、强制撤销订单
        if (in_array($event->order->status, [19, 23, 21, 24])) {
            $taobaoTrade = TaobaoTrade::where('tid', $event->order->foreign_order_no)->first();
            // 如果对应淘宝订单没有变为“已退款/交易成功
            if ($taobaoTrade && !in_array($taobaoTrade->trade_status, [2, 7])) {
                $taobaoTrade->handle_status = 0;
                $taobaoTrade->save();
            }
        }
    }
}
