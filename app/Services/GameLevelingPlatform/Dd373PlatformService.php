<?php

namespace App\Services\GameLevelingPlatform;

use Exception;
use GuzzleHttp\Client;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingPlatform;
use App\Exceptions\GameLevelingOrderOperateException;

class Dd373PlatformService implements GameLevelingPlatformServiceInterface
{
    /**
     * 表单发送模式
     * @param $options
     * @param $url
     * @param $order
     * @param string $functionName
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function formDataRequest($options, $url, $order, $functionName = '', $method = 'POST')
    {
        try {
            $data = [];
            foreach ($options as $name => $value) {
                $data[$name]['name'] = $name;
                $data[$name]['contents'] = $value;
            }
            myLog('dd373-platform-request', ['begin']);
            // 发送
            $client = new Client();
            $response = $client->request($method, $url, ['multipart' => $data]);
            $result = $response->getBody()->getContents();
            myLog('dd373-platform-result', [$url, $data, $result]);

            // 空结果
            if (!isset($result) || empty($result)) {
                if ($url != config('gameleveling.dd373.url')['delete']) {
                    throw new GameLevelingOrderOperateException('调用dd373的multipart请求，返回结果不存在!');
                }
            }

            // 有结果
            if (isset($result) && !empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['code']) && $arrResult['code'] != 0) {
                        $errorMessage = $arrResult['msg'];
                        $operateName = config('gameleveling.operate')[$functionName];

                        // 往钉钉群里发消息
                        $client = new Client();
                        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                            'json' => [
                                'msgtype' => 'text',
                                'text' => [
                                    'content' => '订单（内部单号：' . $order->trade_no . '）调用【DD373平台】【' . $operateName . '】接口失败:' . $errorMessage
                                ],
                                'at' => [
                                    'isAtAll' => false,
                                    "atMobiles" => [
                                        "18500132452",
                                        "13437284998",
                                        "13343450907"
                                    ]
                                ]
                            ]
                        ]);

                        if ($url != config('gameleveling.dd373.url')['delete']) {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }
            // 正常返回
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '表单请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 普通请求
     * @param $options
     * @param $url
     * @param $order
     * @param string $functionName
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function normalRequest($options, $url, $order, $functionName = '', $method = 'POST')
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url, [
                'form_params' => $options,
                'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            if (!isset($result) || empty($result)) {
                if ($url != config('gameleveling.dd373.url')['delete']) {
                    throw new GameLevelingOrderOperateException('调用dd373的normal请求，返回结果不存在!');
                }
            }

            myLog('dd373-platform-result', [$url, $options, $result]);

            if (isset($result) && !empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['code']) && $arrResult['code'] != 0) {
                        $errorMessage = $arrResult['msg'];
                        $operateName = config('gameleveling.operate')[$functionName];

                        // 往群里发消息
                        $client = new Client();
                        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                            'json' => [
                                'msgtype' => 'text',
                                'text' => [
                                    'content' => '订单（内部单号：' . $order->trade_no . '）调用【DD373平台】【' . $operateName . '】接口失败:' . $errorMessage
                                ],
                                'at' => [
                                    'isAtAll' => false,
                                    "atMobiles" => [
                                        "18500132452",
                                        "13437284998",
                                        "13343450907"
                                    ]
                                ]
                            ]
                        ]);

                        if ($url != config('gameleveling.dd373.url')['delete']) {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '表单请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 加工参数
     * @param $options
     * @return array
     */
    public static function handleOptions($options)
    {
        $sign = md5("JsonData=" . json_encode($options) . "&platformSign=" . config('gameleveling.dd373.platform-sign') . config('gameleveling.dd373.key'));

        return [
            'JsonData' => json_encode($options),
            'platformSign' => config('gameleveling.dd373.platform-sign'),
            'Sign' => $sign,
        ];
    }

    /**
     * 上架
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public static function onSale(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'platformOrderNo' => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['onSale'], $order, 'onSale');
            return true;
        } catch (GameLevelingOrderOperateException $e) {
            myLog('dd373-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            static::delete($order);
            throw new GameLevelingOrderOperateException($e->getMessage());
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            static::delete($order);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 下架
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function offSale(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'platformOrderNo' => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['offSale'], $order, 'offSale');
            return true;
        } catch (GameLevelingOrderOperateException $e) {
            myLog('dd373-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            static::delete($order);
            throw new GameLevelingOrderOperateException($e->getMessage());
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            static::delete($order);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 申请协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function applyConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'payAmount' => $order->gameLevelingOrderConsult->amount,
                'guarantyAQFee' => $order->gameLevelingOrderConsult->security_deposit,
                'guarantyXLFee' => $order->gameLevelingOrderConsult->efficiency_deposit,
                'PayUserType' => 1,
                'reason' => $order->gameLevelingOrderConsult->reason ?? '无',
                'timestamp' => time(),
            ];

            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['applyConsult'], $order, 'applyConsult');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '申请撤销', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'State' => 2,
                'reason' => $order->gameLevelingOrderConsult->reason ?? '无',
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['cancelConsult'], $order, 'cancelConsult');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '取消撤销', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 同意协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function agreeConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'State' => 1,
                'reason' => '无',
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            $result = static::normalRequest($options, config('gameleveling.dd373.url')['agreeConsult'], $order, 'agreeConsult');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '同意撤销', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 不同意协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function rejectConsult(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'State' => 3,
                'reason' => '空',
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['rejectConsult'], $order, 'rejectConsult');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '不同意撤销', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 申请仲裁
     * @param GameLevelingOrder $order
     * @param array $pic
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function applyComplain(GameLevelingOrder $order, $pic = [])
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'reason' => $order->gameLevelingOrderComplain->reason ?? '无',
                'timestamp' => time(),
            ];
            // 对图片进行处理
            $finalPic['fileBase1'] = $pic['pic1'];
            $finalPic['fileBase2'] = $pic['pic2'];
            $finalPic['fileBase3'] = $pic['pic3'];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::formDataRequest(array_merge($options, array_filter($finalPic)), config('gameleveling.dd373.url')['applyComplain'], $order, 'applyComplain');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '申请仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消仲裁
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelComplain(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'reason' => '无',
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['cancelComplain'], $order, 'cancelComplain');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '申请仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 完成
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function complete(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['complete'], $order, 'complete');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单完成', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 锁定
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function lock(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['lock'], $order, 'lock');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '锁定', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消锁定
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelLock(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['cancelLock'], $order, 'cancelLock');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '解除锁定', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 撤单
     * @param GameLevelingOrder $order
     * @return bool
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function delete(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'platformOrderNo' => $gameLevelingPlatform->platform_trade_no,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['delete'], $order, 'delete');
            return true;
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '删除订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
        }
    }

    /**
     * 修改订单
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function modifyOrder(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'order_no' => $gameLevelingPlatform->platform_trade_no,
                'game_name' => $order->gameLevelingOrderDetail->game_name,
                'game_region' => $order->gameLevelingOrderDetail->game_region_name,
                'game_serve' => $order->gameLevelingOrderDetail->game_server_name,
                'game_account' => $order->game_account,
                'game_password' => $order->game_password,
                'game_leveling_type' => $order->gameLevelingOrderDetail->game_leveling_type_name,
                'game_leveling_title' => $order->title,
                'game_leveling_price' => $order->amount,
                'game_leveling_day' => $order->day,
                'game_leveling_hour' => $order->hour,
                'game_leveling_security_deposit' => $order->security_deposit,
                'game_leveling_efficiency_deposit' => $order->efficiency_deposit,
                'game_leveling_requirements' => $order->gameLevelingOrderDetail->requirement,
                'game_leveling_instructions' => $order->gameLevelingOrderDetail->explain,
                'businessman_phone' => $order->gameLevelingOrderDetail->user_phone,
                'businessman_qq' => $order->gameLevelingOrderDetail->user_qq,
            ];

            $options = json_encode($options);

            $client = new Client();
            $response = $client->request('POST', config('gameleveling.dd373.url')['modifyOrder'], [
                'form_params' => [
                    'data' => base64_encode(openssl_encrypt($options, 'aes-128-cbc', config('gameleveling.dd373.aes_key'), true, config('gameleveling.dd373.aes_iv'))),
                    "platformSign" => config('gameleveling.dd373.platform-sign'),
                ],
                'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            // 记录日志
            myLog('dd373-platform-modify-order-result', [
                '修改订单信息' => $options ?? '',
                '结果' => $result ?? '',
            ]);
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '修改订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 加时
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addTime(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'platformOrderNo' => $gameLevelingPlatform->platform_trade_no,
                'days' => $order->day,
                'hours' => $order->hour,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['addTime'], $order, 'addTime');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单加时', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 加代练金额
     * @param GameLevelingOrder $order
     * @return bool
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addAmount(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'platformOrderNo' => $gameLevelingPlatform->platform_trade_no,
                'add_money' => $order->amount,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['addAmount'], $order, 'addAmount');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单加款', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 订单详情
     * @param GameLevelingOrder $order
     * @return bool|mixed
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function orderInfo(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 4)
                ->first();

            if (!$gameLevelingPlatform) {
                return true;
            }

            $options = [
                'platformOrderNo' => $gameLevelingPlatform->platform_trade_no,
                'platformSign' => config('gameleveling.dd373.platform-sign'),
            ];
            $str = "platformOrderNo=" . $gameLevelingPlatform->platform_trade_no . "&platformSign=" . config('gameleveling.dd373.platform-sign') . config('gameleveling.dd373.key');

            $options['Sign'] = md5($str);
            // 发送
            return static::normalRequest($options, config('gameleveling.dd373.url')['orderInfo'], $order, 'orderInfo');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取截图
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getScreenShot(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'platformSign' => config('gameleveling.dd373.platform-sign'),
            ];
            $str = "platformOrderNo=" . $order->platform_trade_no . "&platformSign=" . config('gameleveling.dd373.platform-sign') . config('gameleveling.dd373.key');

            $options['Sign'] = md5($str);
            // 发送
            $result = static::normalRequest($options, config('gameleveling.dd373.url')['getScreenShot'], $order, 'getScreenShot');

            $images = [];
            if (isset($result['data']) && count($result['data']) > 0) {
                foreach ($result['data'] as $item) {
                    $images[] = [
                        'username' => $item['uploadUserName'],
                        'description' => $item['description'],
                        'url' => 'http://' . ltrim($item['imageUrl'], '//'),
                        'created_at' => $item['uploadTime'],
                    ];
                }
            }

            return $images;
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单截图', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取留言
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getMessage(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'platformSign' => config('gameleveling.dd373.platform-sign'),
            ];
            $str = "platformOrderNo=" . $order->platform_trade_no . "&platformSign=" . config('gameleveling.dd373.platform-sign') . config('gameleveling.dd373.key');

            $options['Sign'] = md5($str);
            // 发送
            $result = static::normalRequest($options, config('gameleveling.dd373.url')['getMessage'], $order, 'getMessage');

            $message = [];
            if (isset($result['data'])) {
                foreach ($result['data'] as $item) {
                    $message[] = [
                        'sender' => $item['senderType'] == 1 ? '您' : '打手',
                        'send_content' => $item['content'],
                        'send_time' => $item['sendTime'],
                    ];
                }
            }
            return $message;
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 回复留言
     * @param GameLevelingOrder $order
     * @param string $message
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function replyMessage(GameLevelingOrder $order, $message = '')
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'content' => $message ?? '',
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['replyMessage'], $order, 'replyMessage');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 修改账号密码
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function modifyGamePassword(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'gameAccount' => $order->game_account,
                'gamePassWord' => $order->game_password,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::normalRequest($options, config('gameleveling.dd373.url')['modifyGamePassword'], $order, 'modifyGamePassword');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '订单获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 发送截图
     * @param GameLevelingOrder $order
     * @param string $file
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function sendImage(GameLevelingOrder $order, $pic = '')
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'description' => '无',
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::formDataRequest(array_merge($options, ['fileBase' => $pic]), config('gameleveling.dd373.url')['sendImage'], $order, 'sendImage');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '发送截图', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取仲裁详情
     * @param GameLevelingOrder $order
     * @return array|string
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function complainInfo(GameLevelingOrder $order)
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'platformSign' => config('gameleveling.dd373.platform-sign'),
            ];

            $str = "platformOrderNo=" . $order->platform_trade_no . "&platformSign=" . config('gameleveling.dd373.platform-sign') . config('gameleveling.dd373.key');

            $options['Sign'] = md5($str);
            // 发送
            $result = static::formDataRequest($options, config('gameleveling.dd373.url')['complainInfo'], $order, 'complainInfo');

            if (!isset($result) || $result['code'] != 0) {
                return '接口无相关信息';
            }

            $arr = [];
            $arr['detail']['who'] = $result['data']['UserType'] == 1 ? '我方' : ($result['data']['UserType'] == 2 ? '接单平台' : ($result['data']['UserType'] == 3 ? '接单平台客服' : ($result['data']['UserType'] == 4 ? '对方' : '')));
            $arr['detail']['created_at'] = $result['data']['AppealTime'];
            $arr['detail']['content'] = $result['data']['Describe'];
            $arr['detail']['pic1'] = $result['data']['ImageList'][0] ?? '';
            $arr['detail']['pic2'] = $result['data']['ImageList'][1] ?? '';
            $arr['detail']['pic3'] = $result['data']['ImageList'][2] ?? '';

            $res = static::formDataRequest($options, config('gameleveling.dd373.url')['getArbitrationList'], $order, 'getComplainDetail');

            if (isset($res['data']) && $res['code'] == 0) {
                foreach ($res['data'] as $k => $detail) {
                    $arr['info'][$k]['who'] = $detail['senderType'] == 1 ? '我方' : ($detail['senderType'] == 2 ? '接单平台' : ($detail['senderType'] == 3 ? '接单平台客服' : ($detail['senderType'] == 4 ? '对方' : '')));
                    $arr['info'][$k]['created_at'] = $detail['sendTime'];
                    $arr['info'][$k]['content'] = $detail['content'];
                    $arr['info'][$k]['pic'] = $detail['imgUrl'][0] ?? '';
                }
            }
            return $arr;
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '获取仲裁详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 增加仲裁证据
     * @param GameLevelingOrder $order
     * @param string $pic
     * @param string $content
     * @throws GameLevelingOrderOperateException
     * * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addComplainDetail(GameLevelingOrder $order, $pic = '', $content = '')
    {
        try {
            $options = [
                'platformOrderNo' => $order->platform_trade_no,
                'content' => $content,
                'timestamp' => time(),
            ];
            // 对参数进行加工
            $options = static::handleOptions($options);
            // 发送
            static::formDataRequest(array_merge($options, ['fileBase' => base64ToBlob($pic)]), config('gameleveling.dd373.url')['addComplainDetail'], $order, 'addComplainDetail');
        } catch (Exception $e) {
            myLog('dd373-platform-error', ['方法' => '添加仲裁证据', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }
}
