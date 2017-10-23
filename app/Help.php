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

if (!function_exists('')) {

    function generateOrderNo()
    {
        // 14位长度当前的时间 20150709105750
        $orderdate = date('YmdHis');

        // 今日订单数量
        $orderquantity = \Redis::incr('thousand:order:quantity:' . date('Ymd'));
        return $orderdate . str_pad($orderquantity, 8, 0, STR_PAD_LEFT);
    }
}
