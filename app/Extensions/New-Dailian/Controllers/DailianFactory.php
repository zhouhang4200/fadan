<?php

namespace App\Extensions\Dailian\Controllers;

use App\Exceptions\DailianException;

/**
 * 代练操作工厂
 */
class DailianFactory
{
	/**
	 * 传入一个key,找对应的类
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
    public static function choose($name)
    {
        $dailians = config('order.dailians');

		if ($dailians[$name]) {
            return new $dailians[$name];
        } else {
        	throw new DailianException('参数解析错误!');
        }
    }
}
