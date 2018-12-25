<?php
namespace App\Services;

use RedisFacade;

/**
 * redis 连接类
 * Class RedisConnect
 * @package App\Services
 */
class RedisConnect
{
    /**
     * 订单相关
     * @return mixed
     */
    public static function order()
    {
        return RedisFacade::connection('order');
    }

    /**
     * 订单相关
     * @return mixed
     */
    public static function session()
    {
        return RedisFacade::connection('sessions');
    }
}