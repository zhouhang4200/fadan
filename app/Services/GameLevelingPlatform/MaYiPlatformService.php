<?php

namespace App\Services\GameLevelingPlatform;

use Exception;
use GuzzleHttp\Client;
use App\Models\GameLevelingOrder;
use App\Models\GameLevelingPlatform;
use App\Exceptions\GameLevelingOrderOperateException;

class MaYiPlatformService implements GameLevelingPlatformServiceInterface
{
    /**
     * 成功状态
     * @var array
     */
    protected static $status = [
        1,
        20003,
        20040,
        20046,
        20051,
        20052,
        20071,
        20075,
        20078,
        20084,
        20090,
        20092,
        20097,
        20100,
        20104,
        20105,
        20112,
        20121,
        20124,
        20125,
        20116,
        20119,
        20058,
        20063,
        20122,
        20089,
    ];

    /**
     * 表单请求
     * @param $options
     * @param $functionName
     * @param $order
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function formDataRequest($options, $functionName, $order, $method = 'POST')
    {
        try {
            $data = [];
            foreach ($options as $name => $value) {
                $data[$name]['name'] = $name;
                $data[$name]['contents'] = $value;
            }

            $client = new Client();
            $response = $client->request($method, config('gameleveling.mayi.url'), [
                'multipart' => $data,
            ]);
            $result =  $response->getBody()->getContents();

            myLog('mayi-platform-form-request-result', [config('gameleveling.mayi.url'), $options, $result]);

            if (! isset($result) || empty($result)) {
                if ($options['method'] != 'dlOrderDel') {
                    throw new GameLevelingOrderOperateException('接口返回数据为空!');
                }
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    if (isset($arrResult['status']) && ! in_array($arrResult['status'], self::$status)) {
                        $errorMessage = $arrResult['message'];

                        // 往群里发消息
                        $client = new Client();
                        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                            'json' => [
                                'msgtype' => 'text',
                                'text' => [
                                    'content' => '订单（内部单号：'.$order->trade_no. '）调用【蚂蚁平台】【'.config('gameleveling.operate')[$functionName].'】接口失败:'.$errorMessage
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

                        if ($options['method'] != 'dlOrderDel') {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }

            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 普通请求
     * @param $options
     * @param $functionName
     * @param $order
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function normalRequest($options, $functionName, $order, $method = 'POST')
    {
        try {
            $client = new Client();
            $response = $client->request($method, config('gameleveling.mayi.url'), [
                'form_params' => $options,
            ]);

            $result = $response->getBody()->getContents();

            if (!isset($result) || empty($result)) {
                if ($options['method'] != 'dlOrderDel') {
                    throw new GameLevelingOrderOperateException('接口返回数据为空!');
                }
            }

            myLog('mayi-platform-request-result', [$options, $result]);

            if (isset($result) && !empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    if (isset($arrResult['status']) && ! in_array($arrResult['status'], self::$status)) {
                        // 判断是否失败
                        $errorMessage = $arrResult['message'] ?? 'mayi接口返回错误';

                        // 往群里发消息
                        $client = new Client();
                        $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                            'json' => [
                                'msgtype' => 'text',
                                'text' => [
                                    'content' => '订单（内部单号：'.$order->trade_no. '）调用【蚂蚁平台】【'.config('gameleveling.operate')[$functionName].'】接口失败:'.$errorMessage
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

                        if ($options['method'] != 'dlOrderDel') {
                            throw new GameLevelingOrderOperateException($errorMessage);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '请求', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 获取签名
     * @param $method
     * @param $time
     * @return string
     * @throws GameLevelingOrderOperateException
     */
    public static function getSign($method, $time)
    {
        try {
            return md5($method . config('gameleveling.mayi.appid') . $time . config('gameleveling.mayi.Ver') . config('gameleveling.mayi.appsecret'));
        } catch (Exception $e) {
            throw new GameLevelingOrderOperateException($e->getMessage());
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
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method' => 'dlOrderGrounding',
                'order_id' => $gameLevelingPlatform->platform_trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderGrounding', $time),
            ];

            static::normalRequest($options, 'onSale', $order);

            return true;
        } catch (GameLevelingOrderOperateException $e) {
            // 删除该平台订单
            static::delete($order);
            myLog('mayi-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        } catch (Exception $e) {
            // 删除该平台订单
            static::delete($order);
            myLog('mayi-platform-error', ['方法' => '上架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method' => 'dlOrderUndercarriage',
                'order_id' => $gameLevelingPlatform->platform_trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderUndercarriage', $time),
            ];

            static::normalRequest($options, 'offSale', $order);

            return true;
        } catch (GameLevelingOrderOperateException $e) {
            // 删除该平台订单
            static::delete($order);
            myLog('mayi-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        } catch (Exception $e) {
            // 删除该平台订单
            static::delete($order);
            myLog('mayi-platform-error', ['方法' => '下架', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }
    /**
     * 申请协商
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function applyConsult(GameLevelingOrder $order)
    {
        try {
            $gameLevelingOrderConsult = GameLevelingOrderConsult::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('status', 1)
                ->first();

            $time = time();
            $options = [
                'method' => 'dlOrderTs',
                'nid' => $order->platform_trade_no,
                'bzmoney' => bcadd($gameLevelingOrderConsult->security_deposit, $gameLevelingOrderConsult->efficiency_deposit),
                'needsMoney' => $gameLevelingOrderConsult->amount,
                'tsContent' => $gameLevelingOrderConsult->reason ?? '无',
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderTs', $time),
            ];

            static::normalRequest($options, 'applyConsult', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '申请协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
            $time = time();
            $options = [
                'method' => 'dlOrderCancelTs',
                'nid' => $order->platform_trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderCancelTs', $time),
            ];

            static::normalRequest($options, 'cancelConsult', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '取消协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
            $time = time();
            $options = [
                'method' => 'dlOrderAgreeTs',
                'nid' => $order->platform_trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderAgreeTs', $time),
            ];

            static::normalRequest($options, 'agreeConsult', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '同意协商', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }
    /**
     * 申请仲裁
     * @param GameLevelingOrder $order
     * @param $pic
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function applyComplain(GameLevelingOrder $order, $pic)
    {
        try {
            $gameLevelingOrderComplain = GameLevelingOrderComplain::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('status', 1)
                ->first();

            $time = time();
            $options = [
                'method'    => 'dlOrdertsPub',
                'nid'       => $order->platform_trade_no,
                'appid'     => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver'       => config('gameleveling.mayi.Ver'),
                'sign'      => static::getSign('dlOrdertsPub', $time),
                'bz'        => $gameLevelingOrderComplain->reason ?? '无',
                'img1'      => $pic['pic1'],
                'img2'      => $pic['pic2'],
                'img3'      => $pic['pic3'],
            ];

            static::formDataRequest($options, 'applyComplain', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '申请仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消申诉
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelComplain(GameLevelingOrder $order)
    {
        try {
            $time = time();
            $options = [
                'method' => 'dlCancelOrdertsPub',
                'order_id' => $order->platform_trade_no,
                'bz' => '取消仲裁',
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlCancelOrdertsPub', $time),
            ];

            static::normalRequest($options, 'cancelComplain', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '取消仲裁', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 完成
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function complete(GameLevelingOrder $order)
    {
        try {
            $time = time();
            $options = [
                'method' => 'dlOrderAcceptance',
                'nid' => $order->platform_trade_no,
                'password' => config('gameleveling.mayi.password'),
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderAcceptance', $time),
            ];

            static::normalRequest($options, 'complete', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '完成验收', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }


    /**
     * 锁定
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function lock(GameLevelingOrder $order)
    {
        try {
            $time = time();
            $options = [
                'method' => 'dlOrderLock',
                'nid' => $order->platform_trade_no,
                'remark' => '锁定订单',
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderLock', $time),
            ];

            static::normalRequest($options, 'lock', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '锁定', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 取消锁定
     * @param GameLevelingOrder $order
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function cancelLock(GameLevelingOrder $order)
    {
        try {
            $time = time();
            $options = [
                'method' => 'dlOrderunLock',
                'nid' => $order->platform_trade_no,
                'remark' => '订单状态正常',
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderunLock', $time),
            ];

            static::normalRequest($options, 'cancelLock', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '取消锁定', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

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
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method' => 'dlOrderDel',
                'nid' => $gameLevelingPlatform->platform_trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderDel', $time),
            ];

            static::normalRequest($options, 'delete', $order);

            return true;
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '撤单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);

            if ($e->getMessage() != '操作失败，订单状态已改变，请刷新页面重试') {
                throw new GameLevelingOrderOperateException($e->getMessage());
            }
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
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method'        => 'dlOrderUpdate',
                'order_id'      => $gameLevelingPlatform->platform_trade_no,
                'gameName'      => $order->gameLevelingOrderDetail->game_name,
                'zoneName'      => $order->gameLevelingOrderDetail->game_region_name,
                'serverName'    => $order->gameLevelingOrderDetail->game_server_name,
                'pertype'       => $order->gameLevelingOrderDetail->game_leveling_type_name,
                'title'         => $order->title,
                'paymoney'      => $order->amount,
                'hours'         => bcadd(bcmul($order->day, 24, 0), $order->hour, 0),
                'use_gold'      => 0,
                'bzmoney_gold'  => $order->efficiency_deposit,
                'bzmoney_exp'   => $order->security_deposit,
                'gaccount'      => $order->game_account,
                'gpassword'     => $order->game_password,
                'jsm'           => $order->game_role,
                'equipment'     => $order->gameLevelingOrderDetail->explain,
                'detaildemand'  => $order->gameLevelingOrderDetail->requirement,
                'test_phone'    => $order->gameLevelingOrderDetail->user_phone,
                'contact_phone' => $order->gameLevelingOrderDetail->user_phone,
                'qq'            => $order->gameLevelingOrderDetail->user_qq,
                'password'      => config('gameleveling.mayi.password'),
                'onway'         => 1,
                'appid'         => config('gameleveling.mayi.appid'),
                'appsecret'     => config('gameleveling.mayi.appsecret'),
                'TimeStamp'     => $time,
                'Ver'           => config('gameleveling.mayi.Ver'),
                'sign'          => static::getSign('dlOrderUpdate', $time),
            ];

            static::normalRequest($options, 'modifyOrder', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '修改订单', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method' => 'dlOrdereUpdateSpec',
                'order_id' => $gameLevelingPlatform->platform_trade_no,
                'append_hours' => $order->day,
                'append_day' => $order->hour,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrdereUpdateSpec', $time),
            ];

            static::normalRequest($options, 'addTime', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '加时', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method' => 'dlOrdereUpdatePaymoney',
                'order_id' => $gameLevelingPlatform->platform_trade_no,
                'append_price' => $order->amount,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrdereUpdatePaymoney', $time),
            ];

            static::normalRequest($options, 'addAmount', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '加款', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 订单详情
     * @param GameLevelingOrder $order
     * @return bool|mixed
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function orderInfo(GameLevelingOrder $order)
    {
        try {
            $gameLevelingPlatform = GameLevelingPlatform::where('game_leveling_order_trade_no', $order->trade_no)
                ->where('platform_id', 3)
                ->first();

            if (! $gameLevelingPlatform) {
                return true;
            }

            $time = time();
            $options = [
                'method' => 'dlOrderInfo',
                'nid' => $gameLevelingPlatform->platform_trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderInfo', $time),
            ];

            return static::normalRequest($options, 'orderInfo', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '订单详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
            $time = time();
            $options = [
                'method' => 'dlOrderImageList',
                'nid' => $order->trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderImageList', $time),
            ];

            $result =  static::normalRequest($options, 'getScreenShot', $order);
            $images = [];
            foreach ($result['data'] as $item) {
                if (count($item['img_url']) > 1) {
                    foreach ($item['img_url'] as $i) {
                        $images[] = [
                            'username' => '',
                            'description' => $item['explain'],
                            'url' => $i,
                            'created_at' => $item['add_time'],
                        ];
                    }
                } else {
                    $images[] = [
                        'username' => '',
                        'description' => $item['explain'],
                        'url' => $item['img_url'][0],
                        'created_at' => $item['add_time'],
                    ];
                }
            }
            return $images;
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '订单截图', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取留言
     * @param GameLevelingOrder $order
     * @return array
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getMessage(GameLevelingOrder $order)
    {
        try {
            $time = time();
            $options = [
                'method' => 'dlOrderMessageList',
                'nid' => $order->trade_no,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderMessageList', $time),
            ];

            $result  = static::normalRequest($options, 'getMessage', $order);

            $message = [];
            if (isset($result['data']) && count($result['data']) > 0) {
                for ($i = count($result['data']) - 1; $i >= 0; $i--) {
                    $message[] = [
                        'sender' => $result['data'][$i]['sender'] == 'ceshi009' ? '您': ($result['data'][$i]['sender'] == '系统' ? $result['data'][$i]['sender'] : '打手'),
                        'send_content' => $result['data'][$i]['content'],
                        'send_time' => $result['data'][$i]['add_time'],
                    ];
                }
            }
            return $message;
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '获取留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 回复留言
     * @param GameLevelingOrder $order
     * @param string $message
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function replyMessage(GameLevelingOrder $order, $message = '无')
    {
        try {
            $time = time();
            $options = [
                'method' => 'dlOrderMessageReply',
                'nid' => $order->trade_no,
                'lytext' => $message,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrderMessageReply', $time),
            ];

            static::normalRequest($options, 'replyMessage', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '回复留言', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
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
            $time = time();
            $options = [
                'method' => 'dlOrdereUpdatePass',
                'order_id' => $order->platform_trade_no,
                'account' => $order->game_account,
                'gpassword' => $order->game_password,
                'appid' => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('gameleveling.mayi.Ver'),
                'sign' => static::getSign('dlOrdereUpdatePass', $time),
            ];

            static::normalRequest($options, 'modifyGamePassword', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '修改账号密码', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 获取仲裁详情
     * @param GameLevelingOrder $order
     * @return array|string
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function complainInfo(GameLevelingOrder $order)
    {
        try {
            $time = time();
            $options = [
                'method'    => 'dlOrdertsPubInfo',
                'nid'       => $order->platform_trade_no,
                'appid'     => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver'       => config('gameleveling.mayi.Ver'),
                'sign'      => static::getSign('dlOrdertsPubInfo', $time),
            ];

            $details = static::normalRequest($options, 'complainInfo', $order);

            if (! isset($details) || ! isset($details['status']) || $details['status'] != 1 || ! isset($details['data']) || ! is_array($details)) {
                return '暂无相关信息';
            }
            $arr = [];
            $arr['detail']['who'] = $details['data']['userid'] == config('gameleveling.mayi.uid') ? '我方' : '对方';
            $arr['detail']['created_at'] = $details['data']['add_time'];
            $arr['detail']['content'] = $details['data']['remark'];
            $arr['detail']['arbitration_id'] = $details['data']['ts_id'];
            $arr['detail']['pic1'] = $details['data']['pic1'];
            $arr['detail']['pic2'] = $details['data']['pic2'];
            $arr['detail']['pic3'] = $details['data']['pic3'];

            if (isset($details['data']['supplement'])) {
                foreach($details['data']['supplement'] as $k => $detail) {
                    $arr['info'][$k]['who'] = $detail['userid'] == config('gameleveling.mayi.uid') ? '我方' : '对方';
                    $arr['info'][$k]['created_at'] = $detail['add_time'];
                    $arr['info'][$k]['content'] = $detail['remark'];
                    $arr['info'][$k]['pic'] = $detail['img_url'];
                }
            }
            return $arr;
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '获取仲裁详情', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    /**
     * 添加仲裁证据
     * @param GameLevelingOrder $order
     * @param $pic
     * @param $content
     * @throws GameLevelingOrderOperateException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addComplainDetail(GameLevelingOrder $order, $pic, $content)
    {
        try {
            $time = time();
            $options = [
                'method'    => 'dlOrdertsPubAddInfo',
                'nid'       => $order->platform_trade_no,
                'appid'     => config('gameleveling.mayi.appid'),
                'appsecret' => config('gameleveling.mayi.appsecret'),
                'TimeStamp' => $time,
                'Ver'       => config('gameleveling.mayi.Ver'),
                'sign'      => static::getSign('dlOrdertsPubAddInfo', $time),
                'pic1'      => !empty($pic) ? base64ToBlob($pic) : '',
                'bz'        => $content,
            ];

            static::formDataRequest($options, 'addComplainDetail', $order);
        } catch (Exception $e) {
            myLog('mayi-platform-error', ['方法' => '添加仲裁证据', '原因' => $e->getMessage(), $e->getFile(), $e->getLine()]);
            throw new GameLevelingOrderOperateException($e->getMessage());
        }
    }

    public static function sendImage(GameLevelingOrder $order, $pic){}
    public static function forceDelete(GameLevelingOrder $order){}
    public static function rejectConsult(GameLevelingOrder $order) {}
}
