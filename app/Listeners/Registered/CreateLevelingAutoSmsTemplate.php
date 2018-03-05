<?php

namespace App\Listeners\Registered;

use App\Models\SmsTemplate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class CreateLevelingAutoSmsTemplate
 * @package App\Listeners\Registered
 */
class CreateLevelingAutoSmsTemplate
{
    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if ($event->user->getTable() == 'users') {
            SmsTemplate::insert([
                [
                    'user_id' => $event->user->id,
                    'name' => config('sms.purpose')[1],
                    'contents' => config('sms.levelingTemplate')[1],
                    'purpose' => 1,
                    'type' => 1,
                ],
                [
                    'user_id' => $event->user->id,
                    'name' => config('sms.purpose')[2],
                    'contents' => config('sms.levelingTemplate')[2],
                    'purpose' => 2,
                    'type' => 1,
                ],
            ]);
        }
    }
}
