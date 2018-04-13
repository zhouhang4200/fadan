<?php

namespace App\Services\Leveling;

use Carbon\Carbon;

/**
 * 蚂蚁代练操作控制器
 */
class MayiDailianController extends LevelingAbstract implements LevelingInterface
{
	/**
	 * 调用接口时间
	 * @var [type]
	 */
	protected $time;

	public function __construct()
	{
		$this->time = time();
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
        	'方法名' => $options['method'],
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
        $client = new Client;
        $response = $client->request($method, config('leveling.mayidailian.url'), [
            'query' => $options,
        ]);
        $res =  $response->getBody()->getContents();
        // 记录日志
        myLog('mayidailian', [
        	'方法名' => $options['method'],
        	'时间' => Carbon::now()->toDateTimeString(),
        	'结果' => $result ? json_decode($result) : '',
        ]);
    }

    /**
     * 获取签名
     * @param  [type] $method [description]
     * @return [type]         [description]
     */
    public function getSign($method)
    {
    	return md5($method.config('leveling.mayidailian.appid').$this->time.config('leveling.mayidailian.appsecret').config('leveling.mayidailian.Ver'));
    }

    /**
	 * 上架
	 * @return [type] [description]
	 */
    public function onSale($orderDatas) {
    	$options = [
			'method'        => '',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign(''),
		];

		$this->normalRequest($options);
    }

    /**
     * 下架
     * @return [type] [description]
     */
	public function offSale($orderDatas) {
		$options = [
			'method'        => '',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign(''),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 接单
	 * @return [type] [description]
	 */
	public function receive($orderDatas) {}

	/**
	 * 申请撤销
	 * @return [type] [description]
	 */
	public function applyRevoke($orderDatas) {
		$options = [
			'method'        => 'dlOrderTs',
			'nid'           => $orderDatas['mayi_order_no'],
			'dlBzmoneyGold' => $orderDatas['deposit'],
			'needsMoney'    => $orderDatas['pay_amount'],
			'tsContent'     => $orderDatas['revoke_message'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderTs'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 取消撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function cancelRevoke($orderDatas) {
		$options = [
			'method'        => 'dlOrderCancelTs',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderCancelTs'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 同意撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function agreeRevoke($orderDatas) {
		$options = [
			'method'        => 'dlOrderAgreeTs',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderAgreeTs'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 强制撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function forceRevoke($orderDatas) {} 

	/**
	 * 不同意撤销
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function refuseRevoke($orderDatas) {} 

	/**
	 * 申请仲裁
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function applyArbitration($orderDatas) {
		$options = [
			'method'        => 'dlOrdertsPub',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrdertsPub'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 取消仲裁
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function cancelArbitration($orderDatas) {} 

	/**
	 * 强制仲裁（客服仲裁
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function customArbitration($orderDatas) {} 

	/**
	 * 申请验收
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function applyComplete($orderDatas) {} 

	/**
	 * 取消验收
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function cancelComplete($orderDatas) {} 

	/**
	 * 完成验收
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function complete($orderDatas) {
		$options = [
			'method'        => 'dlOrderAcceptance',
			'nid'           => $orderDatas['mayi_order_no'],
			'password'		=> config('leveling.mayidailian.password'),
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderAcceptance'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 锁定
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function lock($orderDatas) {
		$options = [
			'method'        => 'dlOrderLock',
			'nid'           => $orderDatas['mayi_order_no'],
			'remark'	    => '订单状态异常',
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderLock'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 取消锁定
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function cancelLock($orderDatas) {
		$options = [
			'method'        => 'dlOrderunLock',
			'nid'           => $orderDatas['mayi_order_no'],
			'remark'	    => '订单状态正常',
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderunLock'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 异常
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function abnormal($orderDatas) {} 

	/**
	 * 取消异常
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function cancelAbnormal($orderDatas) {} 

	/**
	 * 撤单（删除)
	 * @param  [type] $orderDatas [description]
	 * @return [type]             [description]
	 */
	public function delete($orderDatas) {
		$options = [
			'method'        => 'dlOrderDel',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderDel'),
		];

		$this->normalRequest($options);
	} 






	/**
	 * 修改订单(未接单时候的修改订单)
	 * @return [type] [description]
	 */
	public function updateOrder($orderDatas) {
		$options = [
			'method'        => 'dlOrderUpdate',
			'order_id'      => $orderDatas['mayi_order_no'],
			'gameId'        => $orderDatas['game_id'],
			'zoneId'        => $orderDatas['region'],
			'serverId'      => $orderDatas['serve'],
			'pertype'       => $orderDatas['game_leveling_type'],
			'title'         => $orderDatas['game_leveling_title'],
			'paymoney'      => $orderDatas['amount'],
			'hours'         => bcadd(bcmul($orderDatas['game_leveling_day'], 24, 0), $orderDatas['game_leveling_hour'], 0),
			'use_gold'      => 0,
			'bzmoney_gold'  => $orderDatas['efficiency_deposit'],
			'bzmoney_exp'   => $orderDatas['bzmoney_exp'],
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
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderUpdate'),
		];

		$this->normalRequest($options);
	}

	/**
	 * 订单加时
	 * 增加后的总时间
	 */
	public function addTime($orderDatas) {
		$options = [
			'method'       => 'dlOrdereUpdateSpec',
			'order_id'     => $orderDatas['mayi_order_no'],
			'append_hours' => $orderDatas['game_leveling_day'],
			'append_hours' => $orderDatas['game_leveling_hour'],
			'append_price' => 0,
			'zfpwd'        => config('leveling.mayidailian.password'),
			'appid'        => config('leveling.mayidailian.appid'),
			'appsecret'    => config('leveling.mayidailian.appsecret'),
			'TimeStamp'    => $this->time,
			'Ver'          => config('leveling.mayidailian.Ver'),
			'sign'         => $this->getSign('dlOrdereUpdateSpec'),
		];

		$this->normalRequest($options);
	}

	/**
	 * 订单加款
	 * 增加后的总款
	 */
	public function addMoney($orderDatas) {
		$options = [
			'method'       => 'dlOrdereUpdateSpec',
			'order_id'     => $orderDatas['mayi_order_no'],
			'append_hours' => 0,
			'append_price' => $orderDatas['game_leveling_amount'],
			'zfpwd'        => config('leveling.mayidailian.password'),
			'appid'        => config('leveling.mayidailian.appid'),
			'appsecret'    => config('leveling.mayidailian.appsecret'),
			'TimeStamp'    => $this->time,
			'Ver'          => config('leveling.mayidailian.Ver'),
			'sign'         => $this->getSign('dlOrdereUpdateSpec'),
		];

		$this->normalRequest($options);
	}

	/**
	 * 获取订单详情
	 * @return [type] [description]
	 */
	public function orderDetail($orderDatas) {
		$options = [
			'method'        => 'dlOrderInfo',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderInfo'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 获取订单截图
	 * @return [type] [description]
	 */
	public function getScreenshot($orderDatas) {
		$options = [
			'method'        => 'dlOrderImageList',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderImageList'),
		];

		$this->normalRequest($options);
	} 

	/**
	 * 获取留言
	 * @return [type] [description]
	 */
	public function getMessage($orderDatas) {
		$options = [
			'method'        => 'dlOrderMessageList',
			'nid'           => $orderDatas['mayi_order_no'],
			'appid'         => config('leveling.mayidailian.appid'),
			'appsecret'     => config('leveling.mayidailian.appsecret'),
			'TimeStamp'     => $this->time,
			'Ver'           => config('leveling.mayidailian.Ver'),
			'sign'          => $this->getSign('dlOrderMessageList'),
		];

		$this->normalRequest($options);
	}

	/**
	 * 回复留言
	 * @return [type] [description]
	 */
	public function replyMessage($orderDatas, $message) {
		$options = [
			'method'    => 'dlOrderMessageReply',
			'nid'       => $orderDatas['mayi_order_no'],
			'lytext'    => $message,
			'appid'     => config('leveling.mayidailian.appid'),
			'appsecret' => config('leveling.mayidailian.appsecret'),
			'TimeStamp' => $this->time,
			'Ver'       => config('leveling.mayidailian.Ver'),
			'sign'      => $this->getSign('dlOrderMessageReply'),
		];

		$this->normalRequest($options);
	}

	/**
	 * 修改接单之后的游戏账号密码
	 * @return [type] [description]
	 */
	public function updateAccountAndPassword($orderDatas) {
		$options = [
			'method'    => 'dlOrderMessageList',
			'nid'       => $orderDatas['mayi_order_no'],
			'appid'     => config('leveling.mayidailian.appid'),
			'appsecret' => config('leveling.mayidailian.appsecret'),
			'TimeStamp' => $this->time,
			'Ver'       => config('leveling.mayidailian.Ver'),
			'sign'      => $this->getSign('dlOrderMessageList'),
			'account'   => $orderDatas['account'],
			'password'  => $orderDatas['password'],
		];

		$this->normalRequest($options);
	}
}
