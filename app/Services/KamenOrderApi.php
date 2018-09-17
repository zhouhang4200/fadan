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
     * @param int $amount
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function success($kmOrderId, $amount = 0)
    {
        try {

            $chargeUser = json_encode([
                "channel_list" => [
                    [
                        "channel_id" =>  "a022d754-2e40-4835-b1f6-8bc70f77e83d",
                        "channel_account" =>  "订单集市",
                        "time" =>  date('Y-m-d H:i:s'),
                        "amount" =>  $amount,
                        "amount_type" =>  "RMB",
                    ]
                ]
            ]);

            $param =  'SiteId=105714&OrderNo=' . $kmOrderId. '&OrderStatus=' . strtolower(urlencode('成功'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('充值成功')) . '&ChargeUse='.  strtolower(urlencode($chargeUser));

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

            $url =  $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' .  $param .  $sign;

            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));

            myLog('km-api-success', ['105714', $kmOrderId, $response->getBody()->getContents()]);

            $param =  'SiteId=107560&OrderNo=' . $kmOrderId. '&OrderStatus=' . strtolower(urlencode('成功'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('充值成功')) . '&ChargeUse='.  strtolower(urlencode($chargeUser));

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

            $url =  $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' .  $param  .  $sign;

            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));

            myLog('km-api-success', ['107560', $kmOrderId, $response->getBody()->getContents()]);

        } catch (\Exception $exception) {
            myLog('km-api-ex', $exception->getMessage());
        }
    }

    /**
     * 更新订单状态 失败
     * @param $amount integer 卡门订单号
     * @param $kmOrderId integer 卡门订单号
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public  function fail($kmOrderId, $amount = 0)
    {
        try {
            $chargeUser = json_encode([
                "channel_list" => [
                    [
                        "channel_id" =>  "a022d754-2e40-4835-b1f6-8bc70f77e83d",
                        "channel_account" =>  "订单集市",
                        "time" =>  date('Y-m-d H:i:s'),
                        "amount" =>  $amount,
                        "amount_type" =>  "RMB",
                    ]
                ]
            ]);

            $param = 'SiteId=105714&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('失败'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('失败')) . '&ChargeUse='  .  strtolower(urlencode($chargeUser));

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

            $url = $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' . $param  .  $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));

            myLog('km-api-fail', ['105714', $kmOrderId, $response->getBody()->getContents()]);

            $param = 'SiteId=107560&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('失败'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode('失败')) . '&ChargeUse='  .  strtolower(urlencode($chargeUser));

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

            $url = $this->apiUrl[rand(0, 11)] . 'API/Order/ModifyOrderStatus.aspx?' . $param  . $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));

            myLog('km-api-fail', ['107560', $kmOrderId, $response->getBody()->getContents()]);
        } catch(\Exception $e){
            myLog('km-api-ex', $e->getMessage());
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

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

            $url = $this->apiUrl[rand(0, 11)] .  'API/Order/ModifyOrderStatus.aspx?' . $param . $sign;

            // 发送请求
            $client = new Client();
            $response = $client->request('GET', str_replace(' ', '+', $url));
            $result =  $response->getBody()->getContents();

            $param = 'SiteId=107560&OrderNo=' . $kmOrderId . '&OrderStatus=' . strtolower(urlencode('处理中'))
                . '&Charger=vipqd_10---marekt&Description=' . strtolower(urlencode(generateUuid())) . '&ChargeUse=';

            $sign = '&Sign=' . strtoupper(md5(str_replace('&', '', $param) . 'B8F75DCE91E6486F9729E19EB762664E'));

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
