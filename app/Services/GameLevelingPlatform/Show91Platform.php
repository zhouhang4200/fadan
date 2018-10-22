<?php

namespace App\Services\GameLevelingPlatform;

use Exception;
use GuzzleHttp\Client;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingPlatform;
use App\Exceptions\GameLevelingOrderOperateException;

class Show91Platform implements GameLevelingPlatformInterface
{
    /**
     * 表单请求
     * @param $options
     * @param $url
     * @param $functionName
     * @param $order
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function formDataRequest($options, $url, $functionName, $order, $method = 'POST')
    {
        try {
            $data = [];
            foreach ($options as $name => $value) {
                $data[$name]['name'] = $name;
                $data[$name]['contents'] = $value;
            }
            $client = new Client();
            $response = $client->request($method, $url, [
                'multipart' => $data,
            ]);
            $result = $response->getBody()->getContents();

            myLog('show91-platform-request-log', ['地址' => $url, '信息' => $options, '结果' => $result]);

            if (! isset($result) || empty($result)) {
                throw new GameLevelingOrderOperateException('接口返回数据为空!');
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                        $errorMessage = $arrResult['reason'];
                        // 往钉钉群里发消息
                        $client = new Client();
                        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                            'json' => [
                                'msgtype' => 'text',
                                'text' => [
                                    'content' => '订单（内部单号：'.$order->trade_no. '）调用【show91平台】【'.$operateName = config('leveling.operate')[$functionName].'】接口失败:'.$errorMessage
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

                        if ($url != config('leveling.show91.url')['delete']) {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }

            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '表单请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 普通请求
     * @param $options
     * @param $url
     * @param $functionName
     * @param $order
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function normalRequest($options, $url, $functionName, $order, $method = 'POST')
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url, [ 'form_params' => $options,]);
            $result =  $response->getBody()->getContents();

            myLog('show91-platform-request-log', ['地址' => $url, '信息' => $options, '结果' => $result,]);

            if (! isset($result) || empty($result)) {
                throw new GameLevelingOrderOperateException('接口返回数据为空!');
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                        $errorMessage = $arrResult['reason'];
                        // 往钉钉群里发消息
                        $client = new Client();
                        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                            'json' => [
                                'msgtype' => 'text',
                                'text' => [
                                    'content' => '订单（内部单号：'.$order->trade_no. '）调用【show91平台】【'.config('leveling.operate')[$functionName].'】接口失败:'.$errorMessage
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

                        if ($url != config('leveling.show91.url')['delete']) {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 上架
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function onSale(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['onSale'], 'onSale', $order);

            return true;
        } catch (GameLevelingOrderOperateException $e) {
            static::delete($order);
            myLog('show91-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }  catch (Exception $e) {
            static::delete($order);
            myLog('show91-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 下架
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function offSale(GameLevelingOrder $order)
    {
        $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
            ->where('platform_id', 1)
            ->first();

        if (! $gameLevelingPlatform) {
            return true;
        }

        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['offSale'], 'offSale', $order);

            return true;
        } catch (GameLevelingOrderOperateException $e) {
            static::delete($order);
            myLog('show91-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }  catch (Exception $e) {
            static::delete($order);
            myLog('show91-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function take(GameLevelingOrder $order){}

    /**
     * 申请协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function applyConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account'              => config('leveling.show91.account'),
                'sign'                 => config('leveling.show91.sign'),
                'oid'                  => $order->platform_trade_no,
                'selfCancel.pay_price' => $order->gameLevelingOrderConsult->amount,
                'selfCancel.pay_bond'  => bcadd($order->gameLevelingOrderConsult->security_deposit, $order->gameLevelingOrderConsult->efficiency_deposit),
                'selfCancel.content'   => $order->gameLevelingOrderConsult->reason ?? '无',
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['applyConsult'], 'applyConsult', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '申请协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['cancelConsult'], 'cancelConsult', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '取消协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 同意协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function agreeConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
                'v'       => 1,
                'p'       => config('leveling.show91.password'),
            ];
            // 发送
            $result = static::normalRequest($options, config('leveling.show91.url')['agreeConsult'], 'agreeConsult', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '同意协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 不同意协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function refuseConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
                'v'       => 2,
                'p'       => config('leveling.show91.password'),
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['refuseConsult'], 'refuseConsult', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '不同意协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function forceDelete(GameLevelingOrder $order){}

    /**
     * 申请仲裁
     * @param GameLevelingOrder $order
     * @param array $pic
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function applyComplain(GameLevelingOrder $order, $pic = [])
    {
        try {
            $options = [
                'account'        => config('leveling.show91.account'),
                'sign'           => config('leveling.show91.sign'),
                'oid'            => $order->platform_trade_no,
                'appeal.title'   => '申请仲裁',
                'appeal.content' => $order->gameLevelingOrderComplain->reason ?? '无',
                'pic1'           => $pic['pic1'],
                'pic2'           => $pic['pic2'],
                'pic3'           => $pic['pic3'],
            ];
            // 发送
            static::formDataRequest($options, config('leveling.show91.url')['applyComplain'], 'applyComplain', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '申请仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消仲裁
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelComplain(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'aid'     => $order->platform_trade_no,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['cancelComplain'], 'cancelComplain', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '取消仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function arbitration(GameLevelingOrder $order){}
    public static function applyComplete(GameLevelingOrder $order){}
    public static function cancelComplete(GameLevelingOrder $order){}

    /**
     * 完成
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function complete(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
                'p'       => config('leveling.show91.password'),
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['complete'], 'complete', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单完成',  '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    public static function lock(GameLevelingOrder $order){}
    public static function cancelLock(GameLevelingOrder $order){}
    public static function anomaly(GameLevelingOrder $order){}
    public static function cancelAnomaly(GameLevelingOrder $order){}

    /**
     * 撤单
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function delete(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['delete'], 'delete', $order);

            return true;
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '删除订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 修改订单
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function modifyOrder(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'order_no'                         => $gameLevelingPlatform->platform_trade_no,
                'game_name'                        => $order->gameLevelingOrderDetail->game_name,
                'game_region'                      => $order->gameLevelingOrderDetail->game_region_name,
                'game_serve'                       => $order->gameLevelingOrderDetail->game_server_name,
                'game_account'                     => $order->game_account,
                'game_password'                    => $order->game_password,
                'game_leveling_type'               => $order->gameLevelingOrderDetail->game_leveling_type_name,
                'game_leveling_title'              => $order->title,
                'game_leveling_price'              => $order->amount,
                'game_leveling_day'                => $order->day,
                'game_leveling_hour'               => $order->hour,
                'game_leveling_security_deposit'   => $order->security_deposit,
                'game_leveling_efficiency_deposit' => $order->efficiency_deposit,
                'game_leveling_requirements'       => $order->gameLevelingOrderDetail->requirement,
                'game_leveling_instructions'       => $order->gameLevelingOrderDetail->explain,
                'businessman_phone'                => $order->gameLevelingOrderDetail->user_phone,
                'businessman_qq'                   => $order->gameLevelingOrderDetail->user_qq,
                'order_password' 				   => $order->take_order_password,
                'game_role'						   => $order->game_role,
            ];

            $options = json_encode($options);

            $client = new Client();
            $response = $client->request('POST', config('leveling.show91.url')['modifyOrder'], [
                'form_params' => [
                    'data' => base64_encode(openssl_encrypt($options, 'aes-128-cbc', config('leveling.show91.aes_key'), true, config('leveling.show91.aes_iv'))),
                    "platformSign" => config('leveling.show91.platform-sign'),
                ],
                'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            myLog('show91-platform-modify-order-result', ['请求参数' => $options, '地址' => config('leveling.show91.url')['updateOrder'], '结果' => $result ?? '']);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '修改订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 加时
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addTime(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
                'day'     => $order->day,
                'hour'    => $order->hour,
            ];

            // 发送
            static::normalRequest($options, config('leveling.show91.url')['addTime'], 'addTime', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单加时', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 加款
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addAmount(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
                'appwd'   => config('leveling.show91.password'),
                'cash'    => $order->amount,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['addAmount'], 'addAmount', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单加款', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取订单详情
     * @param GameLevelingOrder $order
     * @return bool|mixed
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getOrderDetail(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
            ];
            // 发送
            return static::normalRequest($options, config('leveling.show91.url')['getOrderDetail'], 'getOrderDetail', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取订单截图
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getScreenShot(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
            ];
            // 发送
            $dataList = static::normalRequest($options, config('leveling.show91.url')['getScreenShot'], 'getScreenShot', $order);

            if (isset($dataList) && $dataList['result'] ==0 && !empty($dataList['data'])) {
                foreach ($dataList['data'] as $key => $value) {
                    $imageList[] = [
                        'url' => $value['url'],
                        'username' => $value['userName'],
                        'created_at' => $value['created_on'],
                        'description' => '',
                    ];
                }
                return $imageList;
            } else {
                return [];
            }
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取留言
     * @param GameLevelingOrder $order
     * @return array|string
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getMessage(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
            ];
            // 发送
            $message = static::normalRequest($options, config('leveling.show91.url')['getMessage'], 'getMessage', $order);

            if (isset($message) && isset($message['result']) && $message['result'] == 0 && isset($message['data'])) {
                $sortField = [];
                $messageArr = [];
                foreach ($message['data'] as $item) {
                    if (isset($item['id'])) {
                        $sortField[] = $item['created_on'];
                    } else {
                        $sortField[] = 0;
                    }
                    $messageArr[] = [
                        'sender' =>  isset($item['uid']) && $item['uid'] == config('show91.uid') ? '您': ($item['userNickname'] == '系统留言' ? '系统留言' :  '打手'),
                        'send_content' => $item['mess'],
                        'send_time' => $item['created_on'],
                    ];
                }
                // 用ID倒序
                array_multisort($sortField, SORT_ASC, $messageArr);

                return $messageArr;
            }
            return '';
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 回复留言
     * @param GameLevelingOrder $order
     * @param $message
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function replyMessage(GameLevelingOrder $order, $message)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
                'mess'    => $message,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['replyMessage'], 'replyMessage', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 修改账号密码
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function modifyGamePassword(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account'   => config('leveling.show91.account'),
                'sign'      => config('leveling.show91.sign'),
                'oid'       => $order->platform_trade_no,
                'newAcc'    => $order->game_account,
                'newAccPwd' => $order->game_password,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['modifyGamePassword'], 'modifyGamePassword', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 置顶
     * @param $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function setTop($order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 1)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $gameLevelingPlatform->platform_trade_no,
                'isTop'   => 1,
            ];
            // 发送
            static::normalRequest($options, config('leveling.show91.url')['setTop'], 'setTop', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '订单置顶', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function sendCompleteImage(GameLevelingOrder $order, $file){}


    /**
     * 获取仲裁详情
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getComplainDetail(GameLevelingOrder $order)
    {
        try {
            $options = [
                'account' => config('leveling.show91.account'),
                'sign'    => config('leveling.show91.sign'),
                'oid'     => $order->platform_trade_no,
            ];

            // 获取详情
            $result = static::normalRequest($options, config('leveling.show91.url')['getComplainDetail'], 'getComplainDetail', $order);

            $details = [];
            if (isset($result) && $result['result'] == 0 && isset($result['data']) && isset($result['data']['evis'])) {
                $details = $result['data'];
            }

            if (isset($details['appeal']['pic1'])) {
                $details['appeal']['pic1'] = config('leveling.show91.api_url') . '/gameupload/appeal/'.$details['appeal']['uid'].'/'.$details['appeal']['pic1'];
            }

            if (isset($details['appeal']['pic2'])) {
                $details['appeal']['pic2'] = config('leveling.show91.api_url') . '/gameupload/appeal/'.$details['appeal']['uid'].'/'.$details['appeal']['pic2'];
            }

            if (isset($details['appeal']['pic3'])) {
                $details['appeal']['pic3'] = config('leveling.show91.api_url') . '/gameupload/appeal/'.$details['appeal']['uid'].'/'.$details['appeal']['pic3'];
            }

            if (! isset($details) || ! is_array($details) || ! isset($details['appeal']) || count($details) < 1) {
                return '暂无相关信息';
            }

            $arr = [];
            $arr['detail']['who'] = config('leveling.show91.uid') == $details['appeal']['uid'] ? '我方' : ($details['appeal']['uid'] == 0 ? '系统留言' : '对方');
            $arr['detail']['created_at'] = $details['appeal']['created_on'];
            $arr['detail']['content'] = $details['appeal']['content'];
            $arr['detail']['arbitration_id'] = $details['appeal']['id'];
            $arr['detail']['pic1'] = $details['appeal']['pic1'];
            $arr['detail']['pic2'] = $details['appeal']['pic2'];
            $arr['detail']['pic3'] = $details['appeal']['pic3'];

            if (isset($details['evis'])) {
                foreach($details['evis'] as $k => $detail) {
                    $arr['info'][$k]['who'] = config('leveling.show91.uid') == $detail['uid'] ? '我方' : ($detail['uid'] == 0 ? '系统留言' : '对方');
                    $arr['info'][$k]['created_at'] = $detail['created_on'];
                    $arr['info'][$k]['content'] = $detail['content'];
                    $arr['info'][$k]['pic'] = isset($detail['pic']) ? config('leveling.show91.api_url') . '/gameupload/appeal/' . $detail['uid'] . '/' . $detail['pic'] : '';
                }
            }
            return $arr;
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '获取仲裁详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 添加仲裁证据
     * @param GameLevelingOrder $order
     * @param $pic
     * @param $content
     * @param string $complainId
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addComplainDetail(GameLevelingOrder $order, $pic, $content, $complainId = '')
    {
        try {
            $options = [
                'account'           => config('leveling.show91.account'),
                'sign'              => config('leveling.show91.sign'),
                'appealEvi.aid'     => $complainId,
                'appealEvi.content' => $content,
                'pic1'              => !empty($pic) ? base64ToBlob($pic) : '',
            ];

            // 发送
            $res = static::formDataRequest($options, config('leveling.show91.url')['addComplainDetail'], 'addComplainDetail', $order);
        } catch (Exception $e) {
            myLog('show91-platform-error', ['方法' => '添加仲裁证据', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    public static function sendImage(GameLevelingOrder $order, $pic = [])
    {
        // TODO: Implement sendImage() method.
    }
}
