<?php

namespace App\Listeners\Leveling;

use App\Services\Show91;
use App\Models\OrderDetail;
use App\Events\AutoRequestInterface;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 监听根据第三方平台自动调相应第三方平台接口
 */
class ChangeStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  autoRequestInterface  $event
     * @return void
     */
    public function handle(autoRequestInterface $event)
    {
        $this->requestApiInterface($event->order, $event->name, $event->bool);
    }

    public function requestApiInterface($order, $name, $bool) 
    {
        $orderDetails = OrderDetail::where('order_no', $order->no)
                    ->pluck('field_value', 'field_name')
                    ->toArray();

        switch ($orderDetails['third']) {
            case 1: // 91平台
                call_user_func_array([Show91::class, $name], [$order, $bool]);
            break;
            case 2:
                return true;
            break;
            case 3:
                return true;
            break;
            case 4:
                return true;
            break;
        }

        if (! $orderDetails['third']) {
            call_user_func_array([Show91::class, $name], [$order, $bool]);
        }
    }
}
