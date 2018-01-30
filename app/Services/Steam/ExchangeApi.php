<?php
namespace App\Services\Steam;

use App\Services\Aes;
use GuzzleHttp\Client;

class ExchangeApi
{
    /**
     * steam验证
     * @var array
     */

    private static $key = 'CE12DA8211B673F1B9694E3688D553D7';

    public $serverIp;

    private static $apiUrl = [
        'exchange' => 'http://api.kamennet.com/api/ptk/action.aspx?',
        'findCard' => 'http://test.api.kamennet.com/API/Order/Query_Card.aspx?',
        'serverIp' => 'http://47.97.61.179:8000/api/SteamApi/GetServerIp?',
    ];


    public function __construct($ip)
    {
        $this->serverIp = $ip.'api/SteamApi/GetSteamLoginAndSecure?';
    }

    public static function getServerIp()
    {
        $client = new Client();
        $res = $client->request('GET', self::$apiUrl['serverIp'], [
            'form_params' => []
        ]);
        return json_decode(json_decode($res->getBody()->getContents()));
    }

    public function report($data, $sign)
    {
        $client = new Client();
        $res = $client->request('POST', $this->serverIp, [
            'form_params' => [
                'data' => $data,
                'sign' => $sign,
            ]
        ]);
        return json_decode($res->getBody()->getContents());
    }

    public static function accEncryption($username = null, $password = null, $data = null, $captcha = null)
    {
        $aes = new Aes();
        $data = [
            'username' => $username,
            'password' => $password,
            'data' => $data,
            'captcha' => $captcha,
        ];
        $encryptData = $aes->encrypt(json_encode($data));
        return $encryptData;
    }

    public static function getSign($username = null, $password = null, $data = null, $code = null)
    {

        $str = (string)($username . $password . $data . $code);

        return md5($str . '123!@#abc');

    }

    /**
     * 查询接口
     * @param $account
     * @param $password
     * @param $siteId
     * @return mixed
     */
    public static function exchange($account, $password, $siteId)
    {

        $client = new Client();
        $res = $client->request('POST', self::$apiUrl['exchange'], [
            'form_params' => [
                'method' => 'ptk.card.validation',
                'siteId' => $siteId,
                'cardNumber' => $account,
                'cardPassword' => $password,
                'sign' => static::getExchangeSign($account, $password, $siteId)
            ]
        ]);

        return json_decode($res->getBody()->getContents());
    }


    public static function exchangeOrder($account, $password, $siteId, $productid, $chargeAccount, $chargePassword)
    {
        $client = new Client();
        $res = $client->request('POST', self::$apiUrl['exchange'], [
            'form_params' => [
                'method' => 'ptk.order.add',
                'siteId' => $siteId,
                'cardNumber' => $account,
                'cardPassword' => $password,
                'productid' => $productid,
                'buynum' => 1,
                'chargeAccount' => $chargeAccount,
                'chargePassword' => $chargePassword,
                'sign' => static::getExchangeSign($account, $password, $siteId)
            ]
        ]);
        return json_decode($res->getBody()->getContents());
    }

    public static function orderStatus($account, $password, $siteId, $orderId)
    {

        $client = new Client();
        $res = $client->request('POST', self::$apiUrl['exchange'], [
            'form_params' => [
                'method' => 'ptk.order.get',
                'siteId' => $siteId,
                'cardNumber' => $account,
                'cardPassword' => $password,
                'orderid' => $orderId,
                'sign' => static::getExchangeSign($account, $password, $siteId)
            ]
        ]);
        return json_decode($res->getBody()->getContents());
    }

    public static function findCard($account, $password, $siteId, $importTime)
    {
        $client = new Client();
        $res = $client->request('POST', self::$apiUrl['findCard'], [
            'form_params' => [
                'siteId' => $siteId,
                'cardNumber' => $account,
                'cardPassword' => $password,
                'importTime' => $importTime,
                'sign' => static::getExchangeSign($account, $password, $siteId)
            ]
        ]);
        return json_decode($res->getBody()->getContents());
    }

    /**
     * 签名
     * @param $param
     * @return string
     */
    public static function getExchangeSign($account, $password, $siteId)
    {

        $str = $siteId . $account . $password . self::$key;

        return strtoupper(md5($str));

    }


}
