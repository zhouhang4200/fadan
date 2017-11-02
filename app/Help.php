<?php

use App\Models\City;
use \GuzzleHttp\Client;
use \App\Services\RedisConnect;

if (!function_exists('loginDetail')) {

    /**
     * @param $ip
     * @return array
     */
    function loginDetail($ip)
    {
    	$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $ip;

    	$client = new Client();

    	$res = $client->request('GET', $url);

    	$res = $res->getBody();

    	$res = json_decode($res);

        if (isset($res->ret) && $res->ret == 1) {
            
            $city = City::where('name', $res->city)->first();

            return [
                'country'  => $res->country,
                'province' => $res->province,
                'city'     => empty($res->city) ? $res->province : $res->city,
                'city_id'  => $city ? $city->id : 0,
                'ip'       => ip2long($ip),
            ];
        }
        return [
            'country'  => '',
            'province' => '',
            'city'     => '',
            'city_id'  => 0,
            'ip'       => ip2long($ip),
        ];
    }
}

if (!function_exists('jsonMessages')) {

    function jsonMessages($code, $message)
    {
        $data = ['code' => $code, 'message' => $message];

        return json_encode($data);
    }
}

if (!function_exists('receiving')) {
    /**
     * 用户接单
     * @param $userId
     * @param $orderNo
     */
    function receiving($userId, $orderNo)
    {
        // 将当前用户ID，写入订单抢单队列中
        $redis = RedisConnect::order();
        $redis->lpush(config('redis.order.receiving') . $orderNo, $userId);
    }
}

if (!function_exists('receivingRecord')) {
    /**
     * 用户接单记录 用主账号ID记录
     * 用户抢单后写入一条记录
     * @param $userId
     * @param $orderNo
     * @return int
     */
    function receivingRecord($userId, $orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->setex(config('redis.order.receivingRecord') . $orderNo . $userId  , config('redis.timeout'), $userId);
    }
}

if (!function_exists('receivingUserLen')) {
    /**
     * 获取接单用户队列长度
     * @param $orderNo
     */
    function receivingUserLen($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->lLen(config('redis.order.receiving') . $orderNo);
    }
}
if (!function_exists('receivingUser')) {
    /**
     * 获取接单用户队列所有用户ID
     * @param $orderNo
     */
    function receivingUser($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->lrange(config('redis.order.receiving') .  (string) $orderNo, 0, receivingUserLen($orderNo) );
    }
}

if (!function_exists('receivingRecordExist')) {
    /**
     * 用户接单记录是否存在 用主账号ID记录
     * @param $userId
     * @param $orderNo
     * @return int
     */
    function receivingRecordExist($userId, $orderNo)
    {
        $redis = RedisConnect::order();
        return  $redis->get(config('redis.order.receivingRecord') . $orderNo . $userId);
    }
}

if (!function_exists('waitReceivingGet')) {
    /**
     * 获取 待接单的订单
     */
    function waitReceivingGet()
    {
        $redis = RedisConnect::order();
        return $redis->hgetall(config('redis.order.waitReceiving'));
    }
}

if (!function_exists('waitReceivingDel')) {
    /**
     * 删除 待接单的订单
     * @param $orderNo
     * @return mixed
     */
    function waitReceivingDel($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->hdel(config('redis.order.waitReceiving'), $orderNo);
    }
}

if (!function_exists('waitReceivingAdd')) {
    /**
     * 添加 待接单的订单
     * @param $orderNo
     * @return mixed
     */
    function waitReceivingAdd($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->hset(config('redis.order.waitReceiving'), $orderNo, $orderNo);
    }
}

if (!function_exists('waitReceivingQuantity')) {
    /**
     * 获取订单集市 订单数
     * @return mixed
     */
    function marketOrderQuantity()
    {
        $redis = RedisConnect::order();
        return $redis->get(config('redis.order.waitReceivingQuantity'));
    }
}

if (!function_exists('waitReceivingQuantityAdd')) {
    /**
     * 获取订单集市 订单数 加1
     * @return mixed
     */
    function waitReceivingQuantityAdd()
    {
        $redis = RedisConnect::order();
        return $redis->incr(config('redis.order.waitReceivingQuantity'));
    }
}

if (!function_exists('waitReceivingQuantitySub')) {
    /**
     * 获取订单集市 订单数 减1
     * @return mixed
     */
    function waitReceivingQuantitySub()
    {
        if (marketOrderQuantity() > 0) {
            $redis = RedisConnect::order();
            return $redis->decr(config('redis.order.waitReceivingQuantity'));
        }
    }
}

if (!function_exists('socketServer')) {
    /**
     * socketServer
     * @return mixed
     */
    function socketServer()
    {
        return env('SOCKET_SERVER');
    }
}


if (!function_exists('generateOrderNo')) {
    /**
     * 生成订单号
     * @return string
     */
    function generateOrderNo()
    {
        // 14位长度当前的时间 20150709105750
        $orderDate = date('YmdHis');

        // 今日订单数量
        $redis = RedisConnect::order();
        $orderQuantity = $redis->incr(config('redis.order.quantity') . date('Ymd'));
        return $orderDate . str_pad($orderQuantity, 8, 0, STR_PAD_LEFT);
    }
}
