<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileOrder extends Model
{
    protected $fillable = [
    	'no', 'channel', 'status', 'game_id', 'game_name',
		'region', 'server', 'role', 'account', 'password',
		'client_phone', 'pay_type', 'original_price', 'price', 'creator_user_id',
		'creator_username', 'gainer_user_id', 'remark', 'created_at', 'updated_at',
		'out_trade_no', 'client_qq', 'game_leveling_requirements', 'game_leveling_instructions',
		'security_deposit', 'efficiency_deposit', 'game_leveling_day', 'game_leveling_hour', 'game_leveling_title',
		'game_leveling_type', 'user_qq', 'demand'
	];
}
