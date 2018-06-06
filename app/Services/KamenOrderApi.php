<?php
namespace App\Services;

use GuzzleHttp\Client;

/**
 * 卡门订单状态变更API
 * Class KamenOrderApi
 * @package App\Services
 */
class KamenOrderApi
{
    /**
     * @var
     */
    protected static $_instance;

    /**
     * @var array
     */
    private $apiUrl = [
        "http://ls.kamennet.com/",
        "http://api1.kabaling.com/",
        "http://ls1.kabaling.com/",
        "http://api2.kabaling.com/",
        "http://ls.kabaling.com/",
        "http://api3.kamennet.com/",
        "http://api4.kabaling.com/",
        "http://api1.kamennet.com/",
        "http://ls1.kamennet.com/",
        "http://ls2.kamennet.com/",
        "http://api2.kamennet.com/",
        "http://ls2.kabaling.com/"
    ];

    /**
     * @var integer 卡门网订单号
     */
    private $kmOrderId;

    /**
     * @return KamenOrderApi
     */
    public static function share()
    {
        if (self::$_instance == null) {
            return self::$_instance = new KamenOrderApi();
        }
        return self::$_instance;
    }


    /**
     * 更新订单状态 为成功
     * @param int $kmOrderId
     * @return string
     */
    public function success($kmOrderId)
    {
        try {
            $param =  'SiteId=105714&OrderNo=' . $kmOrderId. '&OrderStatus=' . strtolower(urlencode('成功'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('充值成功')) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . '123456'));

            $url =  $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' .  $param . $sign;

            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
//        return $response->getBody()->getContents();

            $param =  'SiteId=107560&OrderNo=' . $kmOrderId. '&OrderStatus=' . strtolower(urlencode('成功'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('充值成功')) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . '123456'));

            $url =  $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' .  $param . $sign;

            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
        return $response->getBody()->getContents();
        } catch (\Exception $exception) {

        }
    }

    /**
     * 更新订单状态 失败
     * @param $kmOrderId integer 卡门订单号
     * @return string
     */
    public  function fail($kmOrderId)
    {
        try {
            $param = 'SiteId=105714&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('失败'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('失败')) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . '123456'));

            $url = $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' . $param . $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
            $result1 =  $response->getBody()->getContents();

            $param = 'SiteId=107560&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('失败'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('失败')) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . '123456'));

            $url = $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' . $param . $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
            $result2 =  $response->getBody()->getContents();

//            return $result2;
        } catch(\Exception $e){
            return false;
        }
    }

    /**
     * 更新订单状态
     * @param $kmOrderId integer 卡门订单号
     * @return bool|string
     */
    public  function ing($kmOrderId)
    {
        try {
            $param = 'SiteId=105714&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('处理中'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode(generateUuid())) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . '123456'));

            $url = $this->apiUrl[rand(0, 11)] .  'API/Order/ModifyOrderStatus.aspx?' . $param . $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
            $result =  $response->getBody()->getContents();

            $param = 'SiteId=107560&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('处理中'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode(generateUuid())) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . '123456'));

            $url = $this->apiUrl[rand(0, 11)] .  'API/Order/ModifyOrderStatus.aspx?' . $param . $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
            $result =  $response->getBody()->getContents();
//            return $result;
        } catch (\Exception $exception) {

        }
    }
}