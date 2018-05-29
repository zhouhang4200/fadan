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

                // 检测平台是否开启，
                $client = new Client();
                foreach (config('partner.platform') as $platform) {
                    // if ($platform['user_id'] != 8737) {
                        // myLog('send-who', ['platform' => $platform]);
                        $decrypt = base64_encode(openssl_encrypt($orderData, 'aes-128-cbc', $platform['aes_key'], true, $platform['aes_iv']));
                        try {
                            $response = $client->request('POST', $platform['receive'], [
                                'form_params' => [
                                    'data' => $decrypt
                                ]
                            ]);
                            $result = $response->getBody()->getContents();
//                        OrderSend::insert([
//                            'platform_name' => $platform['name'],
//                            'status' => 1,
//                            'send_result' => $result,
//                            'send_data' => $orderData,
//                        ]);
                            myLog('order-send-result-des', [$platform['name'], $result]);
                            // myLog('order-send-result', [$platform['name'], $platform['receive'], $result, $orderData, $decrypt]);
                        } catch (\Exception $exception) {
//                        OrderSend::insert([
//                            'platform_name' => $platform['name'],
//                            'status' => 0,
//                            'send_result' => $result,
//                            'send_data' => $orderData,
//                        ]);
                            myLog('order-send-ex', [$platform['name'], $exception->getMessage(), $decrypt]);
                        // }
                    }
                }
            }
        }
    }
}
