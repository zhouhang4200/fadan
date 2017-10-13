<?php

namespace App\Listeners\Registered;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\UserAsset;

class CreateUserAsset
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
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $userAsset = new UserAsset;
        $userAsset->user_id = $event->user->id;
        $userAsset->save();
    }
}
