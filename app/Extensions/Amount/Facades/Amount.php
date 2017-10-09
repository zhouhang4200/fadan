<?php

namespace App\Extensions\Amount\Facades;

use Illuminate\Support\Facades\Facade;

class Amount extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'amount';
    }
}
