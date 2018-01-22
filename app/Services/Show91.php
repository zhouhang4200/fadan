<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\ThirdGame;
use App\Models\ThirdArea;
use App\Models\ThirdServer;
use App\Exceptions\CustomException;
use App\Exceptions\DailianException;
use App\Models\GoodsTemplate;
use App\Models\GoodsTemplateWidget;
use App\Models\GoodsTemplateWidgetValue;

class Show91
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
            'account' => config('show91.account'),
            'sign' => config('show91.sign'),
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
            'account' => config('show91.account'),
            'sign' => config('show91.sign'),
        ];

        $options = array_merge($params, $options);

        $client = new Client;
        $response = $client->request($method, $url, [
            'query' => $options,
        ]);
        return $response->getBody()->getContents();
    }

    /*
     * 获得状态正常的游戏
     * @return [json]      [result:XX, content:XX]  or [result:XX, reason:XX]
     */
    public static function getGames($options = [])
    {
    	return static::normalRequest(config('show91.url.getGames'), $options = []);
    }

    /**
     * 根据gameid获得游戏区
     * @param  [type] $gid [gid => 游戏id]
     * @return [type]      [description]
     */
    public static function getAreas($options)
    {
    	return static::normalRequest(config('show91.url.getAreas'), $options);
    }

    /**
     * 根据areaid获得服务器
     * @param  [type] $aid [aid => 服务器id]
     * @return [type]      [description]
     */
    public static function getServer($options)
    {
    	return static::normalRequest(config('show91.url.getServer'), $options);
    }

    /**
     * 发布订单
     * @param [type] $options [参数数组]
     */
    public static function addOrder($order, $bool = 0)
    {
        // 我们的服
        $templateId =  GoodsTemplate::where('game_id', $order->game_id)->where('service_id', 4)->value('id'); //模板id
        $serverTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)->where('field_name', 'serve')->value('id');
        $serverId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $serverTemplateWidgetId)
                ->where('field_name', 'serve')
                ->where('field_value', $order->detail()->where('field_name', 'serve')->value('field_value'))
                ->value('id');
        // 我们的区
        $areaTemplateWidgetId = GoodsTemplateWidget::where('goods_template_id', $templateId)->where('field_name', 'region')->value('id');
        $areaId = GoodsTemplateWidgetValue::where('goods_template_widget_id', $areaTemplateWidgetId)
                ->where('field_name', 'region')
                ->where('field_value', $order->detail()->where('field_name', 'region')->value('field_value'))
                ->value('id');

        $options = [
            'orderType' => 0,
            'order.game_id' => ThirdGame::where('game_id', $order->game_id)->where('third_id', 1)->value('third_game_id') ?: '', // 王者荣耀
            'order.game_area_id' => ThirdArea::where('game_id', $order->game_id)
                                    ->where('third_id', 1)
                                    ->where('area_id', $areaId)
                                    ->value('third_area_id') ?: '', // 安卓区
            'order.game_server_id' => ThirdServer::where('game_id', $order->game_id)
                                    ->where('third_id', 1)
                                    ->where('server_id', $serverId)
                                    ->value('third_server_id') ?: '', // QQ服
            'order.title' => $order->detail()->where('field_name', 'game_leveling_title')->value('field_value') ?: '无',
            'order.price' => $order->amount,
            'order.bond4safe' => $order->detail()->where('field_name', 'security_deposit')->value('field_value') ?: 0,
            'order.bond4eff' => $order->detail()->where('field_name', 'efficiency_deposit')->value('field_value') ?: 0,
            'order.timelimit_days' => $order->detail()->where('field_name', 'game_leveling_day')->value('field_value'),
            'order.timelimit_hour' => $order->detail()->where('field_name', 'game_leveling_hour')->value('field_value'),
            'order.account' => $order->detail()->where('field_name', 'account')->value('field_value'),// 游戏账号
            'order.account_pwd' => $order->detail()->where('field_name', 'password')->value('field_value'), //账号密码
            'order.role_name' => $order->detail()->where('field_name', 'role')->value('field_value'),//角色名字
            'order.order_pwd' => '',//订单密码
            'order.current_info' => $order->detail()->where('field_name', 'game_leveling_instructions')->value('field_value'),
            'initPic1' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
            'initPic2' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
            'initPic3' => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
            'order.require_info' => $order->detail()->where('field_name', 'game_leveling_requirements')->value('field_value') ?: 1,// 代练要求
            'order.remark' => $order->detail()->where('field_name', 'cstomer_service_remark')->value('field_value') ?: '无',//订单备注
            'order.linkman' => $order->creator_primary_user_id, // 联系人
            'order.linkphone' => $order->detail()->where('field_name', 'user_phone')->value('field_value'),
            'order.linkqq' => $order->detail()->where('field_name', 'user_qq')->value('field_value'),
            'order.sms_notice' => 0, // 短信通知
            'order.sms_mobphone' => '1', // 短信通知电话
            'micro' => 0, // 验证码订单
            'haozhu' => $order->detail()->where('field_name', 'client_phone')->value('field_value'),
            'istop' => 0,
            'forAuth' => 0,
            'order.game_play_id' => 1,
        ];
        // 默认是下单, 如果存在则为修改订单
        if ($bool) {
            $options['order.order_id'] = $order->detail()->where('field_name', 'third_order_no')->value('field_value');
            mylog('options', '修改订单：'.$options);
        }

        mylog('options', '下单：'.$options);

    	$res = static::formDataRequest(config('show91.url.addOrder'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 修改订单的游戏账号密码
     * @param  [type] $options [oid => 订单id， newAcc => 游戏账号, newAccPwd => 游戏密码]
     * @return [type]          [description]
     */
    public static function editOrderAccPwd($options = [])
    {
    	return static::normalRequest(config('show91.url.editOrderAccPwd'), $options);
    }

    /**
     * 锁定游戏帐号密码
     * @param  [type] $options ['oid' => 订单id]
     * @return [type]          [description]
     */
    public static function changeOrderBlock($options = [])
    {
        $res = static::normalRequest(config('show91.url.changeOrderBlock'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 获得订单状态接口
     * @return [type]          [description]
     */
    public static function orderStatus()
    {
    	return static::normalRequest(config('show91.url.orderStatus'));
    }

    /**
     * 获得订单详情接口
     * @param  [type] $options ['oid' => 订单id]
     * @return [type]          [description]
     */
    public static function orderDetail($options = [])
    {
    	$res = static::normalRequest(config('show91.url.orderDetail'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 协商结算，退单
     * @return [type] [description]
     */
    public static function cancelOrder()
    {
    	return static::normalRequest(config('show91.url.cancelOrder'));
    }

    /**
     * 提交申诉
     * @param  [type] $options [oid => 订单id, appeal.title => 提交的问题, appeal.content => 详细描述及赔偿要求, pic1~pic3 => 截图]
     * @return [type]          [description]
     */
    public static function addappeal($options = [])
    {
        $res = static::formDataRequest(config('show91.url.addappeal'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 查看所有申诉的订单
     * @return [type] [description]
     */
    public static function seeappeal()
    {
    	return static::normalRequest(config('show91.url.seeappeal'));
    }

    /**
     * 协商留言
     * @param [type] $options [oid => 订单id， mess => 订单留言]
     */
    public static function addMess($options = [])
    {
    	$res = static::normalRequest(config('show91.url.addMess'), $options);
        $res = json_decode($res);

        if ($res->result != 0) {
            throw new CustomException($res->reason, $res->result);
        }

        return $res->data ?? [];
    }

    /**
     * 提交协商请求
     * @param [type] $options [oid => 订单id， selfCancel.pay_price => 需要玩家支付的代练费，double格式，请不要超过订单费用,
     *                        selfCancel.pay_bond => 需要工作室赔偿的保证金，double格式, selfCancel.content => 协商原因]
     */
    public static function addCancelOrder($options = [])
    {
        $res = static::normalRequest(config('show91.url.addCancelOrder'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 确认协商接口
     * @param  [type] $options [oid => 订单id，v => 同意1/不同意2, p => 支付密码]
     * @return [type]          [description]
     */
    public static function confirmSc($options = [])
    {
        $res = static::normalRequest(config('show91.url.confirmSc'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 撤销协商接口
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function cancelSc($options = [])
    {
        $res = static::normalRequest(config('show91.url.cancelSc'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 撤销申诉
     * @param  [type] $options [aid => 申訴id]
     * @return [type]          [description]
     */
    public static function cancelAppeal($options = [])
    {
    	$res = static::normalRequest(config('show91.url.cancelAppeal'), $options);

        return static::returnErrorMessage($res);
    }

     /**
     * 查看订单截图
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function topic($options = [])
    {
    	$res = static::normalRequest(config('show91.url.topic'), $options);
        $res = json_decode($res);

        if ($res->result != 0) {
            throw new CustomException($res->reason, $res->result);
        }

        return $res->data ?? [];
    }

    /**
     * 上传截图
     * @param  [type] $options [oid => 訂單id， file1 => 文件上传表单名称]
     * @return [type]          [description]
     */
    public static function addpic($options = [])
    {
    	$res = static::formDataRequest(config('show91.url.addpic'), $options);
        $res = json_decode($res);

        if ($res->result != 0) {
            throw new CustomException($res->reason, $res->result);
        }

        return $res->data ?? ''; // 正常情况下，data是文件名
    }

    /**
     * 对已发布的订单发起主动撤单，返回资金
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function chedan($options = [])
    {
    	$res = static::normalRequest(config('show91.url.chedan'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 订单确认验收结算
     * @param  [type] $options [oid => 订单id，p => 支付密码]
     * @return [type]          [description]
     */
    public static function accept($options = [])
    {
        $res = static::normalRequest(config('show91.url.accept'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 获取订单留言
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function messageList($options = [])
    {
    	$res = static::normalRequest(config('show91.url.messageList'), $options);
        $res = json_decode($res);

        if ($res->result != 0) {
            throw new CustomException($res->reason, $res->result);
        }

        return $res->data ?? [];
    }

    /**
     * 添加申诉证据
     * @param  [type] $options [appealEvi.aid => 申诉表id, appealEvi.content => 留言长度, pic1 => 上传证据图片，可为空]
     * @return [type]          [description]
     */
    public static function addevidence($options = [])
    {
    	return static::normalRequest(config('show91.url.addevidence'), $options);
    }

    /**
     * 订单补款
     * @param [type] $options [oid => 订单id， appwd => 订单密码, cash => 补款金额]
     */
    public static function addPrice($order, $bool = false)
    {
    	$options = [
            'oid' => $order->detail()->where('field_name', 'third_order_no')->value('field_value'),
            'appwd' => config('show91.password'),
            'cash' => $order->addAmount,
        ];
        $res = static::normalRequest(config('show91.url.addPrice'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 增加订单代练时间
     * @param [type] $options [oid => 订单id, orderAddTime.days => 增加天数, orderAddTime.hours => 增加小时数,
     *                        orderAddTime.msg => 留言长度可为空]
     */
    public static function addLimitTime($order, $bool = 0)
    {
        $options = [
            'oid' => $order->detail()->where('field_name', 'third_order_no')->value('field_value'),
            'orderAddTime.days' => $order->addDays,
            'orderAddTime.hours' => $order->addHours,
            'orderAddTime.msg' => '',
        ];

        $res = static::normalRequest(config('show91.url.addLimitTime'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 确认增加代练时间
     * @param  [type] $options [oid => 订单id, v => 处理结果1同意，2不同意, reply => 回复长度255，可控]
     * @return [type]          [description]
     */
    public static function confirmAt($options = [])
    {
    	return static::normalRequest(config('show91.url.confirmAt'), $options);
    }

    /**
     * 订单上下架
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function grounding($options = [])
    {
        $res = static::normalRequest(config('show91.url.grounding'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 增加代练时间，商家用
     * @param [type] $options [day => 天, hour => 小时]
     */
    public static function addLimitTime2($order, $bool = 0)
    {
        $options = [
            'oid' => $order->detail()->where('field_name', 'third_order_no')->value('field_value'),
            'day' => $order->detail()->where('field_name', 'game_leveling_day')->value('field_value'),
            'hour' => $order->detail()->where('field_name', 'game_leveling_hour')->value('field_value'),
        ];
    	$res = static::normalRequest(config('show91.url.addLimitTime2'), $options);

        return static::returnErrorMessage($res);
    }

    public static function returnErrorMessage($res = null)
    {
        $res = json_decode($res, true);

        if (! $res) {
            throw new DailianException('外部接口错误,请重试!');
        }

        if ($res && $res['result']) {
            throw new DailianException($res['reason']);
        }
        return $res;
    }
}
