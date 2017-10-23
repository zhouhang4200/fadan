<?php
namespace App\Services;

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
        return Redis::connect('order');
    }
}