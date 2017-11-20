<?php

namespace App\Console\Commands;

use App\Extensions\Order\Operations\Cancel;
use App\Extensions\Order\Operations\GrabClose;
use Carbon\Carbon;
use App\Models\Order as OrderModel;
use Illuminate\Console\Command;
use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Receiving;

use Log, Config, Weight, Order;
use Symfony\Component\Console\Helper\Helper;

/**
 * 订单分配
 * Class OrderAssign
 * @package App\Console\Commands
 */
class OrderAssignTemp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:AssignTemp {no} {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单分配任务';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Order::handle(new GrabClose($this->argument('no')));
        Order::handle(new Receiving($this->argument('no'), $this->argument('user')));
    }
}
