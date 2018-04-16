<?php

namespace App\Console\Commands;


use App\Services\RedisConnect;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use Symfony\Component\Console\Helper\Helper;

/**
 * 订单发送
 * Class OrderAssign
 * @package App\Console\Commands
 */
class OrderSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Order:Send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单发送到各平台';

    protected $redis;


    public function handle()
    {
        
        $this->redis = RedisConnect::order();

        while (1) {
            $orderData = $this->redis->lpop('order:send');

            if($orderData) {
                // 获取所有待分配订单
                $client = new Client();
                foreach (config('partner.platform') as $platform) {
                    try {
                        // 如果是蚂蚁代练
                        if ('mayi' == $paltform['name']) {
                            $time = time();
                            // 获取 sign
                            $sign = md5('dlOrderAdd'.config('leveling.mayidailian.appid').$time.config('leveling.mayidailian.appsecret').config('leveling.mayidailian.Ver'));
                            // 公用的串信息
                            $publicStr = "method=dlOrderAdd&appid=".config('leveling.mayidailian.appid')."&appsecret=".config('leveling.mayidailian.appsecret')."&TimeStamp=".$time."&Ver=1.0&sign=".$sign;
                            $orderData = json_decode($orderData, true);
                            // 遍历结果，用 & 连接
                            $str = '';
                            foreach ($orderData as $key => $value) {
                                $str .= $key . '=' . $value . '&';
                            }
                            // 最后合成的参数
                            $orderData = $str.$publicStr;
                        }
                        $response = $client->request('POST', $platform['receive'], [
                           'form_params' => [
                               'data' => base64_encode(openssl_encrypt($orderData, 'aes-128-cbc', $platform['aes_key'], true, $platform['aes_iv'])),
                           ]
                        ]);
                        $result = $response->getBody()->getContents();
                        myLog('order-send-result', [$platform['name'], $result, $orderData]);
                    } catch (\Exception $exception) {
                        myLog('order-send-ex', [$platform['name'], $exception->getMessage()]);
                    }
                }
            }
        }
    }
}
