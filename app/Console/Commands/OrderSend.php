<?php

namespace App\Console\Commands;


use App\Services\RedisConnect;

use Redis;
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
                foreach (config('partner.platform') as $third => $platform) {
                    $decrypt = base64_encode(openssl_encrypt($orderData, 'aes-128-cbc', $platform['aes_key'], true, $platform['aes_iv']));
                    try {
                        $response = $client->request('POST', $platform['receive'], [
                            'form_params' => [
                                'data' => $decrypt
                            ]
                        ]);
                        $result = $response->getBody()->getContents();

                        if (isset($result) && ! empty($result)) {
                            $arrResult = json_decode($result, true);

                            if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                                $orderData = json_decode($orderData, true);
                                // 蚂蚁订单
                                if ($third == 3) {
                                    if (isset($arrResult['status']) && $arrResult['status'] != 1) {
                                        $orderData['notice_reason'] = $arrResult['message'] ?? '';
                                        $this->writeNotice($third, $orderData);
                                    }
                                }

                                // 91 
                                if ($third == 1) {
                                    if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                                        $orderData['notice_reason'] = $arrResult['reason'] ?? '';
                                        $this->writeNotice($third, $orderData);
                                    }
                                }

                                // wanzi
                                if ($third == 5) {
                                    if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                                        $orderData['notice_reason'] = $arrResult['reason'] ?? '';
                                        $this->writeNotice($third, $orderData);
                                    }
                                }

                                // 373
                                if ($third == 4) {
                                    if (isset($arrResult['code']) && $arrResult['code'] != 0) {
                                        $orderData['notice_reason'] = $arrResult['msg'] ?? '';
                                        $this->writeNotice($third, $orderData);
                                    }
                                }
                            }
                        }

                        myLog('order-send-result-des', [$platform['name'], $result]);

                    } catch (\Exception $exception) {
                        myLog('order-send-ex', [$platform['name'], $exception->getMessage(), $decrypt]);
                    }
                }
            }
        }
    }

    public function writeNotice($third, $orderData)
    {
        $order = Order::where('no', $orderData['no'])->first();
        $orderDetail = OrderDetail::where('order_no', $orderData['no'])->pluck('field_value', 'field_name')->toArray();
        // 记录报警
        $orderData['status']           = 1;
        $orderData['order_no']         = $order->no;
        $orderData['source_order_no']  = $orderDetail['source_order_no'];
        $orderData['order_created_at'] = $order->created_at;


        $name = "order:order-api-notices";
        $key = $orderData['order_no'].'-'.$third.'-'.$functionName;
        $value = json_encode(['third' => $third, 'reason' => $orderData['notice_reason'], 'functionName' => 'create', 'datas' => $orderData]);

        Redis::hSet($name, $key, $value);
    }
}
