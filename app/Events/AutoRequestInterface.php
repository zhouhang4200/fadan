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

class AutoRequestInterface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // 订单 object
    public $order;
    // 接口方法名字
    public $name;
    // 传入接口的参数
    public $bool;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order, $name, $bool = 0)
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
