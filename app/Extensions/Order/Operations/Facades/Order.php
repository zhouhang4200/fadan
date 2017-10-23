<?php

namespace App\Extensions\Order\Operations\Facades;

use Illuminate\Support\Facades\Facade;

class Order extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'order';
    }
}
