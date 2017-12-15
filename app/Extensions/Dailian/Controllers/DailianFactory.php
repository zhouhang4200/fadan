<?php

namespace App\Extensions\Dailian\Controllers;

class DailianFactory
{
	/**
	 * 传入一个key,找对应的类
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
    public function choose($name)
    {
		if (config('order.dailians')[$name]) {
            return new config('order.dailians')[$name];
        } else {
        	throw new Exception('参数传入错误!');
        }
    }
}
