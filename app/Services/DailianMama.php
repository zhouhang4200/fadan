<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\ThirdGame;
use App\Models\ThirdArea;
use App\Models\ThirdServer;
use App\Models\OrderDetail;
use App\Models\GoodsTemplate;
use App\Models\LevelingConsult;
use App\Exceptions\DailianException;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;
use App\Models\OrderRegionCorrespondence;

class DailianMama
{
	 /**
     * form-data 格式提交数据
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function formDataRequest($url, $options = '', $method = 'POST')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: multipart/form-data']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 普通提交
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function normalRequest($url, $options = '', $method = 'POST')
    {
        $client = new Client;
        $response = $client->request($method, $url, [
            'query' => $options,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * 发送请求
     * @param $url
     * @param $options
     * @return string
     */
    public static function request($url, $options)
    {
        // 添加公共参数
        $options['sourceid']  = config('dailianmama.source_id');
        $options['timestamp'] = time();
        // 生成签名
        $options['sign'] = self::generateSign($options);
        // 发送请求
        $client = new Client;
        $response = $client->request('POST', $url, [
            'query' => http_build_query($options),
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * 返回接口自定义错误信息, 此方法目前在此控制器没有用到
     * @param  [type] $res [description]
     * @return [type]      [description]
     */
    // public static function returnErrorMessage($res = null)
    // {
    //     $res = json_decode($res, true);

    //     if (! $res) {
    //         throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
    //     }
    //     // 由于代练妈妈接口没有统一的返错机制，
    //     // 如果要抓取到详细的错误，可以把这里写到每一个接口里面
    //     // 根据每个接口返回的错误信息格式返回错误信息
    //     if ($res && $res['result'] !== 1) {
    //         if (null !== $res['data'])
    //         {
    //             // 发布订单
    //             if (is_array($res['data'])) {
    //                 $error = $res['data']['message'];
    //             }
    //         } else {
    //             $error = '失败';
    //         }
    //         throw new DailianException($error);
    //     }
    //     return $res;
    // }

    /**
     * 下订单和修改订单接口
     * @param  [type]  $order [description]
     * @param  boolean $bool  [默认为false， 为true时修改订单]
     * @return [type]         [description]
     */
    public static function releaseOrder($order, $bool = false)
    {
    	$orderDetails = static::getOrderDetails($order->no);
        $templateId =  GoodsTemplate::where('game_id', $order->game_id)->where('service_id', 4)->value('id'); //模板id
        // 我们的区
        $areaTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)
                ->where('field_name', 'region')
                ->value('id');
        $areaId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $areaTemplateWidgetId)
                ->where('field_name', 'region')
                ->where('field_value', $orderDetails['region'])
                ->value('id');
        // 我们的服
        $serverTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)
                ->where('field_name', 'serve')
                ->value('id');
        $serverId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $serverTemplateWidgetId)
                ->where('parent_id', $areaId)
                ->where('field_name', 'serve')
                ->where('field_value', $orderDetails['serve'])
                ->value('id');
        // 找第三方的区服信息
        $orderRegionCorrespondence = OrderRegionCorrespondence::where('third', 2)
            ->where('game_id', $order->game_id)
            ->where('area_id', $areaId)
            ->where('server_id', $serverId)
            ->first();

        if (! $orderRegionCorrespondence) {
            return ['status' => 0, 'message' => '代练妈妈代练平台:代练妈妈平台下没有此游戏！'];
        }

        $thirdAreaName = $orderRegionCorrespondence->third_area_name;
        $thirdServerName = $orderRegionCorrespondence->third_server_name;
        $thirdGameName = $orderRegionCorrespondence->third_game_name; 

        $dailianTime = $orderDetails['game_leveling_day'] ? 
            bcadd(bcmul($orderDetails['game_leveling_day'], 24), $orderDetails['game_leveling_hour'], 0)
            : $orderDetails['game_leveling_hour'];
        // 下单时候的签名
        $sign = md5("accountpwd={$orderDetails['password']}&accountvalue={$orderDetails['account']}&areaname={$thirdAreaName}&autologinphone={$orderDetails['client_phone']}&content={$orderDetails['customer_service_remark']}&dlcontent={$orderDetails['game_leveling_requirements']}&dltype={$orderDetails['game_leveling_type']}&efficiency={$orderDetails['efficiency_deposit']}&gamename={$thirdGameName}&linktel={$orderDetails['user_phone']}&orderorgin=0&ordertitle={$orderDetails['game_leveling_title']}&otherdesc={$orderDetails['game_leveling_instructions']}&qq={$orderDetails['user_qq']}&requiretime=".$dailianTime."&rolename={$orderDetails['role']}&safedeposit={$orderDetails['security_deposit']}&servername={$thirdServerName}&sourceid=".config('dailianmama.source_id')."&timestamp=".time()."&title=无&unitprice=".$order->amount.config('dailianmama.share_key'));

        // 下面的数组没用可以删掉，仅仅用来看需要哪些参数
    	$options = [
            'gamename'       => $thirdGameName, // 与代练妈妈保持一致
            'areaname'       => $thirdAreaName, // 与代练妈妈保持一致
            'servername'     => $thirdServerName, // 与代练妈妈保持一致
            'ordertitle'     => $orderDetails['game_leveling_title'], // 代练标题
            'orderorgin'     => 0, // 0普通， 1私有， 2优质
            'unitprice'      => $order->amount, // 订单金额
            'safedeposit'    => $orderDetails['security_deposit'], // 安全保证金
            'efficiency'     => $orderDetails['efficiency_deposit'], // 效率保证金
            'requiretime'    => $dailianTime, // 要求代练时间
            'accountvalue'   => $orderDetails['account'], //账号名
            'accountpwd'     => $orderDetails['password'], // 密码
            'rolename'       => $orderDetails['role'], // 角色
            'dlcontent'      => $orderDetails['game_leveling_requirements'], // 代练要求
            'dltype'         => $orderDetails['game_leveling_type'], // 排位，陪玩， 晋级， 定位, 其他...?
            // 'orginuserid' => '', // 发布私有订单使用?
            'otherdesc'      => $orderDetails['game_leveling_instructions'], // 当前游戏说明?
            // 'price'          => $order->original_amount, // 备注金额? => 来源价格
            'title'          => '无', // 备注标题?
            'content'        => $orderDetails['customer_service_remark'], // 备注内容?
            'linktel'        => $orderDetails['user_phone'], // 发单人联系电话?
            'qq'             => $orderDetails['user_qq'], // 发单人联系qq？
            // 'saveuserbak' => '' // 值为save时只修改备注 ?
            'autologinphone' => $orderDetails['client_phone'], // 号主电话?
            // 'subid'          => '', // 子账号id?
            'sourceid'       => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp'      => time(), // unix时间戳
            'sign'           => $sign, // 签名
    	];

        // 这是下单时候需要传的参数
        $options = "gamename=".urlencode($thirdGameName)."&areaname=".urlencode($thirdAreaName)."&servername=".urlencode($thirdServerName)."&ordertitle=".urlencode($orderDetails['game_leveling_title'])."&linktel=".urlencode($orderDetails['user_phone'])."&orderorgin=".urlencode('0')."&unitprice=".urlencode($order->amount)."&safedeposit=".urlencode($orderDetails['security_deposit'])."&efficiency=".urlencode($orderDetails['efficiency_deposit'])."&requiretime=".urlencode($dailianTime)."&accountvalue=".urlencode($orderDetails['account'])."&accountpwd=".urlencode($orderDetails['password'])."&rolename=".urlencode($orderDetails['role'])."&dlcontent=".urlencode($orderDetails['game_leveling_requirements'])."&dltype=".urlencode($orderDetails['game_leveling_type'])."&otherdesc=".urlencode($orderDetails['game_leveling_instructions'])."&title=".urlencode('无')."&content=".urlencode($orderDetails['customer_service_remark'])."&qq=".urlencode($orderDetails['user_qq'])."&autologinphone=".urlencode($orderDetails['client_phone'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
        // 修改订单参数

        if ($bool) {
            if (! $orderDetails['dailianmama_order_no']) {
                throw new DailianException('代练妈妈平台:代练妈妈订单号不存在，修改失败!');
            }
            // $options['orderid'] = $orderDetails['dailianmama_order_no'];
            // 修改订单时候的签名
            $sign  = md5("accountpwd={$orderDetails['password']}&accountvalue={$orderDetails['account']}&areaname={$thirdAreaName}&autologinphone={$orderDetails['client_phone']}&content={$orderDetails['customer_service_remark']}&dlcontent={$orderDetails['game_leveling_requirements']}&dltype={$orderDetails['game_leveling_type']}&efficiency={$orderDetails['efficiency_deposit']}&gamename={$thirdGameName}&linktel={$orderDetails['user_phone']}&orderid={$orderDetails['dailianmama_order_no']}&orderorgin=0&ordertitle={$orderDetails['game_leveling_title']}&otherdesc={$orderDetails['game_leveling_instructions']}&qq={$orderDetails['user_qq']}&requiretime=".$dailianTime."&rolename={$orderDetails['role']}&safedeposit={$orderDetails['security_deposit']}&servername={$thirdServerName}&sourceid=".config('dailianmama.source_id')."&timestamp=".time()."&title=无&unitprice=".$order->amount.config('dailianmama.share_key'));
            // 这是修改订单需要传过去的数据
            $options = "gamename=".urlencode($thirdGameName)."&areaname=".urlencode($thirdAreaName)."&servername=".urlencode($thirdServerName)."&ordertitle=".urlencode($orderDetails['game_leveling_title'])."&linktel=".urlencode($orderDetails['user_phone'])."&orderorgin=".urlencode('0')."&unitprice=".urlencode($order->amount)."&safedeposit=".urlencode($orderDetails['security_deposit'])."&efficiency=".urlencode($orderDetails['efficiency_deposit'])."&requiretime=".urlencode($dailianTime)."&accountvalue=".urlencode($orderDetails['account'])."&accountpwd=".urlencode($orderDetails['password'])."&rolename=".urlencode($orderDetails['role'])."&dlcontent=".urlencode($orderDetails['game_leveling_requirements'])."&qq=".urlencode($orderDetails['user_qq'])."&dltype=".urlencode($orderDetails['game_leveling_type'])."&otherdesc=".urlencode($orderDetails['game_leveling_instructions'])."&title=".urlencode('无')."&content=".urlencode($orderDetails['customer_service_remark'])."&autologinphone=".urlencode($orderDetails['client_phone'])."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
        }
        $res = static::normalRequest(config('dailianmama.url.releaseOrder'), $options);

        // return static::returnErrorMessage($res);
        // 
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            return ['status' => 0, 'message' => '代练妈妈平台:外部接口错误,请重试!'];
            // throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            if (is_array($res['data'])) {
                return ['status' => 0, 'message' => '代练妈妈平台:'.$res['data']['message']];
                // throw new DailianException($res['data']['message']);
            } else {
                return ['status' => 0, 'message' => '代练妈妈平台:'.$res['data']];
                // throw new DailianException($res['data']);
            }
        }
        return ['status' => 1, 'order_no' => $res['data']['orderid']];
    }

    /**
     * 订单上架,上架接口不能刷新订单时间，刷新未接手订单请使用刷新未接手订单接口
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function upOrder($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

        $sign = md5("orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            'orderid'   => $orderDetails['dailianmama_order_no'], // 代练妈妈订单id
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
    	];
        // 传过去的参数形式
        $options = "orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.upOrder'), $options);

        // return static::returnErrorMessage($res);
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:'.$res['data']['message']);
        }
        return $res;
    }

    /**
     * 下架,正在接手和已经接手的订单无法下架
     * @param  [type] $order [description]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public static function closeOrder($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

        $sign = md5("orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            'orderid'   => $orderDetails['dailianmama_order_no'],
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
    	];
        // 传过去的参数形式
        $options = "orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.closeOrder'), $options);

        // return static::returnErrorMessage($res);
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:'.$res['data']);
        }
        return $res;
    }

    /**
     * 删除订单,正在接手和已经接手的订单无法删除
     * @param  [type] $order [description]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public static function deleteOrder($order, $bool = false)
    {
    	$orderDetails = static::getOrderDetails($order->no);

        $sign = md5("orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
        $options = [
            'orderid'   => $orderDetails['dailianmama_order_no'],
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
        ];
        // 传过去的参数形式
        $options = "orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.deleteOrder'), $options);

        // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:'.$res['data']);
        }
        return $res;
    }

    /**
     * 订单详情
     * @param  [type] $order [description]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public static function orderinfo($order, $bool = false)
    {
    	$orderDetails = static::getOrderDetails($order->no);

        $sign = md5("orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
        $options = [
            'orderid'   => $orderDetails['dailianmama_order_no'], 
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
        ]; 
        // 传过去的参数形式
        $options = "orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

	    $res = static::normalRequest(config('dailianmama.url.orderinfo'), $options);

	    // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败！');
        }
        return $res; 
    }

    /**
     * 请使用此接口来刷新未接手订单，此接口可刷新所有未接手状态的订单发布时间。注意：代练妈妈规定此接口在一定时间内只能调用一次
     * @param  [type] $order [description]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public static function refreshAllOrderTime($order = null, $bool = false)
    {
        $sign = md5("sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
        ];
        // 传过去的参数形式
        $options = "sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

	    $res = static::normalRequest(config('dailianmama.url.refreshAllOrderTime'), $options);

	    // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:'.$res['data']);
        }
        return $res; 
    }

    /**
     * 2.7.获得发布订单状态列表
     * 此接口用于获取发布订单的状态列表
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function getReleaseOrderStatusList($order = null, $bool = false)
    {
        $sign = md5("orderstatus=&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            'orderstatus' => '', // 订单状态
            // 'subid'       => '', // 子账号id?
            'sourceid'    => 1, // 与代练妈妈约定的标识
            'timestamp'   => time(), // unix时间戳
            'sign'        => $sign, // 签名
    	];
        // 传过去的参数形式
        $options = "orderstatus=&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.getReleaseOrderStatusList'), $options);

	    // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        return $res; 
    }

    /**
     * 支付密码验证
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function checkTradePassword($order = null, $bool = false)
    {
        $sign = md5("sourceid=".config('dailianmama.source_id')."&timestamp=".time()."&tradePassword=".md5(md5(config('dailianmama.pay_password')).'dlapp')."&type=md5".config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            'tradePassword' => md5(md5(config('dailianmama.pay_password')).'dlapp'), // 支付密码
            'type'          =>'md5', // 类型
            // 'subid'         => '', // 子账号id?
            'sourceid'      => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp'     => time(), // unix时间戳
            'sign'          => $sign, // 签名
    	];
        // 传过去的参数形式
        $options = "sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&tradePassword=".urlencode(md5(md5(config('dailianmama.pay_password')).'dlapp'))."&type=".urlencode('md5')."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.checkTradePassword'), $options);

	    // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:'.$res['data']['msg']);
        }
        return $res; 
    }

    /**
     * 订单操作
     * @param  [type] $order [description]
     * @param  [type] $operate [操作编号，要看代练妈妈文档]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public static function operationOrder($order, $operate, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

        // 支付密码验证
        $passwordKey = static::checkTradePassword();

        $sign = md5("control=".$operate."&orderid=".$orderDetails['dailianmama_order_no']."&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            'orderid'             => $orderDetails['dailianmama_order_no'],
            'control'             => $operate, // 2002, 20010...
            'checkTradePwdRDM' => '123456', // 支付密码验证?
            // 'reason'           => '', // 原因：20006 申请撤销， 20004提交异常， 20007申请仲裁?
            // 'refundment'       => '', // 上家支付的代练费: 20006 申请撤销?
            // 'bail'             => '', // 赔偿打手的双金：20006 申请撤销 ?
            // 'requiretimehadd'  => '', // 增加的小时数 22001补时?
            // 'unitpriceadd'     => '', // 增加的金额数, 22002 补款?
            // 'accountpwd'       => '', // 密码， 22003修改密码?
            // 'subid'               => '', // 子账号id?
            'sourceid'            => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp'           => time(), // unix时间戳
            'sign'                => $sign, // 签名
    	];

        $consult = LevelingConsult::where('order_no', $order->no)->first();
        switch ($operate) {
            case 20006: // 申请撤销
                // $options['reason'] = $consult->revoke_message;
                // $options['refundment'] = $consult->amount;
                // $options['bail'] = $consult->deposit;

                $sign = md5("bail=".$consult->deposit."&checkTradePwdRDM=".$passwordKey['data']."&control={$operate}&orderid={$orderDetails['dailianmama_order_no']}&reason={$consult->revoke_message}&refundment={$consult->amount}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));

                $options = "bail=".urlencode($consult->deposit)."&checkTradePwdRDM=".urlencode($passwordKey['data'])."&control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&reason=".urlencode($consult->revoke_message)."&refundment=".urlencode($consult->amount)."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20007: // 申请仲裁
                // $options['reason'] = $consult->complain_message;
                $sign = md5("control={$operate}&orderid={$orderDetails['dailianmama_order_no']}&reason={$consult->revoke_message}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));

                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&reason=".urlencode($consult->revoke_message)."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 22001: // 补时
                // $options['requiretimehadd'] = bcadd(bcmul($order->addDays, 24), $order->addHours);
                $sign = md5("control={$operate}&orderid={$orderDetails['dailianmama_order_no']}&requiretimehadd=".bcadd(bcmul($order->addDays, 24), $order->addHours)."&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));

                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&requiretimehadd=".urlencode(bcadd(bcmul($order->addDays, 24), $order->addHours))."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 22002: // 补款, 补的是差价,比如订单开始是5元，填了6元，这里传1元
                $sign = md5("checkTradePwdRDM=".$passwordKey['data']."&control={$operate}&orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time()."&unitpriceadd=".$order->addAmount.config('dailianmama.share_key'));

                $options = "checkTradePwdRDM=".urlencode($passwordKey['data'])."&control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&unitpriceadd=".urlencode($order->addAmount)."&sign=".urlencode($sign);
                break;
            case 22003: // 修改密码
                $sign = md5("accountpwd=".$orderDetails['password']."&control={$operate}&orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));

                $options = "accountpwd=".urlencode($orderDetails['password'])."&control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20002: // 锁定账号
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20010: // 取消锁定
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20005: // 申请验收
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20017: // 取消验收
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20013: // 验收完成
                $sign = md5("checkTradePwdRDM=".$passwordKey['data']."&control=".$operate."&orderid=".$orderDetails['dailianmama_order_no']."&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));

                $options = "checkTradePwdRDM=".urlencode($passwordKey['data'])."&control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20012: // 取消撤销
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20009: // 同意撤销
                $sign = md5("checkTradePwdRDM=".$passwordKey['data']."&control=".$operate."&orderid=".$orderDetails['dailianmama_order_no']."&sourceid=".config('dailianmama.source_id')."&timestamp=".time().config('dailianmama.share_key'));

                $options = "checkTradePwdRDM=".urlencode($passwordKey['data'])."&control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20008: // 取消仲裁
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20004: // 提交异常
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
            case 20011: // 取消异常
                $options = "control=".urlencode($operate)."&orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);
                break;
        }

    	$res = static::normalRequest(config('dailianmama.url.operationOrder'), $options);

        // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);
        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            if ($res['data'] == '对不起，订单状态可能已经改变！' && $operate == 20007) {
                $res['data'] = '该订单被代练妈妈平台接单，该状态没有【申请仲裁】操作！';
            }
            throw new DailianException('代练妈妈平台:'.$res['data']);
        }

        return $res; 
    }

    /**
     * 用于获取订单中的留言，调用此接口系统将默认此订单已读
     * @param  string $orderId 代练妈妈订单号
     * @param int $beginId 从那个ID开始取
     * @param  boolean $bool [description]
     * @return array  $data 获取的留言
     * @throws DailianException
     */
    public static function chatOldList($orderId, $beginId = 0, $bool = false)
    {
        // 请示参数，更多请求参数参考对接文档
    	$options = [
            'orderid'   => $orderId, // 代练妈妈的订单号
    	];
        // 如果有传入起始ID就加入此参数
        if ($beginId != 0) {
            $options['beginid'] = $beginId;
        }
        // 请求接口
    	$res = static::request(config('dailianmama.url.chatOldList'), $options);
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        return $res['data'];
    }

    /**
     * 获取订单截图记录
     * @param $orderNo
     * @return mixed|string
     * @throws DailianException
     */
    public static function getOrderPictureList($orderNo)
    {
    	$options = [
            'orderid'   => $orderNo,
    	];

    	$res = static::request(config('dailianmama.url.getOrderPictureList'), $options);

        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        return $res['data']['info2'];
    }

    /**
     * 发送订单留言
     * @param string $orderNo 订单号
     * @param string $message  留言内容
     * @return mixed|string
     * @throws DailianException
     */
    public static function addChat($orderNo, $message)
    {
        // 请求参数
    	$options = [
            'orderid'   => $orderNo,
            'content'   => $message,
    	];
        // 请求接口
    	$res = static::request(config('dailianmama.url.addChat'), $options);

        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        return $res;  
    }

    /**
     * 上传图片成功后使用此接口将截图记录保存到代练妈妈中。
     * 接口只用于保存订单截图，上传请通过接口获取阿里云临时凭证后使用阿里云SDK进行上传，
     * 上传后将上传成功的地址和订单号截图说明等参数通过此接口保存至代练妈妈系统。
     * (PS:也可以将截图上传到别的平台或网站，再使用保存截图功能保存到代练妈妈。但必须保证截图能查看和时效性。)
     * @param $orderNo
     * @param $imagePath
     * @return bool
     * @throws DailianException
     */
    public static function savePicture($orderNo, $imagePath, $description)
    {
    	$res = static::request(config('dailianmama.url.savePicture'), [
            'orderid'     => $orderNo,
            'imgurl'      => $imagePath,
            'description' => $description, // 截图说明
        ]);

        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        return $res;   
    }

    /**
     * 获取阿里云oss临时凭证,获取后一小时内可用
     * https://help.aliyun.com/document_detail/oss/user_guide/upload_object/thirdparty_upload.html
     * @return array 阿里云临时凭证数组
     * @throws DailianException
     */
    public static function getTempUploadKey()
    {
        $redis = RedisConnect::order();
        $keyCache = $redis->get(config('redis.thirdParty.dailianMamaOssKey'));
        if ($keyCache) {
            return unserialize($keyCache);
        }

    	$res = static::request(config('dailianmama.url.getTempUploadKey'), []);

        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        // 缓存一小时
        $redis->setex(config('redis.thirdParty.dailianMamaOssKey'), 60, serialize($res['data']));

        return $res['data'];
    }

    /**
     * [发单管理接口，查询发布时间在3个月内的订单]
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function releaseOrderManageList($order, $bool = false)
    {
        $sign = md5("orderstatus=1&sourceid=".config('dailianmama.source_id')."&timestamp=".time());
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
    		'orderstatus' => 1, // 1未接单, 2已下架， 3代练中，4异常,6待验收，9撤销中，
    							//7,8已结算，5,10已撤销，iszhc已仲裁,qzcx客服强制撤销
    		// 'keyword'   => '', // 关键词查询, 订单号，标题，接单人?
    		// 'sort'      => '', 
                            // 排序条件， 1降序，2升序， 3总时间降序， 4总时间升序, 5区服降序，6区服升序
    						// 9，发布时间降序， 10发布时间升序, 13解散时间降序， 14， 结算时间升序
    						// 15, 发单账号降序, 16, 发单账号升序， 17接单人降序， 18接单人升序
    						// 19,接单时间降序， 20接单时间升序， 21颜色降序， 22颜色升序
    						// 23,结算时间升序， 24结算时间降序, 25,来源降序， 26来源升序
    						// 27状态降序， 28状态升序， 29申请验收时间升序， 30申请验收时间降序?
            // 'currentNo' => '', // 要查询当前页数，默认查询第一页?
            // 'pageSize'  => '', // 每页条数， 为空默认10条?
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
    	];
        // 传过去的参数形式
        $options = "orderstatus=".urlencode('1')."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.releaseOrderManageList'), $options);

	    // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:操作失败!');
        }
        return $res;   
    }

    /**
     * 获取订单价格
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function getOrderPrice($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);
        // 待签名串
        $sign = md5("orderid={$orderDetails['dailianmama_order_no']}&sourceid=".config('dailianmama.source_id')."&timestamp=".time());
        // 下面这个数组是多余的,仅供参考参数有没有缺失用
    	$options = [
            'orderid'   => $orderDetails['dailianmama_order_no'],
            // 'subid'     => '', // 子账号id?
            'sourceid'  => config('dailianmama.source_id'), // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign'      => $sign, // 签名
    	];
        // 传过去的参数形式
        $options = "orderid=".urlencode($orderDetails['dailianmama_order_no'])."&sourceid=".urlencode(config('dailianmama.source_id'))."&timestamp=".urlencode(time())."&sign=".urlencode($sign);

    	$res = static::normalRequest(config('dailianmama.url.getOrderPrice'), $options);

	    // return static::returnErrorMessage($res);
        
        // 返回错误特殊，特殊处理
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('代练妈妈平台:外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException('代练妈妈平台:'.$res['data']['msg']);
        }
        return $res;   
    }

    /**
     * 获取订单详情
     * @param  [type] $orderNo [description]
     * @return [type]          [description]
     */
    public static function getOrderDetails($orderNo)
    {
        return OrderDetail::where('order_no', $orderNo)->pluck('field_value', 'field_name')->toArray();
    }

    /**
     * 生成接口签名
     * @param array $params 待签名参数
     * @return string md5字符
     */
    public static function generateSign($params)
    {
        ksort($params);
        $signString = '';
        foreach ($params as $key => $value) {
            $signString .= $key . '=' . $value . '&';
        }
        return md5(rtrim($signString, '&') . config('dailianmama.share_key'));
    }
}