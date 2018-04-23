<?php

namespace App\Services\Leveling;

use Exception;
use Carbon\Carbon;

/**
 * 蚂蚁代练操作控制器
 */
class DD373Controller extends LevelingAbstract implements LevelingInterface
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
    public static function formDataRequest($options = [], $url = '', $method = 'POST')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: multipart/form-data']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        // 记录日志
        myLog('mayidailian', [
            'dd373单号' => $options['nid'] ?? ($options['order_no'] ?? ''),
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
    public static function normalRequest($options = [], $url= '', $method = 'POST')
    {
        $client = new Client;
        $response = $client->request($method, $url, [
            'form_params' => $options,
        ]);
        $result =  $response->getBody()->getContents();
        // 记录日志
        myLog('mayidailian', [
            'dd373单号' => $options['nid'] ?? ($options['order_no'] ?? ''),
            '方法名' => $options['method'],
            '签名' => $options['sign'],
            '时间' => Carbon::now()->toDateTimeString(),
            '结果' => $result ? json_decode($result) : '',
        ]);
    }

    /**
     * 对参数进行加工
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function handleOptions($datas)
    {
    	return [
			'jsonData'     => json_encode($datas),
			'platformSign' => config('leveling.dd373.appid'),
			'Sign'         => static::getSign($datas),
        ];
    }

    /**
     * 获取签名
     * @param  [type] $method [description]
     * @return [type]         [description]
     */
    public static function getSign($datas)
    {
    	$string = 'JsonData='.json_encode($datas)."&".config('leveling.dd373.appid');

    	myLog('dd373.sign', ['string' = $string, 'sign' => md5($string)]);

        return md5($string);
    }

    /**
     * 上架
     * @return [type] [description]
     */
    public static function onSale($orderDatas) {
    	try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['onSale']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 下架
     * @return [type] [description]
     */
    public static function offSale($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['offSale']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '下架', '原因' => $e->getMessage()]);
    	}
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
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'payAmount' => $orderDatas['amount'],
	        	'guarantyXLFee' => $orderDatas['deposit'],
	        	'guarantyAQFee' => ,
	        	'PayUserType' => ,
	        	'reason' => ! empty($orderDatas['revoke_message']) ?: '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['applyRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '申请撤销', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 取消撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelRevoke($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'State' => 2,
	        	'reason' => ! empty($orderDatas['revoke_message']) ?: '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['cancelRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '取消撤销', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 同意撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function agreeRevoke($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'State' => 1,
	        	'reason' => '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['agreeRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '同意撤销', '原因' => $e->getMessage()]);
    	}
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
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'reason' => ! empty($orderDatas['complain_message']) ?: '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['applyArbitration']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '申请仲裁', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 取消仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelArbitration($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'reason' => '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['cancelArbitration']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '取消仲裁', '原因' => $e->getMessage()]);
    	}
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
       	try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['complete']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单完成', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function lock($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['lock']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '锁定', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 取消锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelLock($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['cancelLock']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '解除锁定', '原因' => $e->getMessage()]);
    	}
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
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['delete']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '删除订单', '原因' => $e->getMessage()]);
    	}
    }



    /**
     * 修改订单(未接单时候的修改订单)
     * @return [type] [description]
     */
    public static function updateOrder($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['updateOrder']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '修改订单', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 订单加时
     * 增加后的总时间
     */
    public static function addTime($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'hours' => '',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['addTime']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单加时', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 订单加款
     * 增加后的总款
     */
    public static function addMoney($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'add_money' => $orderDatas['game_leveling_amount'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['addMoney']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单加款', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 获取订单详情
     * @return [type] [description]
     */
    public static function orderDetail($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['orderDetail']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 获取订单截图
     * @return [type] [description]
     */
    public static function getScreenshot($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'platformSign' => '',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['getScreenshot']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单截图', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 获取留言
     * @return [type] [description]
     */
    public static function getMessage($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['getMessage']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    	}
    }

    /**
     * 回复留言
     * @return [type] [description]
     */
    public static function replyMessage($orderDatas) {
        $time = time();
        $options = [
            'method'    => 'dlOrderMessageReply',
            'nid'       => $orderDatas['mayi_order_no'],
            'lytext'    => $orderDatas['message'] ?? '留言',
            'appid'     => config('leveling.mayidailian.appid'),
            'appsecret' => config('leveling.mayidailian.appsecret'),
            'timestamp' => $time,
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
            'timestamp' => $time,
            'Ver'       => config('leveling.mayidailian.Ver'),
            'sign'      => static::getSign('dlOrdereUpdatePass', $time),
        ];

        static::normalRequest($options);
    }
}
