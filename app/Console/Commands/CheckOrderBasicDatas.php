<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Models\OrderBasicData;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;

class CheckOrderBasicDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:order-basic-datas';

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
        $numberOriginal = 0;
        $numberIncome = 0;
        $orderBasicDatas = OrderBasicData::get();

        foreach($orderBasicDatas as $orderBasicData) {
            try {
                $originalPrice = $orderBasicData->original_price;
                $tmIncome = $orderBasicData->tm_income;

                 // 来源单号和天猫单号
                $sourceOrderNos = OrderDetail::where('order_no', $orderBasicData->order_no)
                    ->where('field_name_alias', 'source_order_no')
                    ->where('field_value', '!=', '')
                    ->pluck('field_value')
                    ->unique()
                    ->toArray();

                // 天猫数据
                $realTmIncome = 0;
                $realOriginalPrice = 0;
                if (isset($sourceOrderNos) && ! empty($sourceOrderNos) && is_array($sourceOrderNos) && count($sourceOrderNos) > 0) {
                    foreach ($sourceOrderNos as $sourceOrderNo) {
                        $tmOrder = TaobaoTrade::where('tid', $sourceOrderNo)->first();
                        // 真实天猫退款
                        if (isset($tmOrder) && ! empty($tmOrder) && $tmOrder->trade_status == 7) {
                            $realTmIncome += $tmOrder->payment;
                        }
                        // 真实天猫来源价格
                        if (isset($tmOrder) && ! empty($tmOrder)) {
                            $realOriginalPrice += $tmOrder->payment;
                        }
                    }
                }

                if (empty($sourceOrderNos)) {
                    $order = Order::where('no', $orderBasicData->order_no)->first();
                    $realOriginalPrice = $order->original_price;
                }

                // 对比
                if ($originalPrice != $realOriginalPrice) {
                    $orderBasicData->original_price = $realOriginalPrice;
                    $orderBasicData->save();
                    myLog('unsame-order-basic-datas-original', ['order_no' => $orderBasicData->order_no ?? '没取到']);
                    $numberOriginal++;
                    echo $numberOriginal.PHP_EOL;
                }
                
                if ($tmIncome != $realTmIncome) {
                    $orderBasicData->tm_income = $realTmIncome;
                    $orderBasicData->save();
                    myLog('unsame-order-basic-datas-tmincome', ['order_no' => $orderBasicData->order_no ?? '没取到']);
                    $numberIncome++;
                    echo $numberIncome.PHP_EOL;
                }
            } catch (Exception $e) {
                myLog('check-order-basic-datas', ['order_no' => $orderBasicData->order_no ?? '没取到', 'message' => $e->getMessage()]);
            }
        }
    }
}
