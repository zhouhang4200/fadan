<?php

namespace App\Listeners\Logined;

use Illuminate\Auth\Events\Logined;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteLoginSession
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
     * @param  Logined  $event
     * @return void
     */
    public function handle(Logined $event)
    {
        // $user = User::where('name', $request->name)->first();
        
        // $user = $event->user;

        // if ($user) {

        //     $sessionId = \Redis::get("user:$user->id");

        //     if ($sessionId) {

        //         \Redis::del($sessionId);
        //     }

        //     \Redis::set("user:$user->id", session()->getId());
        // }
    }
}
