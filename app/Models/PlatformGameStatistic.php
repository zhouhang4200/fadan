<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformGameStatistic extends Model
{
    protected $fillable = [
    	'date', 'game_id', 'total_order_count', 'wang_wang_order_evg', 'use_time', 'use_time_avg',
    	'receive_order_count', 'complete_order_count', 'complete_order_rate', 'complete_order_amount', 'complete_order_amount_avg', 
    	'revoke_order_count', 'revoke_order_rate', 'arbitrate_order_count', 'complain_order_rate', 'done_order_count',
    	'total_security_deposit', 'security_deposit_avg', 'total_efficiency_deposit', 'efficiency_deposit_avg',
    	'total_original_amount', 'original_amount_avg', 'total_amount', 'amount_avg', 'total_revoke_payment',
    	'revoke_payment_avg', 'total_complain_payment', 'complain_payment_avg', 'total_revoke_income', 'revoke_income_avg',
    	'total_complain_income', 'complain_income_avg', 'total_poundage', 'poundage_avg', 'user_total_profit', 'user_profit_avg', 
    	'platform_total_profit', 'platform_profit_avg','created_at', 'updated_at',
    ];
}
