<?php

namespace App\Services\GameLevelingPlatform;

use App\Models\GameLevelingOrder;
use App\Exceptions\GameLevelingOrderOperateException;

class WanZiPlatform implements GameLevelingPlatformInterface
{
    /**
     * 表单发送
     * @param $options
     * @param $url
     * @param $functionName
     * @param $order
     * @param string $method
     * @return mixed
     * @throws GameLevelingOrderOperateException
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

            myLog('wanzi-request-result', ['地址' => $url, '信息' => $options, '结果' => $result,]);

            if (! isset($result) || empty($result)) {
                throw new GameLevelingOrderOperateException('接口返回数据为空！');
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['code']) && $arrResult['code'] != 1) {
                        $errorMessage = $arrResult['message'] ?? '丸子接口返回错误';

                        // 往群里发消息
                        // $client = new Client();
                        // $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                        //     'json' => [
                        //         'msgtype' => 'text',
                        //         'text' => [
                        //             'content' => '订单（内部单号：'.$order->trade_no. '）调用【丸子平台】【'.config('leveling.operate')[$functionName.'】接口失败:'.$errorMessage
                        //         ],
                        //         'at' => [
                        //             'isAtAll' => false,
                        //             "atMobiles" =>  [
                        //                 "18500132452",
                        //                 "13437284998",
                        //                 "13343450907"
                        //             ]
                        //         ]
                        //     ]
                        // ]);

                        if ($url != config('leveling.wanzi.url')['delete']) {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
     * @throws GameLevelingOrderOperateException
     */
    public static function normalRequest($options, $url, $functionName, $order, $method = 'POST')
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url, ['form_params' => $options,]);
            $result =  $response->getBody()->getContents();

            myLog('wanzi-request-result', ['地址' => $url, '信息' => $options, '结果' => $result,]);

            if (! isset($result) || empty($result)) {
                throw new GameLevelingOrderOperateException('接口返回数据为空!');
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['code']) && $arrResult['code'] != 1) {
                        $errorMessage = $arrResult['message'] ?? '丸子接口返回错误';

                        // 往群里发消息
                        // $client = new Client();
                        // $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                        //     'json' => [
                        //         'msgtype' => 'text',
                        //         'text' => [
                        //             'content' => '订单（内部单号：'.$order->trade_no. '）调用【丸子平台】【'.config('leveling.operate')[$functionName].'】接口失败:'.$errorMessage
                        //         ],
                        //         'at' => [
                        //             'isAtAll' => false,
                        //             "atMobiles" =>  [
                        //                 "18500132452",
                        //                 "13437284998",
                        //                 "13343450907"
                        //             ]
                        //         ]
                        //     ]
                        // ]);

                        if ($url != config('leveling.wanzi.url')['delete']) {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 生成签名
     * @param array $options
     * @return string
     */
    public static function getSign($options = [])
    {
        ksort($options);
        $str = '';
        foreach ($options as $key => $value) {
            $str .= $key . '=' . $value . '&';
        }
        return md5(rtrim($str,  '&') . config('leveling.wanzi.app_secret'));
    }

    /**
     * 上架
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function onSale(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['onSale'], 'onSale', $order);

            return true;
        } catch (GameLevelingOrderOperateException $e) {
            static::delete($order);
            myLog('wanzi-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }  catch (Exception $e) {
            static::delete($order);
            myLog('wanzi-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 下架
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function offSale(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['offSale'], 'offSale', $order);

            return true;
        } catch (GameLevelingOrderOperateException $e) {
            static::delete($order);
            myLog('wanzi-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }  catch (Exception $e) {
            static::delete($order);
            myLog('wanzi-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function take(GameLevelingOrder $order){}

    /**
     * 申请协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function applyConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'         => config('leveling.wanzi.app_id'),
                'order_no'       => $order->platform_trade_no,
                'amount'         => $order->gameLevelingOrderConsult->amount,
                'double_deposit' => bcadd($order->gameLevelingOrderConsult->security_deposit, $order->gameLevelingOrderConsult->efficiency_deposit),
                'reason'         => $order->gameLevelingOrderConsult->reason ?? '无',
                'timestamp'      => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['applyConsult'], 'applyConsult', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '申请协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function cancelConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['cancelConsult'], 'cancelConsult', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '取消协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 同意协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function agreeConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            $result = static::normalRequest($options, config('leveling.wanzi.url')['agreeConsult'], 'agreeConsult', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '同意协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 不同意协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function refuseConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['refuseConsult'], 'refuseConsult', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '不同意协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function forceDelete(GameLevelingOrder $order){}

    /**
     * 申请仲裁
     * @param GameLevelingOrder $order
     * @param $pic
     * @throws GameLevelingOrderOperateException
     */
    public static function applyComplain(GameLevelingOrder $order, $pic)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
                'reason' => $order->gameLevelingOrderComplain->reason ?? '无',
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            $options['pic1'] = $pic['pic1'] ?? null;
            $options['pic2'] = $pic['pic2'] ?? null;
            $options['pic3'] = $pic['pic3'] ?? null;

            // 发送
            static::formDataRequest($options, config('leveling.wanzi.url')['applyComplain'], 'applyComplain', $order);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '申请仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function cancelComplain(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['cancelComplain'], 'cancelComplain', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '取消仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
     */
    public static function complete(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['complete'], 'complete', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单完成', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 锁定
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function lock(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['lock'], 'lock', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '锁定', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消锁定
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function cancelLock(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['cancelLock'], 'cancelLock', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '取消锁定', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function anomaly(GameLevelingOrder $order){}
    public static function cancelAnomaly(GameLevelingOrder $order){}

    /**
     * 撤单
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function delete(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['delete'], 'delete', $order);

            return true;
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '删除订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 修改订单
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function modifyOrder(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
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
            $response = $client->request('POST', config('leveling.wanzi.url')['modifyOrder'], [
                'form_params' => [
                    'data' => base64_encode(openssl_encrypt($options, 'aes-128-cbc', config('leveling.wanzi.aes_key'), true, config('leveling.wanzi.aes_iv')))
                ],
                'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            myLog('wanzi-modify-order-result', ['请求参数' => $options, '地址' => config('leveling.wanzi.url')['modifyOrder'], '结果' => $result ?? '']);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '修改订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 加时
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function addTime(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
                'day'     => $order->day,
                'hour'    => $order->hour,
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;

            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['addTime'], 'addTime', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单加时', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 加价
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function addAmount(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
                'amount'    => $order->amount,
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['addAmount'], 'addAmount', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单加款', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 订单详情
     * @param GameLevelingOrder $order
     * @return mixed
     * @throws GameLevelingOrderOperateException
     */
    public static function getOrderDetail(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            return static::normalRequest($options, config('leveling.wanzi.url')['getOrderDetail'], 'getOrderDetail', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取订单截图
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     */
    public static function getScreenShot(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            $dataList = static::normalRequest($options, config('leveling.wanzi.url')['getScreenShot'], 'getScreenShot', $order);

            if (isset($dataList) && $dataList['code'] == 1 && !empty($dataList['data'])) {
                foreach ($dataList['data'] as $key => $value) {
                    $imageList[] = [
                        'url' => $value['url'],
                        'username' => $value['username'],
                        'created_at' => $value['created_at'],
                        'description' => '',
                    ];
                }
                return $imageList;
            } else {
                return [];
            }
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '获取订单完成截图', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取留言
     * @param GameLevelingOrder $order
     * @return array|string
     * @throws GameLevelingOrderOperateException
     */
    public static function getMessage(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            $message = static::normalRequest($options, config('leveling.wanzi.url')['getMessage'], 'getMessage', $order);

            if (isset($message) && isset($message['code']) && $message['code'] == 1 && isset($message['data'])) {
                $sortField = [];
                $messageArr = [];
                foreach ($message['data'] as $item) {
                    if (isset($item['id'])) {
                        $sortField[] = $item['send_time'];
                    } else {
                        $sortField[] = 0;
                    }
                    $messageArr[] = [
                        'sender' =>  $item['sender'] == '接单方' ? '打手' : ($item['sender'] == '工作人员' ? '系统留言' :  '您'),
                        'send_content' => $item['send_content'],
                        'send_time' => $item['send_time'],
                    ];
                }
                // 用ID倒序
                array_multisort($sortField, SORT_ASC, $messageArr);

                return $messageArr;
            }
            return '';
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 回复留言
     * @param GameLevelingOrder $order
     * @param $message
     * @throws GameLevelingOrderOperateException
     */
    public static function replyMessage(GameLevelingOrder $order, $message)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
                'message' => $message,
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['replyMessage'], 'replyMessage', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 修改账号密码
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     */
    public static function modifyGamePassword(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
                'amount'    => $order->amount,
                'account'   => $order->game_account,
                'password'  => $order->game_password,
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['modifyGamePassword'], 'modifyGamePassword', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    public static function sendImage(GameLevelingOrder $order, $pic){}

    /**
     * 置顶
     * @param $orderDatas
     * @return bool
     * @throws GameLevelingOrderOperateException
     */
    public static function setTop($orderDatas)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 5)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['setTop'], 'setTop', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '订单置顶', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取仲裁详情
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     */
    public static function getComplainDetail(GameLevelingOrder $order)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;

            // 发送
            $result = static::normalRequest($options, config('leveling.wanzi.url')['getComplainDetail'], 'getComplainDetail', $order);
            $details = [];
            if (isset($result) && $result['code'] == 1 && isset($result['data'])) {
                $details = $result['data'];
            }

            if (! isset($details) || ! is_array($details) || count($details) < 1) {
                return '暂无相关信息';
            }


            $arr = [];
            $arr['detail']['who'] = $details['who'] == 1 ? '我方' : ($details['who'] == 2 ? '对方' : '系统留言');
            $arr['detail']['created_at'] = $details['created_at'];
            $arr['detail']['content'] = $details['content'];
            $arr['detail']['arbitration_id'] = $details['arbitration_id'];
            $arr['detail']['pic1'] = '';
            $arr['detail']['pic2'] = '';
            $arr['detail']['pic3'] = '';

            if ($details['image']) {
                $arr['detail']['pic1'] = $details['image']['pic1'];
                $arr['detail']['pic2'] = $details['image']['pic2'];
                $arr['detail']['pic3'] = $details['image']['pic3'];
            }


            if (isset($details['message'])) {
                foreach($details['message'] as $k => $message) {
                    $arr['info'][$k]['who'] = $message['who'] == 1 ? '我方' : ($message['who'] == 2 ? '对方' : '系统留言');
                    $arr['info'][$k]['created_at'] = $message['created_at'];
                    $arr['info'][$k]['content'] = $message['content'];
                    $arr['info'][$k]['pic'] = $message['pic'] ?? '';
                }
            }
            return $arr;
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '获取仲裁详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 增加仲裁证据
     * @param GameLevelingOrder $order
     * @param $pic
     * @param $content
     * @return mixed
     * @throws GameLevelingOrderOperateException
     */
    public static function addComplainDetail(GameLevelingOrder $order, $pic, $content)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $order->platform_trade_no,
                'timestamp' => time(),
                'reason'    => $content,
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            $options['image'] = !empty($pic) ? $pic : '';

            // 发送
            return static::formDataRequest($options, config('leveling.wanzi.url')['addComplainDetail'], 'addComplainDetail', $order);
        } catch (Exception $e) {
            myLog('wanzi-platform-error', ['方法' => '添加仲裁证据', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }
}
