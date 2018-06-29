<?php

namespace App\Listeners;

use Exception;
use App\Models\Order;
use App\Models\MobileOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderBasicData as OrderBasicDataModel;

class ChangeStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  OrderBasicData  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $order = Order::where('no', $event->order->no)->first();

            // APP那边人工渠道下单
            if ($order->source == 7) {
                $mobileOrder = MobileOrder::where('out_trade_no', $order->no)->first();

                if (isset($mobileOrder) && ! empty($mobileOrder)) {
                    $mobileOrder->status = $order->status;
                    $mobileOrder->save();
                }
            }
        } catch (\Exception $exception) {
            myLog('mobile-change-status', ['no' => $event->order->no ?? '', $exception->getMessage(), $exception->getFile(), $exception->getLine()]);
        }
    }
}
