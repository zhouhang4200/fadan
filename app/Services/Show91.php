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
use App\Models\OrderRegionCorrespondence;
use App\Exceptions\RequestTimeoutException;

/**
 * 91接口类
 */
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
        try {
            myLog('91-request', ['url' => $url, '请求参数' => $options, '结果' => $result]);
        } catch (\UnexpectedValueException $exception) {
            info('写日志异常', ['url' => $url, '写日志异常', $exception->getMessage()]);
        } catch (\Exception $exception) {
            info('写日志异常', ['url' => $url, '写日志异常', $exception->getMessage()]);
        }
        curl_close($curl);
        return $result;
    }

    /**
     * 普通提交
     * @param $url
     * @param array $options
     * @param string $method
     * @return string
     * @throws CustomException
     * @throws RequestTimeoutException
     */
    public static function normalRequest($url, $options = [], $method = 'POST')
    {
        $params = [
            'account' => config('show91.account'),
            'sign' => config('show91.sign'),
        ];

        $options = array_merge($params, $options);

        try {
            $client = new Client(['timeout' => 2]);
            $response = $client->request($method, $url, [
                'query' => $options,
            ]);
            $result = $response->getBody()->getContents();
        } catch (\Exception $exception) {
            throw  new CustomException(0, '订单状态异常');
        }

        try {
            myLog('91-request', ['url' => $url, '请求参数' => $options, '结果' => $result]);
        } catch (\UnexpectedValueException $exception) {
            info('写日志异常', ['url' => $url, '写日志异常', $exception->getMessage()]);
        } catch (\Exception $exception) {
            info('写日志异常', ['url' => $url, '写日志异常', $exception->getMessage()]);
            throw  new RequestTimeoutException('91接口异常');
        }

        return $result;
    }

    /*
     * 获得状态正常的游戏
     * @return [json]      [result:XX, content:XX]  or [result:XX, reason:XX]
     */
    public static function getGames($options = [])
    {
    	$res = static::normalRequest(config('show91.url.getGames'), $options = []);

        return static::returnErrorMessage($res);
    }

    /**
     * 根据gameid获得游戏区
     * @param  [type] $gid [gid => 游戏id]
     * @return [type]      [description]
     */
    public static function getAreas($options)
    {
    	$res = static::normalRequest(config('show91.url.getAreas'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 根据areaid获得服务器
     * @param  [type] $aid [aid => 服务器id]
     * @return [type]      [description]
     */
    public static function getServer($options)
    {
    	$res = static::normalRequest(config('show91.url.getServer'), $options);

        return static::returnErrorMessage($res);
    }

    /**
     * 获取该游戏的所有代练类型
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    public static function getPlays($order)
    {
        $thirdGameDatas = OrderRegionCorrespondence::where('game_id', $order->game_id)
            ->where('third', 1)
            ->first();

        $options = ['gid' => $thirdGameDatas->third_game_id];

        $res = static::normalRequest(config('show91.url.getPlays'), $options);

        $res = json_decode($res, true);

        if (! $res || ($res && $res['result'])) {
            return '';
        }
        return $res['plays'];
    }

    /**
     * 发布订单
     * @param [type] $options [参数数组]
     */
    public static function addOrder($order, $bool = false)
    {
        $orderDetails = OrderDetail::where('order_no', $order->no)->pluck('field_value', 'field_name')->toArray();
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
                ->where('field_name', 'serve')
                ->where('parent_id', $areaId)
                ->where('field_value', $orderDetails['serve'])
                ->value('id');

        // 找第三方的区服信息
        $orderRegionCorrespondence = OrderRegionCorrespondence::where('third', 1)
            ->where('game_id', $order->game_id)
            ->where('area_id', $areaId)
            ->where('server_id', $serverId)
            ->first();

        if (! $orderRegionCorrespondence) {
            return ['status' => 0, 'message' => '91代练平台:91平台下没有此游戏！', $order->game_id, $areaId, $serverId];
        }

        // 找游戏类型
        $types = static::getPlays($order);
        $gameType = '';
        // 匹配当前的类型
        if ($types) {
            foreach ($types as $type) {
                if (trim($type['play_name']) == $orderDetails['game_leveling_type']) {
                    $gameType = $type['id'];
                }
            }
        }

        $options = [
            'orderType'            => 0, // 0代练订单, 1 求购订单
            'order.game_id'        => $orderRegionCorrespondence->third_game_id, 
            'order.game_area_id'   => $orderRegionCorrespondence->third_area_id,
            'order.game_server_id' => $orderRegionCorrespondence->third_server_id,
            'order.title'          => $orderDetails['game_leveling_title'] ?: '无',
            'order.price'          => $order->amount,
            'order.bond4safe'      => $orderDetails['security_deposit'] ?: 0,
            'order.bond4eff'       => $orderDetails['efficiency_deposit'] ?: 0,
            'order.timelimit_days' => $orderDetails['game_leveling_day'],
            'order.timelimit_hour' => $orderDetails['game_leveling_hour'],
            'order.account'        => $orderDetails['account'],// 游戏账号
            'order.account_pwd'    => $orderDetails['password'], //账号密码
            'order.role_name'      => $orderDetails['role'],//角色名字
            'order.order_pwd'      => '',//订单密码
            'order.current_info'   => $orderDetails['game_leveling_instructions'],
            'initPic1'             => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
            'initPic2'             => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
            'initPic3'             => new \CURLFile(public_path('frontend/images/123.png'), 'image/png'),
            'order.require_info'   => $orderDetails['game_leveling_requirements'] ?: 1, // 代练要求
            'order.remark'         => $orderDetails['customer_service_remark'] ?: '无',//订单备注
            'order.linkman'        => $order->creator_primary_user_id, // 联系人
//            'order.linkphone'      => $orderDetails['user_phone'],
            'order.linkphone'      => $orderDetails['client_phone'],
            'order.linkqq'         => $orderDetails['user_qq'],
            'order.sms_notice'     => 0, // 短信通知
            'order.sms_mobphone'   => '1', // 短信通知电话
            'micro'                => 0, // 验证码订单
            'haozhu'               => $orderDetails['client_phone'],
            'istop'                => 0,
            'forAuth'              => 0,
            'order.game_play_id'   => 1,
            'order.game_play_id'   => $gameType,
        ];
        myLog('order', [$order->no, $options]);
        // 默认是下单, 如果存在则为修改订单
        if ($bool) {
            if (! $orderDetails['show91_order_no']) {
                throw new DailianException('91代练平台:91平台订单号不存在，修改失败!');
            }
            $options['order.order_id'] = $orderDetails['show91_order_no'];
        }
        $res = static::formDataRequest(config('show91.url.addOrder'), $options);

        $res = json_decode($res, true);

        if (! $res) {
            return ['status' => 0, 'message' => '91代练平台:接口错误，请重试！'];
            // throw new DailianException('91代练平台:外部接口错误,请重试!');
        }

        if ($res && $res['result']) {
            return ['status' => 0, 'message' => '91代练平台:'.$res['reason']];
            // throw new DailianException($res['reason']);
        }
        return ['status' => 1, 'order_no' => $res['data']];
    }

    /**
     * 修改订单的游戏账号密码
     * @param  [type] $options [oid => 订单id， newAcc => 游戏账号, newAccPwd => 游戏密码]
     * @return [type]          [description]
     */
    public static function editOrderAccPwd($order, $bool = false)
    {
        $orderDetails = OrderDetail::where('order_no', $order->no)
            ->pluck('field_value', 'field_name')
            ->toArray();

        $options = [
            'oid' => $orderDetails['show91_order_no'],
            'newAcc' => $orderDetails['account'],
            'newAccPwd' => $orderDetails['password'],
        ];

    	$res = static::normalRequest(config('show91.url.editOrderAccPwd'), $options);

        return static::returnErrorMessage($res);
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
     * @param array $options
     * @return array $messageArr
     * @throws CustomException
     */
    public static function messageList($options = [])
    {
    	$res = static::normalRequest(config('show91.url.messageList'), $options);
        $res = json_decode($res, true);

        if ($res['result'] != 0) {
            throw new CustomException($res['reason'], $res['result']);
        }

        $sortField = [];
        $messageArr = [];
        foreach ($res['data'] as $item) {
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
        $orderDetails = OrderDetail::where('order_no', $order->no)
            ->pluck('field_value', 'field_name')
            ->toArray();

    	$options = [
            'oid' => $orderDetails['show91_order_no'],
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
        $orderDetails = OrderDetail::where('order_no', $order->no)
            ->pluck('field_value', 'field_name')
            ->toArray();

        $options = [
            'oid' => $orderDetails['show91_order_no'],
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
        $orderDetails = OrderDetail::where('order_no', $order->no)
            ->pluck('field_value', 'field_name')
            ->toArray();

        $options = [
            'oid' => $orderDetails['show91_order_no'], 
            'day' => $orderDetails['game_leveling_day'],
            'hour' => $orderDetails['game_leveling_hour'],
        ];
    	$res = static::normalRequest(config('show91.url.addLimitTime2'), $options);

        return static::returnErrorMessage($res);
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
            throw new DailianException('91代练平台:外部接口错误,请重试!');
        }

        if ($res && $res['result']) {
            throw new DailianException('91代练平台:'.$res['reason']);
        }
        return $res;
    }
}
