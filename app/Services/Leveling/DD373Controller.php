<?php

namespace App\Services\Leveling;

use Exception;
use Carbon\Carbon;
use App\Models\Game;
use GuzzleHttp\Client;
use App\Exceptions\DailianException;

/**
 * 蚂蚁代练操作控制器
 */
class DD373Controller extends LevelingAbstract implements LevelingInterface
{
    /**
     * form-data 格式提交数据
     * @param array $options
     * @param string $url
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    public static function formDataRequest($options = [], $url = '', $method = 'POST')
    {
    	try {
    		$data = [];
	        foreach ($options as $name => $value) {
                $data[$name]['name'] = $name;
                $data[$name]['contents'] = $value;
	        }
	        $options = $data;

	        $client = new Client();
	        $response = $client->request($method, $url, [
	            'multipart' => $options,
	        ]);
	        $result =  $response->getBody()->getContents();

            myLog('dd373-api-log', [$url, $options, $result]);

	        if (! isset($result) || empty($result)) {
                throw new DailianException('请求返回数据不存在');
            }

	        if (isset($result) && ! empty($result)) {
	        	$arrResult = json_decode($result, true);

	        	if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
	        		// 失败
	        		if (isset($arrResult['code']) && $arrResult['code'] != 0) {
        				$message = $arrResult['msg'] ?? 'dd373接口返回错误';

	        			myLog('dd373-return-error', [
	        				'地址' => $url ?? '', 
	        				'失败原因' => $arrResult['msg'] ?? '', 
	        				'失败数据' => $arrResult['data'] ?? '',
	        			]);
        				throw new DailianException($message);
	        		}
			        // 记录日志
			        myLog('dd373-request-logs', [
			            '地址' => $url ?? '',
			            'dd373信息' => $options['jsonData'] ?? ($options['jsonData'] ?? ''),
			            '结果' => $result ? json_decode($result, true) : '',
			        ]);
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
     * @param string $url
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    public static function normalRequest($options = [], $url= '', $method = 'POST')
    {
    	try {
	        $client = new Client();
	        $response = $client->request($method, $url, [
	            'form_params' => $options,
	            'body' => 'x-www-form-urlencoded',
	        ]);
	        $result =  $response->getBody()->getContents();

	        if (! isset($result) || empty($result)) {
                throw new DailianException('请求返回数据不存在');
            }

            myLog('dd373-api-log', [$url, $options, $result]);

	        if (isset($result) && ! empty($result)) {
	        	$arrResult = json_decode($result, true);

	        	if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
	        		// 失败
	        		if (isset($arrResult['code']) && $arrResult['code'] != 0) {
        				$message = $arrResult['msg'] ?? 'dd373接口返回错误';
	        			myLog('dd373-return-error', [
	        				'地址' => $url ?? '', 
	        				'失败原因' => $arrResult['msg'] ?? '', 
	        				'失败数据' => $arrResult['data'] ?? '',
	        			]);
        				throw new DailianException($message);
	        		}
			        // 记录日志
			        myLog('dd373-return-logs', [
			            '地址' => $url ?? '',
			            'dd373信息' => $options['jsonData'] ?? ($options['jsonData'] ?? ''),
			            '结果' => $result ? json_decode($result, true) : '',
			        ]);
	        	}
    		}
			myLog('dd373-request-logs', [
	            '地址' => $url ?? '',
	            '参数' => $options ?? '',
	        ]);
    		return json_decode($result, true);
        } catch (Exception $e) {
        	myLog('dd373-local-error', ['方法' => '请求', '原因' => $e->getMessage()]);

        	throw new Exception($e->getMessage());
        }
    }

    /**
     * 对参数进行加工
     * @param $data
     * @return array
     */
    public static function handleOptions($data)
    {
        return [
			'JsonData'     => json_encode($data),
			'platformSign' => config('leveling.dd373.platform-sign'),
			'Sign'         => static::getSign($data),
        ];
    }

    /**
     * 获取签名
     * @param $data
     * @return string
     */
    public static function getSign($data)
    {
    	$string = "JsonData=".json_encode($data)."&platformSign=".config('leveling.dd373.platform-sign').config('leveling.dd373.key');

    	myLog('dd373-sign-log', ['string' => $string, 'sign' => md5($string)]);

        return md5($string);
    }

    /**
     * 上架
     * @param $orderData
     * @throws DailianException
     */
    public static function onSale($orderData) {
    	try {
	        $time = time();
	        $data = [
	        	'platformOrderNo' => $orderData['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($data);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['onSale']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 下架
     * @param $orderData
     * @throws DailianException
     */
    public static function offSale($orderData) {
        try {
	        $time = time();
            $data = [
	        	'platformOrderNo' => $orderData['dd373_order_no'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($data);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['offSale']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '下架', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 接单
     * @param $orderData
     */
    public static function receive($orderData) {}

    /**
     * 申请撤销
     * @param $orderDatas
     * @throws DailianException
     */
    public static function applyRevoke($orderDatas) {
        try {
        	if (! isset($orderDatas['pay_amount']) || ! isset($orderDatas['deposit'])) {
                throw new DailianException('协商代练费或双金不存在');
            }

	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'payAmount' => $orderDatas['pay_amount'],
	        	'guarantyAQFee' => $orderDatas['deposit'],
	        	'guarantyXLFee' => 0,
	        	'PayUserType' => 1,
	        	'reason' => ! empty($orderDatas['revoke_message']) ? $orderDatas['revoke_message'] : '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['applyRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '申请撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 取消撤销
     * @param $orderData
     * @throws DailianException
     */
    public static function cancelRevoke($orderData) {
        try {
	        $time = time();
            $data = [
	        	'platformOrderNo' => $orderData['dd373_order_no'],
	        	'State' => 2,
	        	'reason' => ! empty($orderData['revoke_message']) ?: '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($data);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['cancelRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '取消撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 同意撤销
     * @param $orderData
     * @throws DailianException
     */
    public static function agreeRevoke($orderData) {
        try {
	        $time = time();
	        $data = [
	        	'platformOrderNo' => $orderData['dd373_order_no'],
	        	'State' => 1,
	        	'reason' => '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($data);
	       	// 发送
	       	$result = static::normalRequest($options, config('leveling.dd373.url')['agreeRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '同意撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 强制撤销
     * @param $orderData
     */
    public static function forceRevoke($orderData) {}

    /**
     * 不同意撤销
     * @param $orderDatas
     * @throws DailianException
     */
    public static function refuseRevoke($orderDatas) {
    	try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'State' => 3,
	        	'reason' => '空',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['refuseRevoke']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '不同意撤销', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 申请仲裁
     * @param $orderDatas
     * @throws DailianException
     */
    public static function applyArbitration($orderDatas)
    {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'reason' => ! empty($orderDatas['complain_message']) ? $orderDatas['complain_message'] : '空',
	        	'timestamp' => $time,
	        ];
            // 对图片进行处理
            $finalPic['fileBase1'] = $orderDatas['pic1'];
            $finalPic['fileBase2'] = $orderDatas['pic1'];
            $finalPic['fileBase3'] = $orderDatas['pic1'];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::formDataRequest(array_merge($options, array_filter($finalPic)), config('leveling.dd373.url')['applyArbitration']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '申请仲裁', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 取消仲裁
     * @param $orderDatas
     * @throws DailianException
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
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 强制仲裁（客服仲裁
     * @param $orderDatas
     */
    public static function customArbitration($orderDatas) {}

    /**
     * 申请验收
     * @param $orderDatas
     */
    public static function applyComplete($orderDatas) {}

    /**
     * 取消验收
     * @param $orderDatas
     */
    public static function cancelComplete($orderDatas) {}

    /**
     * 完成验收
     * @param $orderDatas
     * @throws DailianException
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
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 锁定
     * @param $orderDatas
     * @throws DailianException
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
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 取消锁定
     * @param $orderDatas
     * @throws DailianException
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
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 异常
     * @param $orderDatas
     */
    public static function abnormal($orderDatas) {}

    /**
     * 取消异常
     * @param $orderDatas
     */
    public static function cancelAbnormal($orderDatas) {}

    /**
     * 撤单（删除)
     * @param $orderDatas
     * @throws DailianException
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
    		throw new DailianException($e->getMessage());
    	}
    }


    /**
     * 修改订单(未接单时候的修改订单)
     * @param $orderDatas
     * @throws DailianException
     */
    public static function updateOrder($orderDatas) {
    	try {
    		$time = time();
	        $gameName = Game::find($orderDatas['game_id']);
	        $datas = [
	        	'order_no' => $orderDatas['dd373_order_no'],
	        	'game_name' => $gameName ? $gameName->name : '',
	        	'game_region' => $orderDatas['region'],
	        	'game_serve' => $orderDatas['serve'],
	        	'game_account' => $orderDatas['account'],
	        	'game_password' => $orderDatas['password'],
	        	'game_leveling_type' => $orderDatas['game_leveling_type'],
	        	'game_leveling_title' => $orderDatas['game_leveling_title'],
	        	'game_leveling_price' => $orderDatas['amount'],
	        	'game_leveling_day' => $orderDatas['game_leveling_day'],
	        	'game_leveling_hour' => $orderDatas['game_leveling_hour'],
	        	'game_leveling_security_deposit' => $orderDatas['security_deposit'],
	        	'game_leveling_efficiency_deposit' => $orderDatas['efficiency_deposit'],
	        	'game_leveling_requirements' => $orderDatas['game_leveling_requirements'],
	        	'game_leveling_instructions' => $orderDatas['game_leveling_instructions'],
	        	'businessman_phone' => $orderDatas['user_phone'],
	        	'businessman_qq' => $orderDatas['user_qq'],
	        ]; 

	        $datas = json_encode($datas);

	        $client = new Client();
            $response = $client->request('POST', config('leveling.dd373.url')['updateOrder'], [
            	'form_params' => [
	            	'data' => base64_encode(openssl_encrypt($datas, 'aes-128-cbc', config('leveling.dd373.aes_key'), true, config('leveling.dd373.aes_iv'))),
	            	"platformSign" => config('leveling.dd373.platform-sign'),
            	],
	            'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            // 记录日志
	        myLog('dd373-all-logs', [
	            '修改订单信息' => $datas ?? '',
	            '地址' => config('leveling.dd373.url')['updateOrder'] ?? '',
	            '时间' => Carbon::now()->toDateTimeString(),
	            '结果' => $result ?? '',
	        ]);
        } catch (Exception $e) {
            myLog('dd373-local-error', ['方法' => '修改订单', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
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
	        	'days' => $orderDatas['game_leveling_day'],
	        	'hours' => $orderDatas['game_leveling_hour'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['addTime']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单加时', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
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
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取订单详情
     * @param $orderDatas
     * @return mixed
     * @throws DailianException
     */
    public static function orderDetail($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'platformSign' => config('leveling.dd373.platform-sign'),
	        ];
	        $str = "platformOrderNo=".$orderDatas['dd373_order_no']."&platformSign=".config('leveling.dd373.platform-sign').config('leveling.dd373.key');

	        $datas['Sign'] = md5($str);
	       	// 发送
	       	return static::normalRequest($datas, config('leveling.dd373.url')['orderDetail']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取订单截图
     * @param $orderDatas
     * @return array
     * @throws DailianException
     */
    public static function getScreenshot($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'platformSign' => config('leveling.dd373.platform-sign'),
	        ];
	        $str = "platformOrderNo=".$orderDatas['dd373_order_no']."&platformSign=".config('leveling.dd373.platform-sign').config('leveling.dd373.key');

	        $datas['Sign'] = md5($str);
	       	// 发送
	       	$result =  static::normalRequest($datas, config('leveling.dd373.url')['getScreenshot']);

            $images = [];
            foreach ($result['data'] as $item) {
                $images[] = [
                    'username' => $item['uploadUserName'],
                    'description' => $item['description'],
                    'url' => 'http://' . ltrim($item['imageUrl'], '//'),
                    'created_at' => $item['uploadTime'],
                ];
            }
            return $images;
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单截图', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取留言
     * @param $orderDatas
     * @return array
     * @throws DailianException
     */
    public static function getMessage($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'platformSign' => config('leveling.dd373.platform-sign'),
	        ];
	        $str = "platformOrderNo=".$orderDatas['dd373_order_no']."&platformSign=".config('leveling.dd373.platform-sign').config('leveling.dd373.key');

	        $datas['Sign'] = md5($str);
	       	// 发送
	       	$result =  static::normalRequest($datas, config('leveling.dd373.url')['getMessage']);

            $message = [];
            foreach ($result['data'] as  $item) {
                $message[] = [
                    'sender' => $item['senderType'] == 1 ? '您': '打手',
                    'send_content' => $item['content'],
                    'send_time' => $item['sendTime'],
                ];
            }
            return $message;
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 回复留言
     * @param $orderDatas
     * @throws DailianException
     */
    public static function replyMessage($orderDatas) {
        try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'content' => $orderDatas['message'] ?? '',
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['replyMessage']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 修改接单之后的游戏账号密码
     * @param $orderDatas
     * @throws DailianException
     */
    public static function updateAccountAndPassword($orderDatas) {
       	try {
	        $time = time();
	        $datas = [
	        	'platformOrderNo' => $orderDatas['dd373_order_no'],
	        	'gameAccount' => $orderDatas['account'],
	        	'gamePassWord' => $orderDatas['password'],
	        	'timestamp' => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::normalRequest($options, config('leveling.dd373.url')['updateAccountAndPassword']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 发送截图
     * @param $orderDatas
     * @throws DailianException
     */
    public static function updateImage($orderDatas)
    {
    	try {
	        $time = time();
	        $datas = [
				'platformOrderNo' => $orderDatas['dd373_order_no'],
				'description'     => $orderDatas['description'],
				'timestamp'       => $time,
	        ];
	        // 对参数进行加工
	       	$options = static::handleOptions($datas);
	       	// 发送
	       	static::formDataRequest(array_merge($options, ['fileBase' => $orderDatas['file']]), config('leveling.dd373.url')['updateImage']);
    	} catch (Exception $e) {
    		myLog('dd373-local-error', ['方法' => '发送截图', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    public static function getArbitrateImages()
    {

    }
}
