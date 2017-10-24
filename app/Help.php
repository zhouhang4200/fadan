<?php

use App\Models\City;
use \GuzzleHttp\Client;

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
        $redis = \App\Services\RedisConnect::order();
        $redis->lpush(Config::get('rediskey.order.receiving') . $orderNo, $userId);
    }
}

if (!function_exists('receivingRecord')) {
    /**
     * 用户接单记录
     * @param $userId
     * @param $orderNo
     */
    function receivingRecord($userId, $orderNo)
    {
        // 用户抢单后写入一条记录
        $redis = \App\Services\RedisConnect::order();
        $redis->setex(Config::get('rediskey.order.receivingRecord') . $orderNo . $userId, Config::get('rediskey.timeout'), $userId);
    }
}

if (!function_exists('receivingRecordExist')) {
    /**
     * 用户接单记录是否存在
     * @param $userId
     * @param $orderNo
     */
    function receivingRecordExist($userId, $orderNo)
    {
        // 查询是否有抢单记录
        $redis = \App\Services\RedisConnect::order();
        return $redis->get(Config::get('rediskey.order.receivingRecord') . $orderNo . $userId);
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
        $redis = \App\Services\RedisConnect::order();
        $orderQuantity = $redis->incr(Config::get('rediskey.order.quantity') . date('Ymd'));
        return $orderDate . str_pad($orderQuantity, 8, 0, STR_PAD_LEFT);
    }
}
