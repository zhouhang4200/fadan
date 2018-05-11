<?php

namespace App\Services\Leveling;

use Exception;
use Carbon\Carbon;
use App\Models\Game;
use GuzzleHttp\Client;
use App\Exceptions\DailianException;

class Show91Controller extends LevelingAbstract implements LevelingInterface
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
    public static function formDataRequest($options = [], $url = '', $method = 'POST')
    {
    	try {
    		$datas = [];
	        foreach ($options as $name => $value) {
	            $datas[$name]['name'] = $name;
	            $datas[$name]['contents'] = $value;
	        }
	        $client = new Client();
	        $response = $client->request($method, $url, [
	            'multipart' => $datas,
	        ]);
	        $result = $response->getBody()->getContents();

	        if (! isset($result) || empty($result)) {
                throw new DailianException('请求返回数据不存在');
            }

	        if (isset($result) && ! empty($result)) {
	        	$arrResult = json_decode($result, true);

	        	if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
	        		// 失败
	        		if (isset($arrResult['result']) && $arrResult['result'] != 0) {
        				$message = $arrResult['reason'] ?? '91接口返回错误';
	        			myLog('show91-return-error', [
	        				'地址' => $url ?? '', 
	        				'失败错误码' => $arrResult['result'] ?? '', 
	        				'失败原因' => $arrResult['reason'] ?? '',
	        			]);
        				throw new DailianException($message);
	        		}
	        	}
		        // 记录日志
		        myLog('show91-return-log', [
		            '地址' => $url ?? '',
		            '信息' => $options ?? '',
		            '结果' => $result ? json_decode($result, true) : '',
		        ]);
    		}
			myLog('show91-request-log', [
	            '地址' => $url ?? '',
	            '参数' => $options ?? '',
	        ]);
    		return json_decode($result, true);
        } catch (Exception $e) {
        	myLog('show91-local-error', ['方法' => '请求', '原因' => $e->getMessage()]);

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
    public static function normalRequest($options = [], $url= '', $method = 'POST')
    {
    	try {
	        $client = new Client();
	        $response = $client->request($method, $url, [
	            'form_params' => $options,
	        ]);
	        $result =  $response->getBody()->getContents();

	        if (! isset($result) || empty($result)) {
                throw new DailianException('请求返回数据不存在');
            }

	        if (isset($result) && ! empty($result)) {
	        	$arrResult = json_decode($result, true);

	        	if (isset($arrResult) && is_array($arrResult) && count($arrResult) > 0) {
	        		// 失败
	        		if (isset($arrResult['result']) && $arrResult['result'] != 0) {
        				$message = $arrResult['reason'] ?? '丸子接口返回错误';
	        			myLog('show91-return-error', [
	        				'地址' => $url ?? '', 
	        				'失败错误码' => $arrResult['result'] ?? '', 
	        				'失败原因' => $arrResult['reason'] ?? '',
	        			]);
        				throw new DailianException($message);
	        		}
	        	}
		        // 记录日志
		        myLog('show91-return-log', [
		            '地址' => $url ?? '',
		            '信息' => $options ?? '',
		            '结果' => $result ? json_decode($result, true) : '',
		        ]);
    		}
			myLog('show91-request-log', [
	            '地址' => $url ?? '',
	            '参数' => $options ?? '',
	        ]);
    		return json_decode($result, true);
        } catch (Exception $e) {
        	myLog('show91-local-error', ['方法' => '请求', '原因' => $e->getMessage()]);

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
    public static function getSign($datas)
    {
    	
    }

    /**
     * 上架
     * @return [type] [description]
     */
    public static function onSale($orderDatas) {
    	try {
	       	$options = [
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['onSale']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['offSale']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
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
				'account'              => config('leveling.show91.account'),
				'sign'                 => config('leveling.show91.sign'),
				'oid'                  => $orderDatas['show91_order_no'],
				'selfCancel.pay_price' => $orderDatas['pay_amount'],
				'selfCancel.pay_bond'  => $orderDatas['deposit'],
				'selfCancel.content'   => ! empty($orderDatas['revoke_message']) ?: '空',
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['applyRevoke']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '申请撤销', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['cancelRevoke']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '取消撤销', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'v'       => 1,
				'p'       => config('leveling.show91.password'),
	        ];
	       	// 发送
	       	$result = static::normalRequest($options, config('leveling.show91.url')['agreeRevoke']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '同意撤销', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'v'       => 2,
				'p'       => config('leveling.show91.password'),
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['refuseRevoke']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '不同意撤销', '原因' => $e->getMessage()]);
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
				'account'        => config('leveling.show91.account'),
				'sign'           => config('leveling.show91.sign'),
				'oid'            => $orderDatas['show91_order_no'],
				'appeal.title'   => '申请仲裁',
				'appeal.content' => $orderDatas['complain_message'],
				'pic1'           => $orderDatas['pic1'],
				'pic2'           => $orderDatas['pic2'],
				'pic3'           => $orderDatas['pic3'],
	        ];
	       	// 发送
	       	static::formDataRequest($options, config('leveling.show91.url')['applyArbitration']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '申请仲裁', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'aid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['cancelArbitration']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '取消仲裁', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'p'       => config('leveling.show91.password'),
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['complete']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单完成', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function lock($orderDatas) {
        
    }

    /**
     * 取消锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelLock($orderDatas) {
        try {
	        $options = [
				'account'         => config('leveling.show91.account'),
				'sign'            => config('leveling.show91.sign'),
				'platformOrderNo' => $orderDatas['show91_order_no'],
				'timestamp'       => $time,
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['cancelLock']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '解除锁定', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['delete']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '删除订单', '原因' => $e->getMessage()]);
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
				'order_no'                         => $orderDatas['show91_order_no'],
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
				'businessman_phone'                => $orderDatas['user_phone'],
				'businessman_qq'                   => $orderDatas['user_qq'],
				'order_password' 				   => $orderDatas['order_password'],
				'game_role'						   => $orderDatas['role'],
	        ]; 

	        $datas = json_encode($datas);

	        $client = new Client();
            $response = $client->request('POST', config('leveling.show91.url')['updateOrder'], [
            	'form_params' => [
	            	'data' => base64_encode(openssl_encrypt($datas, 'aes-128-cbc', config('leveling.show91.aes_key'), true, config('leveling.show91.aes_iv'))),
	            	"platformSign" => config('leveling.show91.platform-sign'),
            	],
	            'body' => 'x-www-form-urlencoded',
            ]);
            $result = $response->getBody()->getContents();

            myLog('show91-update-order-result', ['请求参数' => $datas, '地址' => config('leveling.show91.url')['updateOrder'], '结果' => $result ?? '']);
        } catch (Exception $e) {
            myLog('show91-local-error', ['方法' => '修改订单', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'day'     => $orderDatas['game_leveling_day'],
				'hour'    => $orderDatas['game_leveling_hour'],
	        ];

	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['addTime']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单加时', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'appwd'   => config('leveling.show91.password'),
				'cash'    => $orderDatas['game_leveling_amount'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['addMoney']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单加款', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	return static::normalRequest($options, config('leveling.show91.url')['orderDetail']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 获取订单截图
     * @return [type] [description]
     */
    public static function getScreenshot($orderDatas) {
        try {
	        $options = [
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	$dataList = static::normalRequest($options, config('leveling.show91.url')['getScreenshot']);

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
    		myLog('show91-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
	        ];
	       	// 发送
	       	$message = static::normalRequest($options, config('leveling.show91.url')['getMessage']);

	       	if (isset($message) && isset($message['result']) && $message['result'] == 0 && isset($message['data'])) {
	       		$sortField = [];
	            $messageArr = [];
	            foreach ($message['data'] as $item) {
	                if (isset($item['id'])) {
	                    $sortField[] = $item['created_on'];
	                } else {
	                    $sortField[] = 0;
	                }
	                $messageArr[] = $item;
	            }
	            // 用ID倒序
	            array_multisort($sortField, SORT_ASC, $messageArr);

	            return $messageArr;
	       	}
	       	return '';
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'mess'    => $orderDatas['message'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['replyMessage']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
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
				'account'   => config('leveling.show91.account'),
				'sign'      => config('leveling.show91.sign'),
				'oid'       => $orderDatas['show91_order_no'],
				'newAcc'    => $orderDatas['account'],
				'newAccPwd' => $orderDatas['password'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['updateAccountAndPassword']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单获取留言', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }

    /**
     * 91独有的置顶功能
     * @param [type] $orderDatas [description]
     */
    public function setTop($orderDatas)
    {
    	try {
	        $options = [
				'account' => config('leveling.show91.account'),
				'sign'    => config('leveling.show91.sign'),
				'oid'     => $orderDatas['show91_order_no'],
				'isTop'   => 1,
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.show91.url')['setTop']);
    	} catch (Exception $e) {
    		myLog('show91-local-error', ['方法' => '订单置顶', '原因' => $e->getMessage()]);
    		throw new DailianException($e->getMessage());
    	}
    }
}