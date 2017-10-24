<?php

namespace App\Extensions\Order\ForeignOrder;

use Log;
use Exception;

class ForeignOrderFactory
{
    public static function choose($channel)
    {
    	try {

	    	$parsers = config('order.parsers');

            if ($parsers[$channel]) {

                return new $parsers[$channel];
            }

    	} catch (Exception $e) {

	        Log::error('传入参数错误或没有此渠道订单！', ['channel' => $channel]);
    	}

    	return true;
    }
}
