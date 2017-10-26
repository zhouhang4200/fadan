<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationEvent  implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * 发送给谁
     * @var
     */
    private $to;

    /**
     * 通知的数据
     * @var
     */
    private $data;

    /**
     * NotificationEvent constructor.
     * @param $to
     * @param $data
     */
    public function __construct($to, $data)
    {
        $this->to = $to;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['notification'];
    }
    /**
     * Get the name the event should be broadcast on.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return $this->to;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->data;
    }
}
