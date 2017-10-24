<?php

namespace App\Extensions\Order\ForeignOrder;

use Exception;

class ForeignOrderFactory
{
    public static function choose($channel)
    {
    	try {

	    	$parsers = config('order.parsers');

	        foreach ($parsers as $foreignOrderClass => $channelName) {

	            if ($channel == $channelName) {

	                return new $foreignOrderClass;
	            }
	        }

    	} catch (Exception $e) {

	        Log::error('传入参数错误或没有此渠道订单！', ['channel' => $channel]);
    	}

    	return true;
    }
}
