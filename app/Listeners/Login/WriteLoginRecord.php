<?php

namespace App\Listeners\login;

use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Models\AdminLoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteLoginRecord
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user->getTable() == 'admin_users') {

            AdminLoginHistory::writeLoginHistory(request()->ip());

        } else {

            LoginHistory::writeLoginHistory(request()->ip());
        }
    }
}
