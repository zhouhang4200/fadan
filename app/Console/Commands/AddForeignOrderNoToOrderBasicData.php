<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Order;
use App\Models\OrderBasicData;
use Illuminate\Console\Command;

class AddForeignOrderNoToOrderBasicData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:foreign_order_no:order_basic_datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将订单的外部订单号添加到订单基础表';

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
        $orderBasicDatas = OrderBasicData::get();

        foreach ($orderBasicDatas as $orderBasicData) {
            try {
                $order = Order::where('no', $orderBasicData->order_no)->first();

                if (isset($order) && ! empty($order)) {
                    $orderBasicData->foreign_order_no = $order->foreign_order_no;
                    $orderBasicData->save();
                }
            } catch (Exception $e) {
                MyLog('add:foreign_order_no:order_basic_datas', ['message' => $e->getMessage(), 'order_no' => $order->no ?? '']);
            }
        }
    }
}
