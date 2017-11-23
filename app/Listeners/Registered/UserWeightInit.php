<?php

namespace App\Listeners\Registered;

use App\Models\UserWeight;
use Illuminate\Auth\Events\Registered;

class UserWeightInit
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
        if ($event->user->getTable() == 'users') {
            $userAsset = new UserWeight;
            $userAsset->user_id = $event->user->id;
            $userAsset->weight = 10;
            $userAsset->save();
        }
    }
}
