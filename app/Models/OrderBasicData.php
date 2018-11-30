<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderBasicData extends Model
{
    protected $fillable = [
    	'tm_status', 'tm_income', 'revoke_creator', 'arbitration_creator', 'order_finished_at',
		'consult_amount', 'consult_deposit', 'consult_poundage', 'creator_judge_income', 'creator_judge_payment',
		'order_no', 'status', 'client_wang_wang', 'customer_service_name', 'game_id',
		'game_name', 'creator_user_id', 'creator_primary_user_id', 'gainer_user_id', 'gainer_primary_user_id',
		'price', 'security_deposit', 'efficiency_deposit', 'original_price', 'order_created_at', 'is_repeat',
		'third', 'date', 'foreign_order_no', 'pay_amount'
	];

    /**
     * 平台统计信息筛选
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function scopeFilter($query, $filters = [])
    {
        if ($filters['userIds']) {
            $query->whereIn('creator_user_id', $filters['userIds']);
        }

        if ($filters['third']) {
            $query->where('third', $filters['third']);
        }

        if ($filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $addDate = Carbon::parse($filters['endDate'])->addDays(1)->toDateString();
            
            $query->where('date', '>=', $filters['startDate'])->where('date', '<', $addDate);
        }
        return $query;
    }

    /**
     * 平台统计信息筛选
     * @param $query
     * @param array $filters
     * @return mixed
     */
    public static function scopeFilterBaby($query, $filters = [])
    {
        if ($filters['gameId']) {
            $query->where('game_id', $filters['gameId']);
        }

        if ($filters['startDate'] && empty($filters['endDate'])) {
            $query->where('date', '>=', $filters['startDate']);
        }

        if ($filters['endDate'] && empty($filters['startDate'])) {
            $query->where('date', '<=', $filters['endDate']);
        }

        if ($filters['endDate'] && $filters['startDate']) {
            $addDate = Carbon::parse($filters['endDate'])->addDays(1)->toDateString();
            
            $query->where('date', '>=', $filters['startDate'])->where('date', '<', $addDate);
        }
        return $query;
    }

    /**
     * @param GameLevelingOrder $order
     */
    public static function createData(GameLevelingOrder $order)
    {
        try {
            $order = GameLevelingOrder::where('trade_no', $order->trade_no)->first();

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

            $gameLevelingOrderConsult = GameLevelingOrderConsult::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('status', 2)
                ->first();

            $gameLevelingOrderComplain = GameLevelingOrderComplain::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('status', 2)
                ->first();

            // 投诉表
            $complaint = BusinessmanComplaint::where('order_no', $order->trade_no)->first();

            // 撤销、仲裁信息
            if ($gameLevelingOrderConsult) {
                $data['revoke_creator'] = $gameLevelingOrderConsult->initiator == 1 ? $order->user_id : $order->take_user_id;
                $data['consult_amount']      = $gameLevelingOrderConsult->amount;
                $data['consult_deposit']     = $gameLevelingOrderConsult->bcadd($gameLevelingOrderConsult->security_deposit, $gameLevelingOrderConsult->efficiency_deposit);
                $data['consult_poundage']    = $gameLevelingOrderConsult->poundage;
            } elseif ($gameLevelingOrderComplain) {
                $data['arbitration_creator'] = $gameLevelingOrderComplain->initiator == 1 ? $order->user_id : $order->take_user_id;
                $data['consult_amount']      = $gameLevelingOrderComplain->amount;
                $data['consult_deposit']     = $gameLevelingOrderComplain->bcadd($gameLevelingOrderComplain->security_deposit, $gameLevelingOrderComplain->efficiency_deposit);
                $data['consult_poundage']    = $gameLevelingOrderComplain->poundage;
            }

            // 投诉
            if ($complaint) {
                if ($complaint->complaint_primary_user_id == $order->parent_user_id) {
                    $data['creator_judge_income'] = $complaint->amount;
                }

                if ($complaint->be_complaint_primary_user_id == $order->parent_user_id) {
                    $data['creator_judge_payment'] = $complaint->amount;
                }
            }

            // 来源单号和天猫单号
            $sourceOrderNos = GameLevelingOrderRelationChannel::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('channel', 1)
                ->pluck('game_leveling_channel_order_trade_no')
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

            // 订单完成支付价格
            $payAmount = 0;
            if ($order->status == 20) {
                $payAmount = $order->amount;
            }

            $data['order_no']                = $order->trade_no;
            $data['foreign_order_no']        = $order->source_order_no ?? '';
            $data['date']                    = $order->created_at->toDateString();
            $data['third']                   = $order->platform_id;
            $data['status']                  = $order->status;
            $data['client_wang_wang']        = $order->buyer_nick;
            $data['customer_service_name']   = $order->customer_service_name;
            $data['game_id']                 = $order->game_id;
            $data['game_name']               = $order->gameLevelingOrderDetail->game_name;
            $data['creator_user_id']         = $order->user_id;
            $data['creator_primary_user_id'] = $order->parent_user_id;
            $data['gainer_user_id']          = $order->take_user_id;
            $data['gainer_primary_user_id']  = $order->take_parent_user_id;
            $data['price']                   = $order->amount;
            $data['pay_amount']              = $payAmount;
            $data['security_deposit']        = $order->security_deposit ?? 0;
            $data['efficiency_deposit']      = $order->efficiency_deposit ?? 0;
            $data['original_price']          = $order->source_price ?? 0;
            $data['order_created_at']        = $order->created_at->toDateTimeString();
            $data['order_finished_at']       = $order->complete_at;
            $data['is_repeat']               = $order->repeat ?? 0;

            static::updateOrCreate(['order_no' => $order->trade_no], $data);
        } catch (\Exception $exception) {
            myLog('new-base-data-error', [$exception->getMessage(), $exception->getFile(), $exception->getLine()]);
        }
    }
}
