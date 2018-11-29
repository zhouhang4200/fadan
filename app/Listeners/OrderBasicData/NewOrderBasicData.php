<?php

namespace App\Listeners\OrderBasicData;

use App\Models\TaobaoTrade;
use App\Models\OrderBasicData;
use App\Models\GameLevelingOrder;
use App\Models\BusinessmanComplaint;
use App\Models\GameLevelingOrderRelationChannel;
use App\Events\NewOrderBasicData as NewOrderBasicDataEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrderBasicData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewOrderBasicDataEvent  $event
     * @return void
     */
    public function handle(NewOrderBasicDataEvent $event)
    {
        try {
            $order = GameLevelingOrder::where('trade_no', $event->order->trade_no)->with('gameLevelingOrderDetail')->first();
myLog('1', [$order]);
            $tmIncome = 0;

            // 来源单号和天猫单号
            $sourceOrderNos = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('channel', 1)
                ->pluck('game_leveling_channel_order_trade_no')
                ->unique()
                ->toArray();

            if (isset($sourceOrderNos) && ! empty($sourceOrderNos) && is_array($sourceOrderNos) && count($sourceOrderNos) > 0) {
                foreach ($sourceOrderNos as $sourceOrderNo) {
                    $tmOrder = TaobaoTrade::where('tid', $sourceOrderNo)->first();

                    if (isset($tmOrder) && ! empty($tmOrder) && $tmOrder->trade_status == 7) {
                        $tmIncome += $tmOrder->payment;
                    }
                }
            }

            $judgeIncome  = 0;
            $judgePayment = 0;

            // 投诉表
            $complaint = BusinessmanComplaint::where('order_no', $order->trade_no)->first();

            // 仲裁信息
            if (isset($complaint) && ! empty($complaint)) {
                if ($complaint->complaint_primary_user_id == $order->creator_primary_user_id) {
                    $judgeIncome = $complaint->amount;
                }

                if ($complaint->be_complaint_primary_user_id == $order->creator_primary_user_id) {
                    $judgePayment = $complaint->amount;
                }
            }

            // 订单完成支付价格
            $payAmount = 0;
            if ($order->status == 20) {
                $payAmount = $order->amount;
            }

            $data['date']                    = $order->created_at->toDateString();
            $data['order_no']                = $order->trade_no;
            $data['foreign_order_no']        = $order->channel_order_trade_no ?? '';
            $data['third']                   = $order->platform_id ?? '';
            $data['is_repeat']               = $order->repeat ?? 0;
            $data['status']                  = $order->status;
            $data['tm_status']               = $order->channel_order_status;
            $data['client_wang_wang']        = $order->seller_nick;
            $data['customer_service_name']   = $order->customer_service_name;
            $data['game_id']                 = $order->game_id;
            $data['game_name']               = $order->gameLevelingOrderDetail->game_name;
            $data['revoke_creator']          = $order->gameLevelingOrderConsult ? $order->gameLevelingOrderConsult->user_id : '';
            $data['arbitration_creator']     = $order->gameLevelingOrderComplain ? $order->gameLevelingOrderComplain->user_id : '';
            $data['creator_user_id']         = $order->user_id;
            $data['creator_primary_user_id'] = $order->parent_user_id;
            $data['gainer_user_id']          = $order->take_user_id;
            $data['gainer_primary_user_id']  = $order->take_parent_user_id;
            $data['price']                   = $order->amount;
            $data['pay_amount']              = $payAmount;
            $data['tm_income']               = $tmIncome;
            $data['security_deposit']        = $order->security_deposit;
            $data['efficiency_deposit']      = $order->efficiency_deposit;
            $data['original_price']          = $order->source_amount;
            $data['consult_amount']          = $order->gameLevelingOrderConsult ? $order->gameLevelingOrderConsult->amount : 0;
            $data['consult_deposit']         = $order->gameLevelingOrderConsult ? bcadd($order->gameLevelingOrderConsult->security_deposit, $order->gameLevelingOrderConsult->efficiency_deposit, 2)  : 0;
            $data['consult_poundage']        = $order->gameLevelingOrderConsult ? $order->gameLevelingOrderConsult->poundage : 0;
            $data['creator_judge_income']    = $judgeIncome;
            $data['creator_judge_payment']   = $judgePayment;
            $data['order_created_at']        = $order->created_at->toDateTimeString();
            $data['order_finished_at']       = $order->complete_at ?? null;

            OrderBasicData::updateOrCreate(['order_no' => $order->trade_no], $data);
        } catch (\Exception $e) {
            myLog('new-order-basic-data', [$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }
}
