<?php

namespace App\Services\Leveling;

use Exception;
use Carbon\Carbon;
use App\Models\Game;
use GuzzleHttp\Client;
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
	        		if (isset($arrResult['result']) && $arrResult['result'] != 0) {
        				$message = $arrResult['reason'] ?? '丸子接口返回错误';

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
    public static function normalRequest($options = [], $url= '', $method = 'POST')
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
                    if (isset($arrResult['result']) && $arrResult['result'] != 0) {
                        $message = $arrResult['reason'] ?? '91接口返回错误';

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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['onSale']);
    	} catch (Exception $e) {
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['offSale']);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '上架', '原因' => $e->getMessage()]);
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
				'account'              => config('leveling.wanzi.account'),
				'sign'                 => config('leveling.wanzi.sign'),
				'oid'                  => $orderDatas['wanzi_order_no'],
				'selfCancel.pay_price' => $orderDatas['pay_amount'],
				'selfCancel.pay_bond'  => $orderDatas['deposit'],
				'selfCancel.content'   => $orderDatas['revoke_message'] ?? '空',
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['applyRevoke']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['cancelRevoke']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'v'       => 1,
				'p'       => config('leveling.wanzi.password'),
	        ];
	       	// 发送
	       	$result = static::normalRequest($options, config('leveling.wanzi.url')['agreeRevoke']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'v'       => 2,
				'p'       => config('leveling.wanzi.password'),
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['refuseRevoke']);
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
				'account'        => config('leveling.wanzi.account'),
				'sign'           => config('leveling.wanzi.sign'),
				'oid'            => $orderDatas['wanzi_order_no'],
				'appeal.title'   => '申请仲裁',
				'appeal.content' => $orderDatas['complain_message'],
				'pic1'           => $orderDatas['pic1'],
				'pic2'           => $orderDatas['pic2'],
				'pic3'           => $orderDatas['pic3'],
	        ];
	       	// 发送
	       	static::formDataRequest($options, config('leveling.wanzi.url')['applyArbitration']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'aid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['cancelArbitration']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'p'       => config('leveling.wanzi.password'),
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['complete']);
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
        
    }

    /**
     * 取消锁定
     * @param  [type] $orderDatas [description]
     * @return [type]             [description]
     */
    public static function cancelLock($orderDatas) {
        try {
	        $options = [
				'account'         => config('leveling.wanzi.account'),
				'sign'            => config('leveling.wanzi.sign'),
				'platformOrderNo' => $orderDatas['wanzi_order_no'],
				'timestamp'       => $time,
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['cancelLock']);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '解除锁定', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['delete']);
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
	            	'data' => base64_encode(openssl_encrypt($datas, 'aes-128-cbc', config('leveling.wanzi.aes_key'), true, config('leveling.wanzi.aes_iv'))),
	            	"platformSign" => config('leveling.wanzi.platform-sign'),
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'day'     => $orderDatas['game_leveling_day'],
				'hour'    => $orderDatas['game_leveling_hour'],
	        ];

	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['addTime']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'appwd'   => config('leveling.wanzi.password'),
				'cash'    => $orderDatas['game_leveling_amount'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['addMoney']);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	return static::normalRequest($options, config('leveling.wanzi.url')['orderDetail']);
    	} catch (Exception $e) {
    		myLog('wanzi-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	$dataList = static::normalRequest($options, config('leveling.wanzi.url')['getScreenshot']);

	       	if (isset($dataList) && $dataList['result'] == 0 && !empty($dataList['data'])) {
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
    		myLog('wanzi-local-error', ['方法' => '订单详情', '原因' => $e->getMessage()]);
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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
	        ];
	       	// 发送
	       	$message = static::normalRequest($options, config('leveling.wanzi.url')['getMessage']);

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
				'account' => config('leveling.wanzi.account'),
				'sign'    => config('leveling.wanzi.sign'),
				'oid'     => $orderDatas['wanzi_order_no'],
				'mess'    => $orderDatas['message'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['replyMessage']);
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
				'account'   => config('leveling.wanzi.account'),
				'sign'      => config('leveling.wanzi.sign'),
				'oid'       => $orderDatas['wanzi_order_no'],
				'newAcc'    => $orderDatas['account'],
				'newAccPwd' => $orderDatas['password'],
	        ];
	       	// 发送
	       	static::normalRequest($options, config('leveling.wanzi.url')['updateAccountAndPassword']);
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
	       	static::normalRequest($options, config('leveling.wanzi.url')['setTop']);
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
                'account' => config('leveling.wanzi.account'),
                'sign'    => config('leveling.wanzi.sign'),
                'aid'     => $orderDatas['wanzi_order_no'],
            ];
            // 发送
            $lists = static::normalRequest($options, config('leveling.wanzi.url')['appeals']);
            // 循环仲裁列表
            $infos = [];
            if (isset($lists) && $lists['result'] == 0 && ! empty($lists)) {
                foreach ($lists['data'] as $key => $list) {
                    if (isset($list['order']) && isset($list['order']['order_id'])) {
                        if ($list['order']['order_id'] == $orderDatas['wanzi_order_no']) {
                            $infos[$key] = $list;
                        }
                    }
                }
            }

            // 获取详情
            $details = [];
            if (isset($infos) && count($infos) > 0) {
                $infos = array_values($infos);
                // 只要最新的仲裁详情
                $detailOptions = [
                    'account' => config('leveling.wanzi.account'),
                    'sign'    => config('leveling.wanzi.sign'),
                    'aid'     => $infos[0]['id'],
                ];
                $result = static::normalRequest($detailOptions, config('leveling.wanzi.url')['seeappeal']);

                if (isset($result) && $result['result'] == 0 && isset($result['data']) && isset($result['data']['evis'])) {
                    $details = $result['data'];
                }
            }

            if (isset($details['appeal']['pic1'])) {
                $details['appeal']['pic1'] = env('WANZI_API_URL').'/gameupload/appeal/'.$details['appeal']['uid'].'/'.$details['appeal']['pic1'];
            }

            if (isset($details['appeal']['pic2'])) {
                $details['appeal']['pic2'] = env('WANZI_API_URL').'/gameupload/appeal/'.$details['appeal']['uid'].'/'.$details['appeal']['pic2'];
            }

            if (isset($details['appeal']['pic3'])) {
                $details['appeal']['pic3'] = env('WANZI_API_URL').'/gameupload/appeal/'.$details['appeal']['uid'].'/'.$details['appeal']['pic3'];
            }
            if (! isset($details) || ! is_array($details) || ! isset($details['appeal']) || count($details) < 1) {
                return '暂无相关信息';
            }
            $arr = [];
            $arr['detail']['who'] = config('leveling.wanzi.uid') ? '我方' : (! isset($details['appeal']['uid']) ? '客服' : '对方');
            $arr['detail']['created_at'] = $details['appeal']['created_on'];
            $arr['detail']['content'] = $details['appeal']['content'];
            $arr['detail']['arbitration_id'] = $details['appeal']['id'];
            $arr['detail']['pic1'] = $details['appeal']['pic1'];
            $arr['detail']['pic2'] = $details['appeal']['pic2'];
            $arr['detail']['pic3'] = $details['appeal']['pic3'];

            foreach($details['evis'] as $k => $detail) {
                $arr['info'][$k]['who'] = config('leveling.show91.uid') ? '我方' : (! isset($detail['uid']) ? '客服' : '对方');
                $arr['info'][$k]['created_at'] = $detail['created_on'];
                $arr['info'][$k]['content'] = $detail['content'];
                $arr['info'][$k]['pic'] = $detail['pic'];
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
                'account'           => config('leveling.wanzi.account'),
                'sign'              => config('leveling.wanzi.sign'),
                'appealEvi.aid'     => $orderDatas['arbitration_id'],
                'appealEvi.content' => $orderDatas['add_content'],
                'pic1'              => !empty($orderDatas['pic']) ? base64ToBlob($orderDatas['pic']) : '',
            ];

            // 发送
            return static::formDataRequest($options, config('leveling.wanzi.url')['addevidence']);
        } catch (Exception $e) {
            myLog('wanzi-local-error', ['方法' => '添加仲裁证据', '原因' => $e->getMessage()]);
            throw new DailianException($e->getMessage());
        }
    }
}
