<?php
namespace App\Services;

use App\Models\SiteInfo;
use Cache, Config, DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

/**
 *
 * Class TmOrderApi
 * @package App\Publics
 */
class TmallOrderApi
{
    const key = 'fltop31bf3856ad364e35';

    /**
     * 获取天猫订单详情
     * @param $kamenSiteId
     * @param $tmallOrderId
     * @return array
     */
    public static function getOrder($kamenSiteId, $tmallOrderId)
    {
        $storeNameAndToken = self::accessToken($kamenSiteId);

        // 获取token
        $client = new Client();
        $response = $client->request('POST', 'http://fulutop.kamennet.com/trade/TradeFullinfoGet', [
            'query' => http_build_query([
                'access_token' => $storeNameAndToken['token'],
                'tid' => $tmallOrderId,
                'sign' => strtoupper(md5('access_token' . $storeNameAndToken['token'] . 'tid' . $tmallOrderId . self::key)),
            ]),
        ]);
        $result = json_decode($response->getBody()->getContents());

        myLog('tm', [$result]);
        if (!isset($result->error_response)) {
            // 等待发货订单
            if (env('APP_ENV') == 'local') {
                return [
                    'store_name' => $storeNameAndToken['name'],
                    'wang_wang' => $result->BuyerNick,
                    'price' => $result->Price,
                    'payment' => $result->Payment,
                    'pay_time' => $result->PayTime,
                    'remark' => $result->ReceiverAddress,
                    'ip' => base64_decode($result->BuyerIp),
                ];
            } else if ($result->Status == 'WAIT_SELLER_SEND_GOODS') {
                return [
                    'store_name' => $result->SellerNick,
                    'wang_wang' => $result->BuyerNick,
                    'price' => $result->Price,
                    'payment' => $result->Payment,
                    'pay_time' => $result->PayTime,
                    'remark' => $result->ReceiverAddress,
                    'ip' => base64_decode($result->BuyerIp),
                ];
            }
        }
    }

    /**
     * 获取 access token
     * @param $kamenSiteId
     * @return mixed
     */
    public static function accessToken($kamenSiteId)
    {
        // 查询是否有缓存
        if ($tokenAndStoreName = cache(config('redis.tmallStoreToken') . $kamenSiteId)) {
            return $tokenAndStoreName;
        }
        // 根据卡门站点ID获取店名
        $storeName = SiteInfo::where('kamen_site_id', $kamenSiteId)->value('name');

        // 获取token
        $client = new Client();
        $response = $client->request('POST', 'http://fulutop.kamennet.com/session/index', [
            'query' => http_build_query([
                'nickName' => $storeName,
                'sign' => strtoupper(md5('nickName' . $storeName . self::key))
            ]),
        ]);
        $result = json_decode($response->getBody()->getContents());
        // 店名与token
        $info = ['name' => $storeName, 'token' => $result->access_token];
        // 缓存token 一年
        $expiresAt = Carbon::now()->addYear(1);
        Cache::put(config('redis.tmallStoreToken') . $kamenSiteId, $info, $expiresAt);

        return $info;
    }
}