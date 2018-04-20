<?php

namespace App\Services\Leveling;

use App\Models\Game;
use Carbon\Carbon;
use GuzzleHttp\Client;

/**
 * 蚂蚁代练操作控制器
 */
class MayiDailianController extends LevelingAbstract implements LevelingInterface
{
    /**
     * 调用接口时间
     * @var [type]
     */
    // protected static $time;

    public function __construct()
    {
        // $time = time();
    }

    /**
     * form-data 格式提交数据
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function formDataRequest($options = [], $method = 'POST')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: multipart/form-data']);
        curl_setopt($curl, CURLOPT_URL, config('leveling.mayidailian.url'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        // 记录日志
        myLog('mayidailian', [
            '蚂蚁单号' => $options['nid'] ?? ($options['order_no'] ?? ''),
            '方法名' => $options['method'],
            '签名' => $options['sign'],
            '时间' => Carbon::now()->toDateTimeString(),
            '结果' => $result ? json_decode($result) : '',
        ]);
    }

    /**
     * 普通提交
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function normalRequest($options = [], $method = 'POST')
    {
        // dd($options);
        $client = new Client;
        $response = $client->request($method, config('leveling.mayidailian.url'), [
            'form_params' => $options,
        ]);
        $result =  $response->getBody()->getContents();
        // 记录日志
        myLog('mayidailian', [
            '蚂蚁单号' => $options['nid'] ?? ($options['order_no'] ?? ''),
            '方法名' => $options['method'],
            '签名' => $options['sign'],
            '时间' => Carbon::now()->toDateTimeString(),
            '结果' => $result ? json_decode($result) : '',
        ]);
    }

    /**
     * 获取签名
     * @param  [type] $method [description]
     * @return [type]         [description]
     */
    public static function getSign($method, $time)
    {
        return md5($method.config('leveling.mayidailian.appid').$time.config('leveling.mayidailian.Ver').config('leveling.mayidailian.appsecret'));
    }

    /**
     * 上架
     * @return [type] [description]
     */
    public static function onSale($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderGrounding',
            'order_id'      => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderGrounding', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 下架
     * @return [type] [description]
     */
    public static function offSale($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderUndercarriage',
            'order_id'      => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderUndercarriage', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 接单
     * @return [type] [description]
     */
    public static function receive($orderDatas) {}

    /**
     * 申请撤销
     * @return [type] [description]
     */
    public static function applyRevoke($orderDatas) {
        $time = time();
        $options = [
            'method'     => 'dlOrderTs',
            'nid'        => $orderDatas['mayi_order_no'],
            'bzmoney'    => $orderDatas['deposit'],
            'needsMoney' => $orderDatas['amount'],
            'tsContent'  => ! empty($orderDatas['revoke_message']) ?: '空',
            'appid'      => config('leveling.mayidailian.appid'),
            'appsecret'  => config('leveling.mayidailian.appsecret'),
            'TimeStamp'  => $time,
            'Ver'        => config('leveling.mayidailian.Ver'),
            'sign'       => static::getSign('dlOrderTs', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 取消撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelRevoke($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderCancelTs',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderCancelTs', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 同意撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function agreeRevoke($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderAgreeTs',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderAgreeTs', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 强制撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function forceRevoke($orderDatas) {}

    /**
     * 不同意撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function refuseRevoke($orderDatas) {}

    /**
     * 申请仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function applyArbitration($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrdertsPub',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrdertsPub', $time),
            'bz'		=> ! empty($orderDatas['complain_message']) ?: '空',
        ];

        static::normalRequest($options);
    }

    /**
     * 取消仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelArbitration($orderDatas) {
        $time = time();
        $options = [
            'method'    => 'dlCancelOrdertsPub',
            'order_id'  => $orderDatas['mayi_order_no'],
            'bz'        => '取消仲裁',
            'appid'     => config('leveling.mayidailian.appid'),
            'appsecret' => config('leveling.mayidailian.appsecret'),
            'TimeStamp' => $time,
            'Ver'       => config('leveling.mayidailian.Ver'),
            'sign'      => static::getSign('dlCancelOrdertsPub', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 强制仲裁（客服仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function customArbitration($orderDatas) {}

    /**
     * 申请验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function applyComplete($orderDatas) {}

    /**
     * 取消验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelComplete($orderDatas) {}

    /**
     * 完成验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function complete($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderAcceptance',
            'nid'           => $orderDatas['mayi_order_no'],
            'password'		=> config('leveling.mayidailian.password'),
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderAcceptance', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function lock($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderLock',
            'nid'           => $orderDatas['mayi_order_no'],
            'remark'	    => '订单状态异常',
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderLock', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 取消锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelLock($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderunLock',
            'nid'           => $orderDatas['mayi_order_no'],
            'remark'	    => '订单状态正常',
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderunLock', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 异常
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function abnormal($orderDatas) {}

    /**
     * 取消异常
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelAbnormal($orderDatas) {}

    /**
     * 撤单（删除)
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function delete($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderDel',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderDel', $time),
        ];

        static::normalRequest($options);
    }






    /**
     * 修改订单(未接单时候的修改订单)
     * @return [type] [description]
     */
    public static function updateOrder($orderDatas) {
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
            'test_phone'    => $orderDatas['user_phone'],
            'contact_phone' => $orderDatas['user_phone'],
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
    }

    /**
     * 订单加时
     * 增加后的总时间
     */
    public static function addTime($orderDatas) {
        $time = time();
        $options = [
            'method'       => 'dlOrdereUpdateSpec',
            'order_id'     => $orderDatas['mayi_order_no'],
            'append_hours' => $orderDatas['game_leveling_day'],
            'append_day'   => $orderDatas['game_leveling_hour'],
            'appid'        => config('leveling.mayidailian.appid'),
            'appsecret'    => config('leveling.mayidailian.appsecret'),
            'TimeStamp'    => $time,
            'Ver'          => config('leveling.mayidailian.Ver'),
            'sign'         => static::getSign('dlOrdereUpdateSpec', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 订单加款
     * 增加后的总款
     */
    public static function addMoney($orderDatas) {
        $time = time();
        $options = [
            'method'       => 'dlOrdereUpdatePaymoney',
            'order_id'     => $orderDatas['mayi_order_no'],
            'append_price' => $orderDatas['game_leveling_amount'],
            'appid'        => config('leveling.mayidailian.appid'),
            'appsecret'    => config('leveling.mayidailian.appsecret'),
            'TimeStamp'    => $time,
            'Ver'          => config('leveling.mayidailian.Ver'),
            'sign'         => static::getSign('dlOrdereUpdatePaymoney', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 获取订单详情
     * @return [type] [description]
     */
    public static function orderDetail($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderInfo',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderInfo', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 获取订单截图
     * @return [type] [description]
     */
    public static function getScreenshot($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderImageList',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderImageList', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 获取留言
     * @return [type] [description]
     */
    public static function getMessage($orderDatas) {
        $time = time();
        $options = [
            'method'        => 'dlOrderMessageList',
            'nid'           => $orderDatas['mayi_order_no'],
            'appid'         => config('leveling.mayidailian.appid'),
            'appsecret'     => config('leveling.mayidailian.appsecret'),
            'TimeStamp'     => $time,
            'Ver'           => config('leveling.mayidailian.Ver'),
            'sign'          => static::getSign('dlOrderMessageList', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 回复留言
     * @return [type] [description]
     */
    public static function replyMessage($orderDatas, $message) {
        $time = time();
        $options = [
            'method'    => 'dlOrderMessageReply',
            'nid'       => $orderDatas['mayi_order_no'],
            'lytext'    => $message,
            'appid'     => config('leveling.mayidailian.appid'),
            'appsecret' => config('leveling.mayidailian.appsecret'),
            'TimeStamp' => $time,
            'Ver'       => config('leveling.mayidailian.Ver'),
            'sign'      => static::getSign('dlOrderMessageReply', $time),
        ];

        static::normalRequest($options);
    }

    /**
     * 修改接单之后的游戏账号密码
     * @return [type] [description]
     */
    public static function updateAccountAndPassword($orderDatas) {
        $time = time();
        $options = [
            'method'    => 'dlOrdereUpdatePass',
            'order_id'  => $orderDatas['mayi_order_no'],
            'account'   => $orderDatas['account'],
            'gpassword' => $orderDatas['password'],
            'appid'     => config('leveling.mayidailian.appid'),
            'appsecret' => config('leveling.mayidailian.appsecret'),
            'TimeStamp' => $time,
            'Ver'       => config('leveling.mayidailian.Ver'),
            'sign'      => static::getSign('dlOrdereUpdatePass', $time),
        ];

        static::normalRequest($options);
    }
}
