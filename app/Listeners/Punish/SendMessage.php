<?php

namespace App\Listeners\Punish;

use App\Events\Punish;
use App\Models\PunishOrReward;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMessage
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
     * @param  Punish  $event
     * @return void
     */
    public function handle(Punish $event)
    {
        return PunishOrReward::where('user_id', $event->userId)
            ->where('type', 2)
            ->whereIn('status', ['3', '9'])
            ->first() ? true : false;
    }
}
