<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException;

class TestOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试订单和订单详情还有订单申诉数据集合';

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
        $orderNo = '2018042815571700000014';

        $order = Order::where('no', $orderNo)->first();

        if (! isset($order) || empty($order)) {
            throw new DailianException('订单不存在');
        }

        $order = $order->toArray();

        $orderDetail = OrderDetail::where('order_no', $orderNo)->first();

        if (! isset($orderDetail) || empty($orderDetail)) {
            throw new DailianException('订单详情不存在');
        }

        $orderDetails = OrderDetail::where('order_no', $orderNo)->pluck('field_value', 'field_name')->toArray();

        $levelingConsult = LevelingConsult::where('order_no', $orderNo)->first();

        if (isset($levelingConsult) && ! empty($levelingConsult)) {
            $levelingConsult = $levelingConsult->toArray();
            $levelingConsult['pay_amount'] = $levelingConsult['amount'] ?? 0;
            unset($levelingConsult['amount']);
        } else {
            $levelingConsult = [];
        }
        // dd($levelingConsult);

        $arrMerge = array_merge($levelingConsult, $order);

        $merge = array_merge($arrMerge, $orderDetails);

        dd($arrMerge, $merge);

        // dd($orderDetails, $levelingConsult);
    }
}
