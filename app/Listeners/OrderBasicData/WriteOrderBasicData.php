<?php

namespace App\Listeners\OrderBasicData;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\TaobaoTrade;
use App\Events\OrderBasicData;
use App\Models\LevelingConsult;
use App\Models\BusinessmanComplaint;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderBasicData as OrderBasicDataModel;

class WriteOrderBasicData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  OrderBasicData  $event
     * @return void
     */
    public function handle(OrderBasicData $event)
    {
        // 订单
        $order = Order::where('no', $event->order->no)->first();
        // 订单详情
        $orderDetail = OrderDetail::where('order_no', $order->no)
            ->pluck('field_value', 'field_name')
            ->toArray();
        // 仲裁信息
        $consult = LevelingConsult::where('order_no', $order->no)->first();
        // 天猫订单
        $tmOrder = TaobaoTrade::where('tid', $order->foreign_order_no)->first();
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

        if (isset($tmOrder) && ! empty($tmOrder)) {
            $data['tm_status']           = $tmOrder->trade_status;
            if ($data['tm_status'] == 7) {
                $data['tm_income']           = $tmOrder->payment;
            }
        } 

        if (isset($complaint) && ! empty($complaint)) {
            if ($complaint->complaint_primary_user_id == $order->creator_primary_user_id) {
                $data['creator_judge_income']        = $complaint->amount;
            }

            if ($complaint->be_complaint_primary_user_id == $order->creator_primary_user_id) {
                $data['creator_judge_payment']        = $complaint->amount;
            }
        }
        
        $data['order_no']                = $order->no;
        $data['date']                    = $order->created_at->toDateString();
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
        $data['order_finished_at']       = $orderDetail['checkout_time'];
        $data['is_repeat']               = $orderDetail['is_repeat'] ? 1 : 0;

        OrderBasicDataModel::updateOrCreate(['order_no' => $order->no], $data);
    }
}
