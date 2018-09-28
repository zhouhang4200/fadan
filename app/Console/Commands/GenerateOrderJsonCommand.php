<?php

namespace App\Console\Commands;

use App\Models\ForeignOrder;
use App\Models\Order;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 生成卡门需要的订单json数据
 * Class GenerateOrderJsonCommand
 * @package App\Console\Commands
 */
class GenerateOrderJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GenerateOrderJsonCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成卡门需要的订单json数据';

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
        $binder = new CustomValueBinder();
        // 读取excel 文件
        $filePath = storage_path('/logs/order.xlsx');
        $data = Excel::setValueBinder($binder)->load($filePath)->toArray();

        $exportData = [];
        foreach ($data as $item) {
            $kamenOrderId = $item[0];
            // 查找订单获取价格
            $foreignOrderNo = ForeignOrder::where('kamen_order_no', $kamenOrderId)->value('foreign_order_no');

            if ($foreignOrderNo) {
                $amount =   Order::where('foreign_order_no', $foreignOrderNo)->value('amount');
            }

            if ($amount) {
                // 组装json
                $chargeUser = json_encode([
                    "channel_list" => [
                        [
                            "channel_id" =>  "a022d754-2e40-4835-b1f6-8bc70f77e83d",
                            "channel_account" =>  "订单集市",
                            "time" =>  date('Y-m-d H:i:s'),
                            "amount" =>  $amount,
                            "amount_type" =>  "RMB",
                        ]
                    ]
                ]);
                // 写入json
                $item[] = $chargeUser;
            }
            $exportData[] = $item;
        }

        Excel::create('new-order',function ($excel) use ($exportData){
            $excel->sheet('score', function ($sheet) use ($exportData){
                $sheet->rows($exportData);
            });
        })->store('xls', storage_path('logs'));

    }
}


