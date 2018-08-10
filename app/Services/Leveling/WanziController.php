<?php

namespace App\Services\Leveling;

use Redis;
use Exception;
use Carbon\Carbon;
use App\Models\Game;
use GuzzleHttp\Client;
use App\Models\OrderDetail;
use App\Exceptions\DailianException;

class WanziController extends LevelingAbstract implements LevelingInterface
{
    	/**
     * 调用接口时间
     * @var [type]
     */
    // protected static $time;

    public function __construct()
    {
 
    }

    /**
     * form-data 格式提交数据
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function formDataRequest($options = [], $url = '', $functionName = '', $datas = [], $method = 'POST')
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

            myLog('wanzi-request-log', [
                '地址' => $url,
                '信息' => $options,
                '结果' => $result,
            ]);
            
	        if (! isset($result) || empty($result)) {
                throw new DailianException('请求返回数据不存在');
            }

	        if (isset($result) && ! empty($result)) {
	        	$arrResult = json_decode($result, true);

	        	if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
	        		// 失败
	        		if (isset($arrResult['code']) && $arrResult['code'] != 1) {
        				$message = $arrResult['message'] ?? '丸子接口返回错误';

                        // 记录报警
                        $datas['notice_reason'] = $message;
                        $datas['operate'] = config('leveling.operate')[$functionName] ?? '无';
                        $datas['notice_created_at'] = Carbon::now()->toDateTimeString();
                        $name = "order:order-api-notices";
                        $key = $datas['order_no'].'-5-'.$functionName;
                        $value = json_encode(['third' => 5, 'reason' => $message, 'functionName' => $functionName, 'datas' => $datas]);
                        Redis::hSet($name, $key, $value);

                         // 往群里发消息
                        // $client = new Client();
                        // $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                        //     'json' => [
                        //         'msgtype' => 'text',
                        //         'text' => [
                        //             'content' => '订单（内部单号：'.$datas['order_no']. '）调用【'.config('order.third')[5].'】【'.$datas['operate'].'】接口失败:'.$datas['notice_reason']
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
                            throw new DailianException($message);
                        }
	        		}
	        	}
    		}
    		return json_decode($result, true);
        } catch (Exception $e) {
        	myLog('wanzi-local-error', ['方法' => '请求', '原因' => $e->getMessage()]);

        	throw new Exception($e->getMessage());
        }
    }

    /**
     * 普通提交
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function normalRequest($options = [], $url= '', $functionName = '', $datas = [], $method = 'POST')
    {
    	try {
            $client = new Client();
            $response = $client->request($method, $url, [
                'form_params' => $options,
            ]);
            $result =  $response->getBody()->getContents();

            myLog('wanzi-request-log', [
                '地址' => $url,
                '信息' => $options,
                '结果' => $result,
            ]);

            if (! isset($result) || empty($result)) {
                throw new DailianException('请求返回数据不存在');
            }

            if (isset($result) && ! empty($result)) {
                $arrResult = json_decode($result, true);

                if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
                    // 失败
                    if (isset($arrResult['code']) && $arrResult['code'] != 1) {
                        $message = $arrResult['message'] ?? '丸子接口返回错误';

                        // 记录报警
                        $datas['notice_reason'] = $message;
                        $datas['operate'] = config('leveling.operate')[$functionName] ?? '无';
                        $datas['notice_created_at'] = Carbon::now()->toDateTimeString();
                        $name = "order:order-api-notices";
                        $key = $datas['order_no'].'-5-'.$functionName;
                        $value = json_encode(['third' => 5, 'reason' => $message, 'functionName' => $functionName, 'datas' => $datas]);
                        Redis::hSet($name, $key, $value);

                         // 往群里发消息
                        // $client = new Client();
                        // $client->request('POST', 'https://oapi.dingtalk.com/robot/send?access_token=54967c90b771a4b585a26b195a71500a2e974fb9b4c9f955355fe4111324eab8', [
                        //     'json' => [
                        //         'msgtype' => 'text',
                        //         'text' => [
                        //             'content' => '订单（内部单号：'.$datas['order_no']. '）调用【'.config('order.third')[5].'】【'.$datas['operate'].'】接口失败:'.$datas['notice_reason']
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
                            throw new DailianException($message);
                        }
                    }
                }
            }
            return json_decode($result, true);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '请求', '原因' => $e->getMessage()]);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 对参数进行加工
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public static function handleOptions($datas)
    {
    	
    }

    /**
     * 获取签名
     * @param  [type] $method [description]
     * @return [type]         [description]
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
     * @return [type] [description]
     */
    public static function onSale($orderDatas) {
    	try {
	       	$options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['onSale'], 'onSale', $orderDatas);

            return true;
    	} catch (DailianException $e) {
            // 删除该平台订单
            static::delete($orderDatas);
    		throw new DailianException($e->getMessage());
    	}  catch (Exception $e) {
            // 删除该平台订单
            static::delete($orderDatas);
            myLog('wanzi-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 下架
     * @return [type] [description]
     */
    public static function offSale($orderDatas) {
        try {
	       	$options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['offSale'], 'offSale', $orderDatas);

            return true;
    	} catch (DailianException $e) {
            // 删除该平台订单
            static::delete($orderDatas);
            throw new DailianException($e->getMessage());
        }  catch (Exception $e) {
            // 删除该平台订单
            static::delete($orderDatas);
            myLog('wanzi-local-error', ['方法' => '下架', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
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
            $options = [
                'app_id'         => config('leveling.wanzi.app_id'),
                'order_no'       => $orderDatas['third_order_no'],
                'amount'         => $orderDatas['pay_amount'],
                'double_deposit' => $orderDatas['deposit'],
                'reason'         => $orderDatas['revoke_message'] ?? '空',
                'timestamp'      => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['applyRevoke'], 'applyRevoke', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '申请撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 取消撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelRevoke($orderDatas) {
        try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['third_order_no'],
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['cancelRevoke'], 'cancelRevoke', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '取消撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 同意撤销
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function agreeRevoke($orderDatas) {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['third_order_no'],
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	$result = static::normalRequest($options, config('leveling.wanzi.url')['agreeRevoke'], 'agreeRevoke', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '同意撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
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
    public static function refuseRevoke($orderDatas) {
    	try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['third_order_no'],
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['refuseRevoke'], 'refuseRevoke', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '不同意撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 申请仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function applyArbitration($orderDatas) {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['third_order_no'],
                'timestamp' => time(),
                'reason' => $orderDatas['complain_message'],
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
            $options['pic1'] = $orderDatas['pic1'] ?? null;
            $options['pic2'] = $orderDatas['pic2'] ?? null;
            $options['pic3'] = $orderDatas['pic3'] ?? null;

	       	// 发送
	       	static::formDataRequest($options, config('leveling.wanzi.url')['applyArbitration'], 'applyArbitration', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '申请仲裁', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 取消仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelArbitration($orderDatas) {
        try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['third_order_no'],
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['cancelArbitration'], 'cancelArbitration', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '取消仲裁', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 强制仲裁（客服仲裁
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function customArbitration($orderDatas) {}

    /*
     * 申请验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
     
    public static function applyComplete($orderDatas) {}

    /*
     * 取消验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     
     
    public static function cancelComplete($orderDatas) {}

    /**
     * 完成验收
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function complete($orderDatas) {
       	try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['third_order_no'],
                'timestamp' => time(),
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['complete'], 'complete', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单完成', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function lock($orderDatas) {
        try {
           $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['lock'], 'lock', $orderDatas);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '锁定', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 取消锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelLock($orderDatas) {
        try {
           $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
            // 发送
            static::normalRequest($options, config('leveling.wanzi.url')['cancelLock'], 'cancelLock', $orderDatas);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '取消锁定', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
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
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
			
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['delete'], 'delete', $orderDatas);

            return true;
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '删除订单', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }


    /**
     * 修改订单(未接单时候的修改订单)
     * @return [type] [description]
     */
    public static function updateOrder($orderDatas) {
    	try {
    		$gameName = Game::find($orderDatas['game_id']);
	        $datas = [
				'order_no'                         => $orderDatas['wanzi_order_no'],
				'game_name'                        => $gameName ? $gameName->name : '',
				'game_region'                      => $orderDatas['region'],
				'game_serve'                       => $orderDatas['serve'],
				'game_account'                     => $orderDatas['account'],
				'game_password'                    => $orderDatas['password'],
				'game_leveling_type'               => $orderDatas['game_leveling_type'],
				'game_leveling_title'              => $orderDatas['game_leveling_title'],
				'game_leveling_price'              => $orderDatas['amount'],
				'game_leveling_day'                => $orderDatas['game_leveling_day'],
				'game_leveling_hour'               => $orderDatas['game_leveling_hour'],
				'game_leveling_security_deposit'   => $orderDatas['security_deposit'],
				'game_leveling_efficiency_deposit' => $orderDatas['efficiency_deposit'],
				'game_leveling_requirements'       => $orderDatas['game_leveling_requirements'],
				'game_leveling_instructions'       => $orderDatas['game_leveling_instructions'],
				'businessman_phone'                => $orderDatas['client_phone'],
				'businessman_qq'                   => $orderDatas['user_qq'],
				'order_password' 				   => $orderDatas['order_password'],
				'game_role'						   => $orderDatas['role'],
	        ]; 

	        $datas = json_encode($datas);

	        $client = new Client();
            $response = $client->request('POST', config('leveling.wanzi.url')['updateOrder'], [
            	'form_params' => [
	            	'data' => base64_encode(openssl_encrypt($datas, 'aes-128-cbc', config('leveling.wanzi.aes_key'), true, config('leveling.wanzi.aes_iv')))
            	],
	            'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            myLog('wanzi-update-order-result', ['请求参数' => $datas, '地址' => config('leveling.wanzi.url')['updateOrder'], '结果' => $result ?? '']);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '修改订单', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 订单加时
     * 增加后的总时间
     */
    public static function addTime($orderDatas) {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
                'day'     => $orderDatas['game_leveling_day'],
                'hour'    => $orderDatas['game_leveling_hour'],
            ];

            $sign = static::getSign($options);
            $options['sign'] = $sign;

	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['addTime'], 'addTime', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单加时', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 订单加款
     * 增加后的总款
     */
    public static function addMoney($orderDatas) {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
                'amount'    => $orderDatas['game_leveling_amount'],
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['addMoney'], 'addMoney', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单加款', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取订单详情
     * @return [type] [description]
     */
    public static function orderDetail($orderDatas) {
        try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	return static::normalRequest($options, config('leveling.wanzi.url')['orderDetail'], 'orderDetail', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取订单完成截图
     * @return [type] [description]
     */
    public static function getScreenshot($orderDatas) {
        try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	$dataList = static::normalRequest($options, config('leveling.wanzi.url')['getScreenshot'], 'getScreenshot', $orderDatas);

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
    		myLog('wanzi-local-error', ['方法' => '获取订单完成截图', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取留言
     * @return [type] [description]
     */
    public static function getMessage($orderDatas) {
        try {
	        $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	$message = static::normalRequest($options, config('leveling.wanzi.url')['getMessage'], 'getMessage', $orderDatas);

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
    		myLog('wanzi-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 回复留言
     * @return [type] [description]
     */
    public static function replyMessage($orderDatas) {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
                'message' => $orderDatas['message'],
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['replyMessage'], 'replyMessage', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 修改接单之后的游戏账号密码
     * @return [type] [description]
     */
    public static function updateAccountAndPassword($orderDatas) {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
                'amount'    => $orderDatas['game_leveling_amount'],
                'account'   => $orderDatas['account'],
                'password'  => $orderDatas['password'],
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['updateAccountAndPassword'], 'updateAccountAndPassword', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 91独有的置顶功能
     * @param [type] $orderDatas [description]
     */
    public static function setTop($orderDatas)
    {
    	try {
	        $options = [
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'isTop'   => 1,
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['setTop'], 'setTop', $orderDatas);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单置顶', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取仲裁详情
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function getArbitrationInfo($orderDatas)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;

            // 发送
            $result = static::normalRequest($options, config('leveling.wanzi.url')['getArbitrationInfo'], 'getArbitrationInfo', $orderDatas);
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
            myLog('wanzi-local-error', ['方法' => '获取仲裁详情', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }

    /**
     * 添加仲裁证据
     * @param [type] $orderDatas [description]
     */
    public static function addArbitrationInfo($orderDatas)
    {
        try {
            $options = [
                'app_id'    => config('leveling.wanzi.app_id'),
                'order_no'  => $orderDatas['wanzi_order_no'],
                'timestamp' => time(),
                'reason'    => $orderDatas['add_content'],
            ];
            
            $sign = static::getSign($options);
            $options['sign'] = $sign;
            $options['image'] = !empty($orderDatas['pic']) ? $orderDatas['pic'] : '';

            // 发送
            return static::formDataRequest($options, config('leveling.wanzi.url')['addArbitrationInfo'], 'addArbitrationInfo', $orderDatas);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '添加仲裁证据', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }
}
