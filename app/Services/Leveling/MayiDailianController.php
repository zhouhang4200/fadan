<?php

namespace App\Services\Leveling;

use Exception;
use App\Models\Game;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\OrderDetail;
use App\Exceptions\DailianException;

/**
 * 蚂蚁代练操作控制器
 */
class MayiDailianController extends LevelingAbstract implements LevelingInterface
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
        20116,
        20119,
        20058,
        20063,
        20122,
        20089,
    ];

    /**
     * form-data 格式提交数据
     * @param array $options
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    public static function formDataRequest($options = [], $method = 'POST')
    {

       try {
            $data = [];
            foreach ($options as $name => $value) {
                $data[$name]['name'] = $name;
                $data[$name]['contents'] = $value;
            }
            $options = $data;
            myLog('mayi-api-log', ['begin']);

            $client = new Client();
            $response = $client->request($method, config('leveling.mayidailian.url'), [
                'multipart' => $options,
            ]);
            $result =  $response->getBody()->getContents();

            myLog('mayi-api-log', [config('leveling.mayidailian.url'), $options, $result]);

            if (! isset($result) || empty($result)) {
                if ($options['method'] != 'dlOrderDel') {
                    throw new DailianException('请求返回数据不存在');
                }
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    if (isset($arrResult['status']) && ! in_array($arrResult['status'], self::$status)) {
                        $message = $arrResult['message'] ?? 'mayi接口返回错误';
                        if ($options['method'] != 'dlOrderDel') {
                            throw new DailianException($message);
                        }
                    }
                }
            }

            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('dd373-local-error', ['方法' => '请求', '原因' => $e->getMessage()]);

            throw new Exception($e->getMessage());
        }
    }

    /**
     * 普通提交
     * @param array $options
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    public static function normalRequest($options = [], $method = 'POST')
    {
        try {
            myLog('mayi-api-log', ['begin']);

            $client = new Client();
            $response = $client->request($method, config('leveling.mayidailian.url'), [
                'form_params' => $options,
            ]);

            $result = $response->getBody()->getContents();

            if (!isset($result) || empty($result)) {
                if ($options['method'] != 'dlOrderDel') {
                    throw new DailianException('请求返回数据不存在');
                }
            }

            myLog('my-api-log', [$options, $result]);

            if (isset($result) && !empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    if (isset($arrResult['status']) && ! in_array($arrResult['status'], self::$status)) {
                        // 判断是否失败
                        $message = $arrResult['message'] ?? 'mayi接口返回错误';
                        if ($options['method'] != 'dlOrderDel') {
                            throw new DailianException($message);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /***
     * 获取签名
     * @param $method
     * @param $time
     * @return string
     * @throws DailianException
     */
    public static function getSign($method, $time)
    {
        try {
            return md5($method . config('leveling.mayidailian.appid') . $time . config('leveling.mayidailian.Ver') . config('leveling.mayidailian.appsecret'));
        } catch (Exception $e) {
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 上架
     * @param $orderDatas
     * @throws DailianException
     */
    public static function onSale($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderGrounding',
                'order_id' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderGrounding', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 下架
     * @param $orderDatas
     * @throws DailianException
     */
    public static function offSale($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderUndercarriage',
                'order_id' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderUndercarriage', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '下架', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 接单
     * @param $orderDatas
     */
    public static function receive($orderDatas)
    {
    }

    /**
     * 申请撤销
     * @param $orderDatas
     * @throws DailianException
     */
    public static function applyRevoke($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            if (! isset($orderDatas['pay_amount']) || ! isset($orderDatas['deposit'])) {
                throw new DailianException('协商代练费或双金不存在');
            }
            $time = time();
            $options = [
                'method' => 'dlOrderTs',
                'nid' => $orderDatas['mayi_order_no'],
                'bzmoney' => $orderDatas['deposit'],
                'needsMoney' => $orderDatas['pay_amount'],
                'tsContent' => $orderDatas['revoke_message'] ?? '空',
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderTs', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '申请撤销', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 取消撤销
     * @param $orderDatas
     * @throws DailianException
     */
    public static function cancelRevoke($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderCancelTs',
                'nid' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderCancelTs', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '取消撤销', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }

    }

    /**
     * 同意撤销
     * @param $orderDatas
     * @throws DailianException
     */
    public static function agreeRevoke($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderAgreeTs',
                'nid' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderAgreeTs', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '同意撤销', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }

    }

    /**
     * 强制撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function forceRevoke($orderDatas)
    {
    }

    /**
     * 不同意撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function refuseRevoke($orderDatas)
    {
    }

    /**
     * 申请仲裁
     * @param $orderDatas
     * @throws DailianException
     */
    public static function applyArbitration($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method'    => 'dlOrdertsPub',
                'nid'       => $orderDatas['mayi_order_no'],
                'appid'     => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver'       => config('leveling.mayidailian.Ver'),
                'sign'      => static::getSign('dlOrdertsPub', $time),
                'bz'        => $orderDatas['complain_message'] ?? '空',
                'img1'      => $orderDatas['pic1'],
                'img2'      => $orderDatas['pic2'],
                'img3'      => $orderDatas['pic3'],
            ];

            static::formDataRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '申请仲裁', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 取消仲裁
     * @param $orderDatas
     * @throws DailianException
     */
    public static function cancelArbitration($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlCancelOrdertsPub',
                'order_id' => $orderDatas['mayi_order_no'],
                'bz' => '取消仲裁',
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlCancelOrdertsPub', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '取消仲裁', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 强制仲裁（客服仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function customArbitration($orderDatas)
    {
    }

    /**
     * 申请验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function applyComplete($orderDatas)
    {
    }

    /**
     * 取消验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelComplete($orderDatas)
    {
    }

    /**
     * 完成验收
     * @param $orderDatas
     * @throws DailianException
     */
    public static function complete($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderAcceptance',
                'nid' => $orderDatas['mayi_order_no'],
                'password' => config('leveling.mayidailian.password'),
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderAcceptance', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '完成验收', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 锁定
     * @param $orderDatas
     * @throws DailianException
     */
    public static function lock($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderLock',
                'nid' => $orderDatas['mayi_order_no'],
                'remark' => '订单状态异常',
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderLock', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '锁定', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 取消锁定
     * @param $orderDatas
     * @throws DailianException
     */
    public static function cancelLock($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderunLock',
                'nid' => $orderDatas['mayi_order_no'],
                'remark' => '订单状态正常',
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderunLock', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '取消锁定', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 异常
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function abnormal($orderDatas)
    {
    }

    /**
     * 取消异常
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelAbnormal($orderDatas)
    {
    }

    /**
     * 撤单（删除)
     * @param $orderDatas
     * @throws DailianException
     */
    public static function delete($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderDel',
                'nid' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderDel', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '撤单', '原因' => $e->getMessage()]);

            OrderDetail::where('order_no', $orderDatas['order_no'])
                ->where('field_name', 'mayi_order_no')
                ->update(['field_value' => '']);

            if ($e->getMessage() != '操作失败，订单状态已改变，请刷新页面重试') {
                throw new DailianException($e->getMessage());
            }
        }
    }


    /**
     * 修改订单(未接单时候的修改订单)
     * @param $orderDatas
     * @throws DailianException
     */
    public static function updateOrder($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $gameName = Game::find($orderDatas['game_id']);
            $options = [
                'method'        => 'dlOrderUpdate',
                'order_id'      => $orderDatas['mayi_order_no'],
                'gameName'      => $gameName ? $gameName->name : '',
                'zoneName'      => $orderDatas['region'],
                'serverName'    => $orderDatas['serve'],
                'pertype'       => $orderDatas['game_leveling_type'],
                'title'         => $orderDatas['game_leveling_title'],
                'paymoney'      => $orderDatas['amount'],
                'hours'         => bcadd(bcmul($orderDatas['game_leveling_day'], 24, 0), $orderDatas['game_leveling_hour'], 0),
                'use_gold'      => 0,
                'bzmoney_gold'  => $orderDatas['efficiency_deposit'],
                'bzmoney_exp'   => $orderDatas['security_deposit'],
                'gaccount'      => $orderDatas['account'],
                'gpassword'     => $orderDatas['password'],
                'jsm'           => $orderDatas['role'],
                'equipment'     => $orderDatas['game_leveling_instructions'],
                'detaildemand'  => $orderDatas['game_leveling_requirements'],
                'test_phone'    => $orderDatas['client_phone'],
                'contact_phone' => $orderDatas['client_phone'],
                'qq'            => $orderDatas['user_qq'],
                'password'      => config('leveling.mayidailian.password'),
                'onway'         => 1,
                'appid'         => config('leveling.mayidailian.appid'),
                'appsecret'     => config('leveling.mayidailian.appsecret'),
                'TimeStamp'     => $time,
                'Ver'           => config('leveling.mayidailian.Ver'),
                'sign'          => static::getSign('dlOrderUpdate', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '修改订单', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 订单加时
     * 增加后的总时间
     */
    public static function addTime($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrdereUpdateSpec',
                'order_id' => $orderDatas['mayi_order_no'],
                'append_hours' => $orderDatas['game_leveling_day'],
                'append_day' => $orderDatas['game_leveling_hour'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrdereUpdateSpec', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '加时', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 订单加款
     * 增加后的总款
     */
    public static function addMoney($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrdereUpdatePaymoney',
                'order_id' => $orderDatas['mayi_order_no'],
                'append_price' => $orderDatas['game_leveling_amount'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrdereUpdatePaymoney', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '加款', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /***
     * 获取订单详情
     * @param $orderDatas
     * @throws DailianException
     */
    public static function orderDetail($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderInfo',
                'nid' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderInfo', $time),
            ];

            return static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 获取订单截图
     * @param $orderDatas
     * @throws DailianException
     */
    public static function getScreenshot($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderImageList',
                'nid' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderImageList', $time),
            ];

            $result =  static::normalRequest($options);
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
            myLog('mayi-local-error', ['方法' => '订单截图', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 获取留言
     * @param $orderDatas
     * @throws DailianException
     */
    public static function getMessage($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderMessageList',
                'nid' => $orderDatas['mayi_order_no'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderMessageList', $time),
            ];

            $result  = static::normalRequest($options);

            $message = [];
            for ($i = count($result['data']) - 1; $i >= 0; $i--) {
                $message[] = [
                    'sender' => $result['data'][$i]['sender'] == 'ceshi009' ? '您': ($result['data'][$i]['sender'] == '系统' ? $result['data'][$i]['sender'] : '打手'),
                    'send_content' => $result['data'][$i]['content'],
                    'send_time' => $result['data'][$i]['add_time'],
                ];
            }
            return $message;
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '获取留言', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 回复留言
     * @param $orderDatas
     * @throws DailianException
     */
    public static function replyMessage($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrderMessageReply',
                'nid' => $orderDatas['mayi_order_no'],
                'lytext' => $orderDatas['message'] ?? '留言',
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrderMessageReply', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '回复留言', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 修改接单之后的游戏账号密码
     * @param $orderDatas
     * @throws DailianException
     */
    public static function updateAccountAndPassword($orderDatas)
    {
        try {
            if (!isset($orderDatas['mayi_order_no']) || empty($orderDatas['mayi_order_no'])) {
                throw new DailianException('蚂蚁订单号不存在');
            }

            $time = time();
            $options = [
                'method' => 'dlOrdereUpdatePass',
                'order_id' => $orderDatas['mayi_order_no'],
                'account' => $orderDatas['account'],
                'gpassword' => $orderDatas['password'],
                'appid' => config('leveling.mayidailian.appid'),
                'appsecret' => config('leveling.mayidailian.appsecret'),
                'TimeStamp' => $time,
                'Ver' => config('leveling.mayidailian.Ver'),
                'sign' => static::getSign('dlOrdereUpdatePass', $time),
            ];

            static::normalRequest($options);
        } catch (Exception $e) {
            myLog('mayi-local-error', ['方法' => '修改账号密码', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 置顶
     */
    public static function setTop()
    {

    }
}
