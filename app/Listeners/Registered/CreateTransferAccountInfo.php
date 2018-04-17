<?php

namespace App\Listeners\Registered;

use App\Models\SmsTemplate;
use App\Models\UserTransferAccountInfo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class CreateTransferAccountInfo
 * @package App\Listeners\Registered
 */
class CreateTransferAccountInfo
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
            UserTransferAccountInfo::create([
                    'user_id' => $event->user->id,
                    'name' => '武汉一起游网络科技有限公司',
                    'bank_name' => '中国民生银行武汉中南路支行',
                    'bank_card' => '698717103',
                    'type' => 1,
            ]);
        }
    }
}
