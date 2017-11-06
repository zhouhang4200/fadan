<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\NotificationEvent;
use App\Exceptions\CustomException;
use App\Extensions\Order\Operations\Receiving;

use Log, Config, Weight, Order;

/**
 * Class OrderTestData
 * @package App\Console\Commands
 */
class OrderTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:TestData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order TestData.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        for ($i =1; $i<=10000; $i++) {
            $data = [];
            for ($a = 1; $a<=1000; $a++) {
                $data[] = [
                    "no" => generateOrderNo(),
                    "foreign_order_no" => generateOrderNo(),
                    "source" => 1,
                    "status" => rand(1, 11),
                    "goods_id" => rand(1, 3999),
                    "goods_name" => str_random(30),
                    "service_id" => rand(1, 11),
                    "service_name" => rand(1, 11),
                    "game_id" => rand(1, 11),
                    "game_name" => rand(1, 11),
                    "original_price" => rand(1, 11),
                    "price" => rand(1, 66),
                    "quantity" => rand(1, 4),
                    "original_amount" => rand(1, 4),
                    "amount" => rand(1, 4),
                    "remark" => rand(1, 4),
                    "creator_user_id" => rand(1, 10),
                    "creator_primary_user_id" => rand(10, 20),
                    "gainer_user_id" => rand(1, 10),
                    "gainer_primary_user_id" => rand(1, 20),
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s'),
                ];
            }
            \DB::table('orders')->insert($data);
        }
    }
}
