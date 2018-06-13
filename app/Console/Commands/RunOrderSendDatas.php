<?php

namespace App\Console\Commands;

use Redis;
use Exception;
use App\Models\Order;
use App\Services\RedisConnect;
use App\Models\User;
use App\Models\OrderDetail;
use Illuminate\Console\Command;

class RunOrderSendDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:order-send-datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $orders = Order::where('service_id', 4)
                ->where('created_at', '>=', '2018-06-09 19:29:42')
                ->where('created_at', '<=', '2018-06-12 22:21:45')
                ->get();

            // dd(count($orders->toArray()));

            foreach ($orders as $order) {
                $details = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name');
                $sendOrder = [
                    'order_no'                         => $order->no,
                    'game_name'                        => $order->game_name,
                    'game_region'                      => $details['region'] ?? '',
                    'game_serve'                       => $details['serve'] ?? '',
                    'game_role'                        => $details['role'] ?? '',
                    'game_account'                     => $details['account'] ?? '',
                    'game_password'                    => $details['password'] ?? '',
                    'game_leveling_type'               => $details['game_leveling_type'] ?? '',
                    'game_leveling_title'              => $details['game_leveling_title'] ?? '',
                    'game_leveling_price'              => $details['game_leveling_amount'] ?? '',
                    'game_leveling_day'                => $details['game_leveling_day'] ?? '',
                    'game_leveling_hour'               => $details['game_leveling_hour'] ?? '',
                    'game_leveling_security_deposit'   => $details['security_deposit'] ?? '',
                    'game_leveling_efficiency_deposit' => $details['efficiency_deposit'] ?? '',
                    'game_leveling_requirements'       => $details['game_leveling_requirements'] ?? '',
                    'game_leveling_instructions'       => $details['game_leveling_instructions'] ?? '',
                    'businessman_phone'                => $details['client_phone'] ?? '',
                    'businessman_qq'                   => $details['user_qq'] ?? '',
                    'order_password'                   => $details['order_password'] ?? '' ?? '',
                    'creator_username'                 => User::find($order->creator_primary_user_id)->username ?? '',
                ];
                $redis = RedisConnect::order();
                $redis->lpush('order:send', json_encode($sendOrder));
            }
        } catch (Exception $e) {
            myLog('run-order-send-datas', ['message' => $e->getMessage()]);
        }
    }
}
