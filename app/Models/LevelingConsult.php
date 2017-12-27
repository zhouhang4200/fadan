<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelingConsult extends Model
{
    public $fillable = [
      'user_id',
      'order_no',
      'amount',
      'deposit',
      'api_amount',
      'api_deposit',
      'api_service',
      'consult',
      'complain',
      'revoke_message',
      'complain_message',
    ];

    public static function rules()
    {
    	return [
    		'amount' => 'required',
    		'deposit' => 'required',
    	];
    }
}
