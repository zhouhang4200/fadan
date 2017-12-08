<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayData extends Model
{
    protected $fillable = ['stock_trusteeship', 'stock_transaction', 'transfer_transaction', 'slow_recharge', 'order_market', 'date'];

    public static function scopeFilter($query, $date)
    {
    	if ($date) {
    		$date = $date . ' 00:00:00';
    		$query->where('date', $date);
    	}
    	return $query;
    }
}
