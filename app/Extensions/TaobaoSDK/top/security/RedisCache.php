<?php

namespace App\Extensions\TaobaoSDK\top\security;

use Illuminate\Support\Facades\Cache;

class RedisCache implements iCache
{
    public function getCache($key)
    {
        return Cache::get($key);
    }

    public function setCache($key, $var)
    {
        return Cache::set($key, $var);
    }
}