<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderBasicData extends Model
{
    protected $fillable = [
    	'tm_status', 'tm_income', 'revoke_creator', 'arbitration_creator', 'order_finished_at',
		'consult_amount', 'consult_deposit', 'consult_poundage', 'creator_judge_income', 'creator_judge_payment',
		'order_no', 'status', 'client_wang_wang', 'customer_service_name', 'game_id',
		'game_name', 'creator_user_id', 'creator_primary_user_id', 'gainer_user_id', 'gainer_primary_user_id',
		'price', 'security_deposit', 'efficiency_deposit', 'original_price', 'order_created_at',
	];
}
