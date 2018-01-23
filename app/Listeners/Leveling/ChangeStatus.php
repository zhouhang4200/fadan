<?php

namespace App\Listeners\Leveling;

use App\Services\Show91;
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
        $third = $order->detail()->where('field_name', 'third')->value('field_value');

        switch ($third) {
            case 1: // 91平台
                $this->requestShow91Interface($order, $name, $bool);
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

        if (! $third) {
            $this->requestShow91Interface($order, $name, $bool);
        }
    }

    /**
     * 91接口
     * @param  [type] $order [description]
     * @param  [type] $name  [description]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public function requestShow91Interface($order, $name, $bool)
    {
        return call_user_func_array([Show91::class, $name], [$order, $bool]);
    }
}
