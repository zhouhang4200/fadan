<?php

namespace App\Console\Commands;


use App\Models\OrderSendResult;
use App\Services\RedisConnect;

use GuzzleHttp\Exception\ConnectException;
use Redis;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Events\OrderBasicData;
use Illuminate\Console\Command;
use Log, Config, Weight, Order;
use App\Models\GameLevelingOrder;
use App\Models\OrderSendChannel;
use \Exception;
/**
 * 订单发送
 * Class OrderAssign
 * @package App\Console\Commands
 */
class NewOrderSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new-order:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新的订单发送到各平台';

    protected $redis;

    public function handle()
    {
        $this->redis = RedisConnect::order();

        $client = new Client(['verify' => false]);

        while (1) {
            $orderData = $this->redis->lpop('new-order:send');

            if($orderData) {
                try {
                    $orderDatas = json_decode($orderData, true);
                    $order = GameLevelingOrder::where('trade_no', $orderDatas['order_no'])->first();

                    if ($order) {
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

                        foreach (config('partner.platform') as $third => $platform) {
                            myLog('new-order-send-setup', ['7' => $order->no]);

                            if (!in_array($third, $blackThirds) && !in_array($third, $managerBlackThirds)) {

                                myLog('new-order-send-setup', ['8' => $order->no, $platform['receive']]);

                                try {
                                    $decrypt = base64_encode(openssl_encrypt($orderData, 'aes-128-cbc', $platform['aes_key'], true, $platform['aes_iv']));
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
//                                            if ($third == 3) {
//                                                if (isset($arrResult['status']) && $arrResult['status'] != 1) {
//                                                    $orderDatas['notice_reason'] = $arrResult['message'] ?? '';
//                                                    $this->writeNotice($third, $orderDatas);
//                                                }
//
//                                                if (isset($arrResult['status']) && $arrResult['status'] == 1) {
//                                                    $this->sendResultRecord($orderDatas['order_no'], $platform['name'], $arrResult['message'], 2, $platform['name'] . '发布成功');
//                                                }
//                                            }

                                            // 91
//                                            if ($third == 1) {
//                                                if (isset($arrResult['result']) && $arrResult['result'] != 0) {
//                                                    $orderDatas['notice_reason'] = $arrResult['reason'] ?? '';
//                                                    $this->writeNotice($third, $orderDatas);
//                                                }
//
//                                                if (isset($arrResult['result']) && $arrResult['result'] == 0) {
//                                                    $this->sendResultRecord($orderDatas['order_no'], $platform['name'], $arrResult['data'], 2, $platform['name'] . '发布成功');
//                                                }
//                                            }

                                             // wanzi
//                                             if ($third == 5) {
//                                                 if (isset($arrResult['result']) && $arrResult['result'] != 0) {
//                                                     $orderDatas['notice_reason'] = $arrResult['reason'] ?? '';
//                                                     $this->writeNotice($third, $orderDatas);
//                                                 }
//
//                                                 if (isset($arrResult['result']) && $arrResult['result'] == 0) {
//                                                     $this->sendResultRecord($orderDatas['order_no'], $platform['name'], $arrResult['data'], 2, $platform['name'] . '发布成功');
//                                                 }
//                                             }

                                            if ($third == 5) {
                                                if (isset($arrResult['code']) && $arrResult['code'] != 1) {
                                                    $orderDatas['notice_reason'] = $arrResult['message'] ?? '';
                                                    $this->writeNotice($third, $orderDatas);
                                                }

                                                if (isset($arrResult['code']) && $arrResult['code'] == 1) {
                                                    $this->sendResultRecord($orderDatas['order_no'], $platform['name'], $arrResult['data']['order_no'], 2, $platform['name'] . '发布成功');
                                                }
                                            }

                                            // 373
//                                            if ($third == 4) {
//                                                if (isset($arrResult['code']) && $arrResult['code'] != 0) {
//                                                    $orderDatas['notice_reason'] = isset($arrResult['data']) && ! empty($arrResult['data']) ? $arrResult['data'] : ($arrResult['msg'] ?? '');
//                                                    $this->writeNotice($third, $orderDatas);
//                                                }
//
//                                                if (isset($arrResult['code']) && $arrResult['code'] == 0) {
//                                                    $this->sendResultRecord($orderDatas['order_no'], $platform['name'], $arrResult['data']['platformOrderNo'], 2, $platform['name'] . '发布成功');
//                                                }
//                                            }
                                        }
                                    }
                                    myLog('new-order-send-result-des', [ $platform['name'], $result]);
                                    myLog('new-order-send-result', [$orderData, $platform['name'], $result, $decrypt]);
                                } catch (ConnectException $exception) {
                                    $this->sendResultRecord($orderDatas['order_no'], $platform['name'], '', 1, $platform['name'] . '服务器异常');
                                    myLog('new-order-send-ex', ['订单' => $orderDatas['order_no'], 'message' => $exception->getMessage(), '行' => $exception->getLine()]);
                                } catch (Exception $exception) {
                                    $this->sendResultRecord($orderDatas['order_no'], $platform['name'], '', 1, $platform['name'] . '服务器异常');
                                    myLog('new-order-send-ex', ['订单' => $orderDatas['order_no'], 'message' => $exception->getMessage(), '行' => $exception->getLine()]);
                                }
                            } else {
                                // 写入发送记录
                                $this->sendResultRecord($orderDatas['order_no'], $platform['name'], '', 1, '该发布渠道已被您关闭');
                            }
                        }
                        // 写基础数据
                        event(new OrderBasicData($order));
                    } else {
                        // 没有查到订单则再次丢入队列
                        $this->redis->lpush('order:send', json_encode($orderData));
                        myLog('new-order-send-setup', ['10' => $orderDatas['order_no']]);
                    }
                } catch (\Exception $e) {
                    myLog('new-order-send-ex', ['订单' => $orderData, 'message' => $e->getMessage(), '行' => $e->getLine()]);
                }
            }
        }
    }

    /**
     * 发送到钉钉群通知
     * @param $third
     * @param $orderData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function writeNotice($third, $orderData)
    {
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

        $this->sendResultRecord($orderData['order_no'], config('order.third')[$third], '', 1, $orderData['notice_reason']);
    }

    private function sendResultRecord($orderNO, $thirdName, $thirdOrderNO, $status, $result)
    {
        OrderSendResult::create([
            'order_no' => $orderNO,
            'third_name' => $thirdName,
            'third_order_no' => $thirdOrderNO,
            'status' => $status,
            'result' => $result,
        ]);
    }
}
