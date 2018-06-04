<?php

namespace App\Console\Commands;


use App\Services\RedisConnect;

use Redis;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use App\Models\Order as OrderModel;
use App\Models\OrderDetail;
use App\Models\OrderSendChannel;
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
                try {
                    $orderDatas = json_decode($orderData, true);
                    $order = OrderModel::where('no', $orderDatas['order_no'])->first();

                    // 检测平台是否开启，
                    $orderSendChannel = OrderSendChannel::where('user_id', $order->creator_primary_user_id)
                        ->where('game_id', $order->game_id)
                        ->first();

                    $managerSetChannel = OrderSendChannel::where('user_id', 0)
                        ->where('game_id', $order->game_id)
                        ->first();

                    $blackThirds = [];
                    $managerBlackThirds = [];
                    if (isset($orderSendChannel) && isset($orderSendChannel->third)) {
                        $blackThirds = explode('-', $orderSendChannel->third); // 黑名单
                    }

                    if (isset($managerSetChannel) && isset($managerSetChannel->third)) {
                        $managerBlackThirds = explode('-', $managerSetChannel->third); // 全局黑名单
                    }
                    $client = new Client();
                    foreach (config('partner.platform') as $third => $platform) {
                        if (in_array($third, $blackThirds) || in_array($third, $managerBlackThirds)) {
                            continue;
                        }
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
                                    $orderDatas['notice_reason'] = '空';
                                    // 蚂蚁订单
                                    if ($third == 3) {
                                        if (isset($arrResult['status']) && $arrResult['status'] != 1) {
                                            $orderDatas['notice_reason'] = $arrResult['message'] ?? '';
                                            $this->writeNotice($third, $orderDatas);
                                        }
                                    }

                                    // 91
                                    if ($third == 1) {
                                        if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                                            $orderDatas['notice_reason'] = $arrResult['reason'] ?? '';
                                            $this->writeNotice($third, $orderDatas);
                                        }
                                    }

                                    // wanzi
                                    if ($third == 5) {
                                        if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                                            $orderDatas['notice_reason'] = $arrResult['reason'] ?? '';
                                            $this->writeNotice($third, $orderDatas);
                                        }
                                    }

                                    // 373
                                    if ($third == 4) {
                                        if (isset($arrResult['code']) && $arrResult['code'] != 0) {
                                            $orderDatas['notice_reason'] = $arrResult['msg'] ?? '';
                                            $this->writeNotice($third, $orderDatas);
                                        }
                                    }
                                }
                            }
                            myLog('order-send-result-des', [$orderDatas['order_no'], $platform['name'], $result]);

                        } catch (\Exception $exception) {
                            myLog('order-send-ex', [$platform['name'], $exception->getMessage()]);
                        }
                    }

                } catch (\Exception $e) {
                    myLog('order-send-ex', ['no' => $orderDatas['order_no'] ?? '', 'message' => $e->getMessage()]);
                }
            }
        }
    }

    public function writeNotice($third, $orderData)
    {
        $order = OrderModel::where('no', $orderData['order_no'])->first();
        $orderDetail = OrderDetail::where('order_no', $orderData['order_no'])->pluck('field_value', 'field_name')->toArray();
        // 记录报警
        $orderData['order_status']     = 1;
        $orderData['source_order_no']  = $orderDetail['source_order_no'];
        $orderData['order_created_at'] = $order->created_at->toDateTimeString();

        $name = "order:order-api-notices";
        $key = $orderData['order_no'].'-'.$third.'-'.'create';
        $value = json_encode(['third' => $third, 'reason' => $orderData['notice_reason'], 'functionName' => 'create', 'datas' => $orderData]);

        Redis::hSet($name, $key, $value);

         // 往群里发消息
        $client = new Client();
        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
            'json' => [
                'msgtype' => 'text',
                'text' => [
                    'content' => '订单（内部单号：'.$orderData['order_no']. '）调用【'.config('order.third')[$third].'】【下单】接口失败:'.$orderData['notice_reason']
                ],
                'at' => [
                    'isAtAll' => false,
                    "atMobiles" =>  [
                        "18500132452",
                        "13437284998",
                        "13343450907"
                    ]
                ]
            ]
        ]);

        myLog('reason', ['third' => $third, 'no' => $order->no, 'reason' => $orderData['notice_reason']]);
    }
}
