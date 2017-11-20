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
    protected $description = '生成测试数据';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $begin = new \DateTime( '2021-01-01' );
        $end = (new \DateTime( '2021-12-31' ))->modify( '+1 day' );

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($begin, $interval ,$end);

        foreach($dateRange as $date){
            $dateTime = $date->format("Y-m-d");
            $year = $date->format("Y");
            // 100 个用户
            for ($a = 1; $a<=100; $a++) {
                $data = [];
                // 每个用户二百单 1 是 发单人 接单人则加1
                for ($j = 1; $j <= 200; $j ++) {
                    $data[] = [
                        "no" => generateOrderNo(),
                        "foreign_order_no" => generateOrderNo(),
                        "source" => rand(1, 4),
                        "status" => rand(1, 10),
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
                        "creator_user_id" => $a,
                        "creator_primary_user_id" => $a,
                        "gainer_user_id" => $a+1,
                        "gainer_primary_user_id" => $a+1,
                        "created_at" => $dateTime,
                        "updated_at" => $dateTime,
                    ];
                }
                \DB::table('orders_p')->insert($data);
            }
        }
    }

}
