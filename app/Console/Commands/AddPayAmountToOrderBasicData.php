<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Order;
use App\Models\OrderBasicData;
use Illuminate\Console\Command;

class AddPayAmountToOrderBasicData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:pay_amount:order_basic_datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'order_basic_datas表的pay_amount字段增加数据';

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
        $orderBasicDatas = OrderBasicData::chunk(5000, function ($chunk) {
            foreach ($chunk as $orderBasicData) {
                try {
                    $order = Order::where('no', $orderBasicData->order_no)->first();

                    if ($order->status == 20) {
                        $orderBasicData->pay_amount = $order->price;
                    } else {
                        $orderBasicData->pay_amount = 0;
                    }
                    $orderBasicData->save();
                } catch (Exception $e) {
                    myLog('order-basic-datas-pay_amount', ['order_no' => $orderBasicData->order_no ?? '', '失败原因' => $e->getMessage()]);
                }
            }
        });
    }
}
