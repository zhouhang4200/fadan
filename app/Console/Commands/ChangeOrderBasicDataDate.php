<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderBasicData;
use Illuminate\Console\Command;

class ChangeOrderBasicDataDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:order-basic-datas:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将基础表的数据按发单时间统计';

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
                // 按订单发布时间来更新日期
                $orderBasicData->date = $order->created_at->toDateTimeString();
                $orderBasicData->save();
            } catch (Exception $e) {
                myLog('change-order-basic-datas-date', ['message' => $e->getMessage()]);
            }
        }
    }
}
