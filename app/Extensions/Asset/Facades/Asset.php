<?php

namespace App\Extensions\Asset\Facades;

use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'asset';
    }
}
