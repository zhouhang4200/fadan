<?php

use App\Models\City;
use GuzzleHttp\Client;
use App\Models\UserSetting;
use App\Services\RedisConnect;
use Illuminate\Support\Facades\Redis;
use App\Models\UserReceivingUserControl;
use App\Models\UserReceivingCategoryControl;

if (!function_exists('loginDetail')) {

    /**
     * @param $ip
     * @return array
     */
    function loginDetail($ip)
    {
    	$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $ip;

    	$client = new Client();

    	$res = $client->request('GET', $url);
    	$res = $res->getBody();
    	$res = json_decode($res);

        if (isset($res->ret) && $res->ret == 1) {

            $city = City::where('name', $res->city)->first();

            return [
                'country'  => $res->country,
                'province' => $res->province,
                'city'     => empty($res->city) ? $res->province : $res->city,
                'city_id'  => $city ? $city->id : 0,
                'ip'       => ip2long($ip),
            ];
        }
        return [
            'country'  => '',
            'province' => '',
            'city'     => '',
            'city_id'  => 0,
            'ip'       => ip2long($ip),
        ];
    }
}

if (!function_exists('jsonMessages')) {

    function jsonMessages($code, $message)
    {
        $data = ['code' => $code, 'message' => $message];

        return json_encode($data);
    }
}

if (!function_exists('receiving')) {
    /**
     * 用户接单
     * @param $userId
     * @param $orderNo
     */
    function receiving($userId, $orderNo)
    {
        // 将当前用户ID，写入订单抢单队列中
        $redis = RedisConnect::order();
        $redis->lpush(config('redis.order.receiving') . $orderNo, $userId);
    }
}

if (!function_exists('receivingRecord')) {
    /**
     * 用户接单记录 用主账号ID记录
     * 用户抢单后写入一条记录
     * @param $userId
     * @param $orderNo
     * @return int
     */
    function receivingRecord($userId, $orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->setex(config('redis.order.receivingRecord') . $orderNo . $userId  , config('redis.timeout'), $userId);
    }
}

if (!function_exists('receivingUserLen')) {
    /**
     * 获取接单用户队列长度
     * @param $orderNo
     */
    function receivingUserLen($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->lLen(config('redis.order.receiving') . $orderNo);
    }
}
if (!function_exists('receivingUser')) {
    /**
     * 获取接单用户队列所有用户ID
     * @param $orderNo
     */
    function receivingUser($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->lrange(config('redis.order.receiving') .  (string) $orderNo, 0, receivingUserLen($orderNo) );
    }
}
if (!function_exists('receivingUserDel')) {
    /**
     * 删除接单用户队列所有用户ID
     * @param $orderNo
     */
    function receivingUserDel($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->del(config('redis.order.receiving') . $orderNo);
    }
}

if (!function_exists('receivingRecordExist')) {
    /**
     * 用户接单记录是否存在 用主账号ID记录
     * @param $userId
     * @param $orderNo
     * @return int
     */
    function receivingRecordExist($userId, $orderNo)
    {
        $redis = RedisConnect::order();
        return  $redis->get(config('redis.order.receivingRecord') . $orderNo . $userId);
    }
}

if (!function_exists('receivingRecordDelete')) {
    /**
     * 删除用户接单记录
     * @param $userId
     * @param $orderNo
     * @return int
     */
    function receivingRecordDelete($userId, $orderNo)
    {
        $redis = RedisConnect::order();
        return  $redis->del(config('redis.order.receivingRecord') . $orderNo . $userId);
    }
}

if (!function_exists('waitReceivingGet')) {
    /**
     * 获取 待接单的订单
     */
    function waitReceivingGet()
    {
        $redis = RedisConnect::order();
        return $redis->hgetall(config('redis.order.waitReceiving'));
    }
}

if (!function_exists('waitReceivingDel')) {
    /**
     * 删除 待接单的订单
     * @param $orderNo
     * @return mixed
     */
    function waitReceivingDel($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->hdel(config('redis.order.waitReceiving'), $orderNo);
    }
}

if (!function_exists('waitReceivingAdd')) {

    /**
     * 添加 待接单的订单
     * @param integer $orderNo 订单号
     * @param string $receivingDate 可接单时间
     * @param string $createdDate 订单创建时间
     * @param string $wangWang 关联的旺旺
     * @return mixed
     */
    function waitReceivingAdd($orderNo, $receivingDate, $createdDate, $wangWang = '')
    {
        $redis = RedisConnect::order();

        return $redis->hset(config('redis.order.waitReceiving'), $orderNo, json_encode([
            'receiving_date' => $receivingDate,
            'created_date' => $createdDate,
            'wang_wang' => $wangWang,
        ]));
    }
}

if (!function_exists('waitReceivingQuantity')) {
    /**
     * 获取订单集市 订单数
     * @return mixed
     */
    function marketOrderQuantity()
    {
        $redis = RedisConnect::order();
        return $redis->get(config('redis.order.waitReceivingQuantity'));
    }
}

if (!function_exists('waitReceivingQuantityAdd')) {
    /**
     * 获取订单集市 订单数 加1
     * @return mixed
     */
    function waitReceivingQuantityAdd()
    {
        $redis = RedisConnect::order();
        return $redis->incr(config('redis.order.waitReceivingQuantity'));
    }
}

if (!function_exists('waitReceivingQuantitySub')) {
    /**
     * 获取订单集市 订单数 减1
     * @return mixed
     */
    function waitReceivingQuantitySub()
    {
        if (marketOrderQuantity() > 0) {
            $redis = RedisConnect::order();
            return $redis->decr(config('redis.order.waitReceivingQuantity'));
        }
    }
}

if (!function_exists('waitHandleQuantity')) {
    /**
     * 获取急需处理订单数
     * @param $userId
     * @return mixed
     */
    function waitHandleQuantity($userId)
    {
        $redis = RedisConnect::order();
        return $redis->get(config('redis.order.waitHandleQuantity') . $userId);
    }
}

if (!function_exists('waitHandleQuantityAdd')) {
    /**
     * 获取急需处理订单数 订单数 加1
     * @param $userId
     * @return mixed
     */
    function waitHandleQuantityAdd($userId)
    {
        $redis = RedisConnect::order();
        return $redis->incr(config('redis.order.waitHandleQuantity') . $userId);
    }
}

if (!function_exists('waitReceivingQuantityClear')) {

    /**
     * 获取急需处理订单数 订单数归0
     * @param $userId
     * @return mixed
     */
    function waitHandleQuantityClear($userId)
    {
        $redis = RedisConnect::order();
        return $redis->set(config('redis.order.waitHandleQuantity') . $userId, 0);
    }
}

if (!function_exists('socketServer')) {
    /**
     * socketServer
     * @return mixed
     */
    function socketServer()
    {
        return env('SOCKET_SERVER');
    }
}

if (!function_exists('refreshUserSetting')) {
    /**
     * 刷新用户的设置缓存
     * @return mixed
     */
    function refreshUserSetting()
    {
        return Cache::forget(config('redis.user.setting') . Auth::user()->getPrimaryUserId());
    }
}
if (!function_exists('getClientIp')) {
    /**
     * 刷新用户的设置缓存
     * @return mixed
     */
    function getClientIp()
    {
        $ip = '';
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ipArr = explode(',', $ip);
        return $ipArr[0];
    }
}

if (!function_exists('generateOrderNo')) {
    /**
     * 生成订单号
     * @return string
     */
    function generateOrderNo()
    {
        // 14位长度当前的时间 20150709105750
        $orderDate = date('YmdHis');

        // 今日订单数量
        $redis = RedisConnect::order();
        $orderQuantity = $redis->incr(config('redis.order.quantity') . date('Ymd'));
        return $orderDate . str_pad($orderQuantity, 8, 0, STR_PAD_LEFT);
    }
}

if (!function_exists('whoCanReceiveOrder')) {

    /**
     * 用户黑白名单，谁能接单
     * @param $sendUserId
     * @param $receiveUserId
     * @param string $serviceId
     * @param string $gameId
     * @return bool
     */
    function whoCanReceiveOrder($sendUserId, $receiveUserId, $serviceId = '', $gameId = '')
    {
        // 0,未设置， 1白名单， 2黑名单
        $type = UserSetting::where('user_id', $sendUserId)->where('option', 'receiving_control')->value('value');

        //  如果用户设置为不开启，或没有设置则直接返回true
        if ($type == 0 || !$type) {
            return true;
        } else if ($type == 1) { // 开启了白名单

            // 获取 发单用户的用户白名单
            $userWhite = UserReceivingUserControl::where('user_id', $sendUserId)
                ->where('type', 1)
                ->pluck('other_user_id')
                ->toArray();

            // 获取 发单用户的商品白名单
            $goodsWhite = UserReceivingCategoryControl::where('service_id', $serviceId)
                        ->where('user_id', $sendUserId)
                        ->where('game_id', $gameId)
                        ->where('type', 1)
                        ->pluck('other_user_id')
                        ->toArray();
            // 如果开启了白名单，但商品与用户白名单中都没有数据则所有人可以接单
            if (!$userWhite && !$goodsWhite) {
                return true;
            }
            // 如果在白名单中，并且商品没有有单独设置白名单 可以接单
            if (in_array($receiveUserId, $userWhite) && !$goodsWhite) {
                return true;
            }
            // 如果在商品白名单中，则可以接单
            if ($goodsWhite && in_array($receiveUserId, $goodsWhite)) {
                return true;
            }
            // 如果有商品白名单，但用户并没有在商品白名单也则不可以接单
            if ($goodsWhite && !in_array($receiveUserId, $goodsWhite)) {
                return false;
            }
            return false;
        } else if ($type == 2) {

            // 获取 发单用户的用户黑名单
            $userBlack = UserReceivingUserControl::where('user_id', $sendUserId)
                ->where('type', 2)
                ->pluck('other_user_id')
                ->toArray();

            // 获取发单商家的商品黑名单
            $goodsBlack = UserReceivingCategoryControl::where('service_id', $serviceId)
                ->where('game_id', $gameId)
                ->where('type', 2)
                ->pluck('other_user_id')
                ->toArray();

            // 如果黑名单中没有数据就所有人可接单
            if (!$userBlack && !$goodsBlack) {
                return true;
            }

            // 如果当前用户在黑名单中 不可接单
            if (in_array($receiveUserId, $userBlack)) {
                return false;
            }

            if (in_array($receiveUserId, $goodsBlack)) {
                return false;
            }
            return true;
        }
    }
}


if (!function_exists('wangWangToUserId')) {
    /**
     * 记录某个旺旺号的订单分配到的了那商户
     * @param $wangWang
     * @param $userId
     */
    function wangWangToUserId($wangWang, $userId)
    {
        $redis = RedisConnect::order();
        $redis->setex(config('redis.order.wangWangToUserId') . $wangWang, config('redis.order.wangWangToUserIdRecordTime'), $userId);
    }
}
if (!function_exists('wangWangGetUserId')) {
    /**
     * 获取某个旺旺号关联的商户D
     * @param $wangWang
     */
    function wangWangGetUserId($wangWang)
    {
        $redis = RedisConnect::order();
        return $redis->get(config('redis.order.wangWangToUserId') . $wangWang);
    }
}
if (!function_exists('wangWangDeleteUserId')) {
    /**
     * 删除某个旺旺号关联的商户D
     * @param $wangWang
     */
    function wangWangDeleteUserId($wangWang)
    {
        $redis = RedisConnect::order();
        $redis->del(config('redis.order.wangWangToUserId') . $wangWang);
    }
}
if (!function_exists('orderAssignSwitchGet')) {
    /**
     * 获取订单分配开关状态
     */
    function orderAssignSwitchGet()
    {
        $redis = RedisConnect::order();
        return $redis->get(config('redis.order.orderAssignSwitch')) ?? 1;
    }
}
if (!function_exists('orderAssignSwitchSet')) {

    /**
     * 设置订单分配开关状态
     * @param $status
     */
    function orderAssignSwitchSet($status)
    {
        $redis = RedisConnect::order();
        return $redis->set(config('redis.order.orderAssignSwitch'), $status);
    }
}

if (!function_exists('waitConfirm')) {

    /**
     * 获取所有 待确认订单
     */
    function waitConfirm()
    {
        $redis = RedisConnect::order();
        return $redis->hgetall(config('redis.order.waitConfirm'));
    }
}


if (!function_exists('waitConfirmDel')) {
    /**
     * 删除 待确认收货订单
     * @param $orderNo
     * @return mixed
     */
    function waitConfirmDel($orderNo)
    {
        $redis = RedisConnect::order();
        return $redis->hdel(config('redis.order.waitConfirm'), $orderNo);
    }
}

if (!function_exists('waitConfirmAdd')) {

    /**
     * 添加 待确认收货的订单
     * @param integer $orderNo 订单号
     * @param integer $sendDate 发货时间
     * @return mixed
     */
    function waitConfirmAdd($orderNo, $sendDate)
    {
        $redis = RedisConnect::order();
        return $redis->hset(config('redis.order.waitConfirm'), $orderNo, $sendDate);
    }
}

if (!function_exists('assignStatusGet')) {

    /**
     * 获取 是否可以开始执行分单
     * @return mixed
     */
    function assignStatusGet()
    {
        $redis = RedisConnect::order();
        return $redis->rpop(config('redis.order.assignStatus'));
    }
}

if (!function_exists('assignStatusAdd')) {

    /**
     * 当一次分单执行完则向队列写入一条数据
     */
    function assignStatusAdd()
    {
        $redis = RedisConnect::order();
        return $redis->lpush(config('redis.order.assignStatus'), 1);
    }
}

if (!function_exists('isBase64')) {

    function isBase64($str){
        if(@preg_match('/^[0-9]*$/',$str) || @preg_match('/^[a-zA-Z]*$/',$str)){
            return false;
        } elseif(base64_decode($str) && base64_decode($str) != ''){
            return true;
        }
        return false;
    }
}

if (!function_exists('sec2Time')) {
    function sec2Time($seconds, $showSeconds = false)
    {
        if (is_numeric($seconds)) {
            $value = array(
              'years' => 0, 'days' => 0, 'hours' => 0,
              'minutes' => 0, 'seconds' => 0,
            );
            if($seconds >= 31556926){
              $value['years'] = floor($seconds/31556926);
              $seconds = ($seconds%31556926);
            }
            if($seconds >= 86400){
              $value['days'] = floor($seconds/86400);
              $seconds = ($seconds%86400);
            }
            if($seconds >= 3600){
              $value['hours'] = floor($seconds/3600);
              $seconds = ($seconds%3600);
            }
            if($seconds >= 60){
              $value['minutes'] = floor($seconds/60);
              $seconds = ($seconds%60);
            }
            $value['seconds'] = floor($seconds);

            $t = '';
            if ($value['years'] > 0) {
                $t .= $value['years'] .'年 ';
            }
            $t .= $value['days'] . '天 ' . $value['hours'] . '小时 ' . $value['minutes'] . '分 ';

            if ($showSeconds) {
              $t .= $value['seconds'] . '秒';
            }
            Return $t;
        } else {
            return (bool) FALSE;
        }
    }
}

if (!function_exists('delRedisCompleteOrders')) {

    function delRedisCompleteOrders($no) {
        $orders = Redis::hGetAll('complete_orders');

        if ($orders) {
            foreach ($orders as $redisOrderNo => $time) {
                if ($no == $redisOrderNo) {
                    Redis::hDel('complete_orders', $redisOrderNo);
                    break;
                }
            }
        }
    }
}