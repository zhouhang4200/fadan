<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderBasicData;
use App\Models\LevelingConsult;
use App\Models\TaobaoTrade;
use Illuminate\Console\Command;
use App\Models\BusinessmanComplaint;

class RunOrderBasicDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:order-basic-datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '运行所有单的数据到订单基础数据';

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
        // 所有的单
        $orders = Order::where('service_id', 4)->get();
        $number = 0;
        $error = 0;

        foreach ($orders as $k => $order) {
            try {
                $has = OrderBasicData::where('order_no', $order->no)->first();
                // 如果基础表已经有数据了，跳过
                if (isset($has) && ! empty($has)) {
                    continue;
                }
                // 订单详情
                $orderDetail = OrderDetail::where('order_no', $order->no)
                    ->pluck('field_value', 'field_name')
                    ->toArray();
                // 仲裁信息
                $consult = LevelingConsult::where('order_no', $order->no)->first();
                // 投诉表
                $complaint = BusinessmanComplaint::where('order_no', $order->no)->first();

                $data                          = [];
                $data['tm_status']             = '';
                $data['tm_income']             = 0;
                
                $data['revoke_creator']        = '';
                $data['arbitration_creator']   = '';
                $data['consult_amount']        = 0;
                $data['consult_deposit']       = 0;
                $data['consult_poundage']      = 0;
                
                $data['creator_judge_income']  = 0;
                $data['creator_judge_payment'] = 0;

                // 撤销信息
                if (isset($consult) && ! empty($consult)) {
                    if ($consult->complete && $consult->consult) {
                        $data['revoke_creator'] = $consult->consult == 1 ? $order->creator_user_id : $order->gainer_user_id;
                    }

                    if ($consult->complete && $consult->complain) {
                        $data['arbitration_creator'] = $consult->complain == 1 ? $order->creator_user_id : $order->gainer_user_id;
                    }

                    $data['consult_amount']      = $consult->api_amount;
                    $data['consult_deposit']     = $consult->api_deposit;
                    $data['consult_poundage']    = $consult->api_service;
                }  

                // 来源单号和天猫单号
                $sourceOrderNos = OrderDetail::where('order_no', $order->no)
                    ->where('field_name_alias', 'source_order_no')
                    ->where('field_value', '!=', '')
                    ->pluck('field_value')
                    ->unique()
                    ->toArray();

                $tmIncome = 0;
                if (isset($sourceOrderNos) && ! empty($sourceOrderNos) && is_array($sourceOrderNos) && count($sourceOrderNos) > 0) {
                    foreach ($sourceOrderNos as $sourceOrderNo) {
                        $tmOrder = TaobaoTrade::where('tid', $sourceOrderNo)->first();

                        if (isset($tmOrder) && ! empty($tmOrder) && $tmOrder->trade_status == 7) {
                            $tmIncome += $tmOrder->payment;
                        }
                    }
                }

                $data['tm_income'] = $tmIncome;

                // 仲裁信息
                if (isset($complaint) && ! empty($complaint)) {
                    if ($complaint->complaint_primary_user_id == $order->creator_primary_user_id) {
                        $data['creator_judge_income']        = $complaint->amount;
                    }

                    if ($complaint->be_complaint_primary_user_id == $order->creator_primary_user_id) {
                        $data['creator_judge_payment']        = $complaint->amount;
                    }
                }
                
                $data['order_no']                = $order->no;
                $data['date']                    = $order->updated_at->toDateString();
                $data['third']                   = $orderDetail['third'];
                $data['status']                  = $order->status;
                $data['client_wang_wang']        = $orderDetail['client_wang_wang'];
                $data['customer_service_name']   = $orderDetail['customer_service_name'];
                $data['game_id']                 = $order->game_id;
                $data['game_name']               = $order->game_name;
                $data['creator_user_id']         = $order->creator_user_id;
                $data['creator_primary_user_id'] = $order->creator_primary_user_id;
                $data['gainer_user_id']          = $order->gainer_user_id;
                $data['gainer_primary_user_id']  = $order->gainer_primary_user_id;
                $data['price']                   = $order->price;
                $data['security_deposit']        = $orderDetail['security_deposit'];
                $data['efficiency_deposit']      = $orderDetail['efficiency_deposit'];
                $data['original_price']          = $order->original_price;
                $data['order_created_at']        = $order->created_at->toDateTimeString();
                $data['order_finished_at']       = $orderDetail['checkout_time'] ?? 0;
                $data['is_repeat']               = $orderDetail['is_repeat'] ?? 0;

                OrderBasicData::updateOrCreate(['order_no' => $order->no], $data);

                $number++;

                echo $number.PHP_EOL;
            } catch (Exception $e) {
                $error++;
                myLog('run-order-basic-datas', ['order_no' => $order->no ?? '没取到', 'number' => $error ?? 0, 'message' => $e->getMessage()]);
            }
        }
    }
}
