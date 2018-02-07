<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 根据第三方平台调对应平台接口事件
 */
class AutoRequestInterface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // 订单 object
    public $order;
    // 接口方法名字
    public $name;
    // 传入接口的参数, true or false , 当true时，表示调用接口的第二种操作, false 为调用接口的第一种操作
    // 有的一个接口里面存在两种操作
    public $bool;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, $name, $bool = false)
    {
        $this->order = $order;
        $this->name = $name;
        $this->bool = $bool;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
