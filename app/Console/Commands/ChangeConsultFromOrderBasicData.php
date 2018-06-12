<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Order;
use App\Models\LevelingConsult;
use App\Models\OrderDetail;
use App\Models\OrderBasicData;
use Illuminate\Console\Command;

class ChangeConsultFromOrderBasicData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:order-basic-datas:consult';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改基础表里面的撤销和仲裁数据';

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
        $orderBasicDatas = OrderBasicData::whereIn('status', [19, 21])->get();

        $count = 0;
        foreach ($orderBasicDatas as $orderBasicData) {
            try {
                $consult = LevelingConsult::where('order_no', $orderBasicData->order_no)->first();

                // 撤销信息
                if (isset($consult) && ! empty($consult)) {
                    if ($consult->complete == 1) {
                        $orderBasicData->consult_amount      = $consult->amount;
                        $orderBasicData->consult_deposit     = $consult->deposit;
                        $orderBasicData->consult_poundage    = $consult->api_service;
                    } elseif ($consult->complete == 2) {
                        $orderBasicData->consult_amount      = $consult->api_amount;
                        $orderBasicData->consult_deposit     = $consult->api_deposit;
                        $orderBasicData->consult_poundage    = $consult->api_service;
                    }
                    $count ++;
                    $orderBasicData->save();
                }
            } catch (Exception $e) {
                myLog('change-order-basic-datas-consult', ['message' => $e->getMessage()]);
            }
        }
        echo $count;
    }
}
