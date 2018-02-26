<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\ThirdGame;
use App\Models\ThirdArea;
use App\Models\ThirdServer;
use App\Models\OrderDetail;
use App\Models\GoodsTemplate;
use App\Exceptions\CustomException;
use App\Exceptions\DailianException;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;

class DailianMama
{
	 /**
     * form-data 格式提交数据
     * @param  [type] $url     [description]
     * @param  [type] $options [description]
     * @param  string $method  [description]
     * @return [type]          [description]
     */
    public static function formDataRequest($url, $options = [], $method = 'POST')
    {
        $params = [
            'subid' => '', // 子账号id?
            'sourceid' => 1, // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign' => 1, // 签名 
        ];

        $options = array_merge($params, $options);

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
    public static function normalRequest($url, $options = [], $method = 'POST')
    {
        $params = [
            'subid' => '', // 子账号id?
            'sourceid' => 1, // 与代练妈妈约定的标识
            'timestamp' => time(), // unix时间戳
            'sign' => 1, // 签名 
        ];

        $options = array_merge($params, $options);

        $client = new Client;
        $response = $client->request($method, $url, [
            'query' => $options,
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * 返回接口自定义错误信息
     * @param  [type] $res [description]
     * @return [type]      [description]
     */
    public static function returnErrorMessage($res = null)
    {
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('外部接口错误,请重试!');
        }

        if ($res && $res['result'] !== 1) {
            throw new DailianException($res['data']);
        }
        return $res;
    }

    /**
     * 下订单和修改订单接口
     * @param  [type]  $order [description]
     * @param  boolean $bool  [默认为false， 为true时修改订单]
     * @return [type]         [description]
     */
    public static function releaseOrder($order, $bool = false)
    {
    	$orderDetails = static::getOrderDetails($order->no);
        // 我们的服
        $templateId =  GoodsTemplate::where('game_id', $order->game_id)->where('service_id', 4)->value('id'); //模板id
        $serverTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)
                ->where('field_name', 'serve')
                ->value('id');
        $serverId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $serverTemplateWidgetId)
                ->where('field_name', 'serve')
                ->where('field_value', $orderDetails['serve'])
                ->value('id');
        // 我们的区
        $areaTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)
                ->where('field_name', 'region')
                ->value('id');
        $areaId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $areaTemplateWidgetId)
                ->where('field_name', 'region')
                ->where('field_value', $orderDetails['region'])
                ->value('id');
        // 第三方区
        $thirdAreaName = ThirdArea::where('game_id', $order->game_id)
                ->where('third_id', 2)
                ->where('area_id', $areaId)
                ->value('third_area_name') ?: '';
        // 第三方服
        $thirdServerName = ThirdServer::where('game_id', $order->game_id)
                ->where('third_id', 2)
                ->where('server_id', $serverId)
                ->value('third_server_name') ?: '';
        // 第三方游戏
        $thirdGameName = ThirdGame::where('game_id', $order->game_id)
                ->where('third_id', 2)
                ->value('third_game_name') ?: '';

    	$options = [
            'game_name'      => $thirdGameName,
            'areaname'       => $thirdAreaName,
            'servername'     => $thirdServerName,
            'ordertitle'     => $orderDetails['game_leveling_title'], // 代练标题
            'orderorgin'     => 0, // 0普通， 1私有， 2优质
            'unitprice'      => $order->amount, // 订单金额
            'safedeposit'    => $orderDetails['security_deposit'], // 安全保证金
            'efficiency'     => $orderDetails['efficiency_deposit'], // 效率保证金
            'requiretime'    => $orderDetails['efficiency_deposit'] ? 
            bcadd(bcmul($orderDetails['efficiency_deposit'], 24), $orderDetails['game_leveling_hour'])
            : $orderDetails['game_leveling_hour'], // 要求代练时间
            'accountvalue'   => $orderDetails['account'],
            'accountpwd'     => $orderDetails['password'],
            'rolename'       => $orderDetails['role'],
            'dlcontent'      => $orderDetails['game_leveling_requirements'], // 代练要求
            'dltype'         => '排位', // 排位，陪玩， 晋级， 定位, 其他...?
            // 'orginuserid' => '', // 发布私有订单使用?
            'otherdesc'      => '无', // 当前游戏说明?
            'price'          => $order->original_amount, // 备注金额? => 来源价格
            'title'          => '无', // 备注标题?
            'content'        => $orderDetails['cstomer_service_remark'], // 备注内容?
            // 'linktel'     => '', // 发单人联系电话?
            // 'qq'          => '', // 发单人联系qq？
            // 'saveuserbak' => '' // 值为save时只修改备注 ?
            'autologinphone' => $orderDetails['client_phone'], // 号主电话?
    	];

        if ($bool) {
            if (! $orderDetails['dailianmama_order_no']) {
                throw new DailianException('无第三方订单，修改失败!');
            }
            $options['orderid'] => $orderDetails['dailianmama_order_no'];
        }

    	$res = static::formDataRequest(config('dailianmama.url.releaseOrder'), $options);

        return static::returnErrorMessage($res);
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

    	$options = [
    		'orderid' => $orderDetails['dailianmama_order_no'], // 代练妈妈订单id
    	];

    	$res = static::normalRequest(config('dailianmama.url.upOrder'), $options);

        return static::returnErrorMessage($res);
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

    	$options = [
    		'orderid' => $orderDetails['dailianmama_order_no'],
    	];

    	$res = static::normalRequest(config('dailianmama.url.closeOrder'), $options);

        return static::returnErrorMessage($res);
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

        $options = [
            'orderid' => $orderDetails['dailianmama_order_no'],
        ];

    	$res = static::normalRequest(config('dailianmama.url.deleteOrder'), $options);

        return static::returnErrorMessage($res);
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

        $options = [
            'orderid' => $orderDetails['dailianmama_order_no'],
        ]; 

	    $res = static::normalRequest(config('dailianmama.url.orderinfo'), $options);

	    return static::returnErrorMessage($res);
    }

    /**
     * 请使用此接口来刷新未接手订单，此接口可刷新所有未接手状态的订单发布时间。注意：代练妈妈规定此接口在一定时间内只能调用一次
     * @param  [type] $order [description]
     * @param  [type] $bool  [description]
     * @return [type]        [description]
     */
    public static function refreshAllOrderTime($order = null, $bool = false)
    {
    	$options = [];
	    $res = static::normalRequest(config('dailianmama.url.refreshAllOrderTime'), $options);

	    return static::returnErrorMessage($res);
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
    	$options = [
    		'orderstatus' => '', // 订单状态
    	];
    	$res = static::normalRequest(config('dailianmama.url.getReleaseOrderStatusList'), $options);

	    return static::returnErrorMessage($res);
    }

    /**
     * 支付密码验证
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function checkTradePassword($order = null, $bool = false)
    {
    	$options = [
    		'tradePassword' => '', // 支付密码
    		'type' => md5(''), // 类型
    	];
    	$res = static::normalRequest(config('dailianmama.url.checkTradePassword'), $options);

	    return static::returnErrorMessage($res);
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

    	$options = [
            'orderid'          => $orderDetails['dailianmama_order_no'],
            'control'          => $operate, // 2002, 20010...
            // 'checkTradePwdRDM' => '', // 支付密码验证?
            // 'reason'           => '', // 原因：20006 申请撤销， 20004提交异常， 20007申请仲裁?
            // 'refundment'       => '', // 上家支付的代练费: 20006 申请撤销?
            // 'bail'             => '', // 赔偿打手的双金：20006 申请撤销 ?
            // 'requiretimehadd'  => '', // 增加的小时数 22001补时?
            // 'unitpriceadd'     => '', // 增加的金额数, 22002 补款?
            // 'accountpwd'       => '', // 密码， 22003修改密码?
    	];

        $consult = LevelingConsult::where('order_no', $order->no)->first();
        switch ($operate) {
            case 20006: // 申请撤销
                $options['reason'] = $consult->revoke_message;
                $options['refundment'] = $consult->amount;
                $options['bail'] = $consult->deposit;
                break;
            case 20007: // 申请仲裁
                $options['reason'] = $consult->complain_message;
                break;
            case 22001: // 补时
                $options['requiretimehadd'] = bcadd(bcmul($order->addDays, 24), $order->addHours);
                break;
            case 22002: // 补款
                $options['unitpriceadd'] = $order->addAmount; // 这里要问仁德
                break;
            case 22003: // 修改密码
                $options['accountpwd'] = $order->password;
                break;
        }
    	$res = static::normalRequest(config('dailianmama.url.operationOrder'), $options);

	    return static::returnErrorMessage($res);
    }

    /**
     * 用于获取订单中的留言，调用此接口系统将默认此订单已读
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function chatOldList($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

    	$options = [
	    	'orderid' => $orderDetails['dailianmama_order_no'],
	    	'beginid' => '', // 开始id？
    	];

    	$res = static::normalRequest(config('dailianmama.url.chatOldList'), $options);

	    return static::returnErrorMessage($res);
    }

    /**
     * 获取订单截图记录
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function getOrderPictureList($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

    	$options = [
    		'orderid' => $orderDetails['dailianmama_order_no'], 
    	];
    	$res = static::normalRequest(config('dailianmama.url.getOrderPictureList'), $options);

	    return static::returnErrorMessage($res);
    }

    /**
     * 发送订单留言
     * @param [type]  $order [description]
     * @param boolean $bool  [description]
     */
    public static function addChat($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

    	$options = [
    		'orderid' => $orderDetails['dailianmama_order_no'],
    		'content' => '', // 订单留言,最大100
    	];
    	$res = static::normalRequest(config('dailianmama.url.addChat'), $options);

	    return static::returnErrorMessage($res);
    }

    /**
     * 上传图片成功后使用此接口将截图记录保存到代练妈妈中。
     * 接口只用于保存订单截图，上传请通过接口获取阿里云临时凭证后使用阿里云SDK进行上传，
     * 上传后将上传成功的地址和订单号截图说明等参数通过此接口保存至代练妈妈系统。
     * (PS:也可以将截图上传到别的平台或网站，再使用保存截图功能保存到代练妈妈。但必须保证截图能查看和时效性。)
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function savePicture($order, $bool = false)
    {
        $orderDetails = static::getOrderDetails($order->no);

    	$options = [
    		'orderid' => $orderDetails['dailianmama_order_no'],
    		'imgurl' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'), // 截图地址
    		'description' => '截图说明', // 截图说明
    	];
    	$res = static::formDataRequest(config('dailianmama.url.savePicture'), $options);

	    return static::returnErrorMessage($res);  	
    }

    /**
     * 获取阿里云临时凭证,获取后一小时内可用
     * https://help.aliyun.com/document_detail/oss/user_guide/upload_object/thirdparty_upload.html
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function getTempUploadKey($order = null, $bool = false)
    {
    	$res = static::normalRequest(config('dailianmama.url.getTempUploadKey'), $options = []);

	    return static::returnErrorMessage($res);	
    }

    /**
     * [发单管理接口，查询发布时间在3个月内的订单]
     * @param  [type]  $order [description]
     * @param  boolean $bool  [description]
     * @return [type]         [description]
     */
    public static function releaseOrderManageList($order, $bool = false)
    {
    	$options = [
    		'orderstatus' => '', // 1未接单, 2已下架， 3代练中，4异常,6待验收，9撤销中，
    							//7,8已结算，5,10已撤销，iszhc已仲裁,qzcx客服强制撤销
    		'keyword' => '', // 关键词查询, 订单号，标题，接单人?
    		'sort' => '', // 排序条件， 1降序，2升序， 3总时间降序， 4总时间升序, 5区服降序，6区服升序
    						// 9，发布时间降序， 10发布时间升序, 13解散时间降序， 14， 结算时间升序
    						// 15, 发单账号降序, 16, 发单账号升序， 17接单人降序， 18接单人升序
    						// 19,接单时间降序， 20接单时间升序， 21颜色降序， 22颜色升序
    						// 23,结算时间升序， 24结算时间降序, 25,来源降序， 26来源升序
    						// 27状态降序， 28状态升序， 29申请验收时间升序， 30申请验收时间降序?
    		'currentNo' => '', // 要查询当前页数，默认查询第一页?
    		'pageSize' => '', // 每页条数， 为空默认10条?
    	];

    	$res = static::normalRequest(config('dailianmama.url.releaseOrderManageList'), $options);

	    return static::returnErrorMessage($res);
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

    	$options = [
	    	'orderid' => $orderDetails['dailianmama_order_no'],
    	];
    	$res = static::normalRequest(config('dailianmama.url.getOrderPrice'), $options);

	    return static::returnErrorMessage($res);
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
}