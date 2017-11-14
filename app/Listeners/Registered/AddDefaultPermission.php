<?php

namespace App\Listeners\Registered;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddDefaultPermission
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
        if ($event->user->parent_id == 0 && $event->user->getTable() == 'users') {
            
            $event->user->assignRole('home.qiantaimorenzu');
        }
    }
}
