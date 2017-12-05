<?php
namespace App\Services;

use App\Models\User;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;

/**
 * Class FuluAppApi
 * @package App\Http\Controllers\Api\Mobile
 */
class FuluAppApi extends Controller
{
    /**
     * 给fulu那边的皮肤订单接口提供QQ和order_id
     * @param $userId
     * @param $orderId
     * @return bool
     */
    public static function sendOrderAndQq($userId, $orderId)
    {
        $str = '39ZsRujRvh5hfYCN7f5Dvt8ZUtYj7ap5';
        $userSet = User::where(['id' => $userId])->first();
        $userSetArr = $userSet->getUserSetting();

        if (isset($userSetArr['skin_trade_qq'])) {
            $data = json_encode(['qq' => $userSetArr['skin_trade_qq'], 'orderId' => $orderId]);

            $sign = md5($str . $data);
            $reqData = base64_encode($data);

            $url = 'https://secapi.fulugou.com/callback/handGameNotify?reqData=' . $reqData. '&sign='. $sign;

            $client = new Client();
            $response = $client->request('GET', $url);
            $result = json_decode(base64_decode($response->getBody()->getContents()));

            if ($result->code != 10000) {
                return false;
            }
            return true;
        } else {
            \Log::alert('卖家没有设置QQ号，订单号：' . $orderId);
        }

    }
}