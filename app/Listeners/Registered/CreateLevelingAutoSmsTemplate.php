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
            $template = [];
            foreach (config('sms.purpose') as $key => $value) {
                $template[] =  [
                    'user_id' => $event->user->id,
                    'name' => config('sms.purpose')[$key],
                    'contents' => config('sms.levelingTemplate')[$key],
                    'purpose' => $key,
                    'type' => 1,
                ];
            }
            SmsTemplate::insert($template);
        }
    }
}
