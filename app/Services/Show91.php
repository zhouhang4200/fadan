<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Exceptions\CustomException;

class Show91
{
    /**
     * 发送请求
     * @param  [type] $url     [地址]
     * @param  [type] $options [参数数组]
     * @param  string $method  [请求方式]
     * @return [type]          [json数据]
     */
    public static function getResult($url, $options = [], $method = 'POST')
    {
        $params = [
            'account' => config('show91.account'),
            'sign' => config('show91.sign'),
        ];

        $options = array_merge($params, $options);

        if (in_array($url, ['http://www.show91.com/oauth/addOrder', 'http://www.show91.com/oauth/addappeal'])) {
            $header[] = "Content-type: multipart/form-data";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options);
            $result = curl_exec($curl);
            curl_close($curl);
            return $result;
        } elseif (in_array($url, ['http://www.show91.com/oauth/addappeal'])) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"sign\"\r\n\r\n89abb1dfef56cdf21c315b3bc3670c5d\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"oid\"\r\n\r\nORD180113103534538973\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"appeal.title\"\r\n\r\n1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"appeal.content\"\r\n\r\n1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"account\"\r\n\r\nEFAE2BC69B8D4E16A3649992F031BDDB\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pic1\"; filename=\"C:\\Users\\Administrator\\Desktop\\aa.jpg\"\r\nContent-Type: image/png\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pic2\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pic3\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
                "postman-token: ac855087-11f8-7534-c563-ee79654de0de"
              ),
            ));
            $err = curl_error($curl);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } else {
            $client = new Client;
            $response = $client->request($method, $url, [
                'query' => $options,
            ]);
            return $response->getBody();
        }
    }

    /**
     * 获得状态正常的游戏
     * @return [json]      [result:XX, content:XX]  or [result:XX, reason:XX]
     */
    public static function getGames($options = [])
    {
    	return static::getResult(config('show91.url.getGames'), $options = []);
    }

    /**
     * 根据gameid获得游戏区
     * @param  [type] $gid [gid => 游戏id]
     * @return [type]      [description]
     */
    public static function getAreas($options)
    {
    	return static::getResult(config('show91.url.getAreas'), $options);
    }

    /**
     * 根据areaid获得服务器
     * @param  [type] $aid [aid => 服务器id]
     * @return [type]      [description]
     */
    public static function getServer($options)
    {
    	return static::getResult(config('show91.url.getServer'), $options);
    }

    /**
     * 发布订单
     * @param [type] $options [参数数组]
     */
    public static function addOrder($options = [])
    {
    	return static::getResult(config('show91.url.addOrder'), $options);
    }

    /**
     * 修改订单的游戏账号密码
     * @param  [type] $options [oid => 订单id， newAcc => 游戏账号, newAccPwd => 游戏密码]
     * @return [type]          [description]
     */
    public static function editOrderAccPwd($options = [])
    {
    	return static::getResult(config('show91.url.editOrderAccPwd'), $options);
    }

    /**
     * 锁定游戏帐号密码
     * @param  [type] $options ['oid' => 订单id]
     * @return [type]          [description]
     */
    public static function changeOrderBlock($options = [])
    {
    	return static::getResult(config('show91.url.changeOrderBlock'), $options);
    }

    /**
     * 获得订单状态接口
     * @return [type]          [description]
     */
    public static function orderStatus()
    {
    	return static::getResult(config('show91.url.orderStatus'));
    }

    /**
     * 获得订单详情接口
     * @param  [type] $options ['oid' => 订单id]
     * @return [type]          [description]
     */
    public static function orderDetail($options = [])
    {
    	return static::getResult(config('show91.url.orderDetail'), $options);
    }

    /**
     * 协商结算，退单
     * @return [type] [description]
     */
    public static function cancelOrder()
    {
    	return static::getResult(config('show91.url.cancelOrder'));
    }

    /**
     * 提交申诉
     * @param  [type] $options [oid => 订单id, appeal.title => 提交的问题, appeal.content => 详细描述及赔偿要求, pic1~pic3 => 截图]
     * @return [type]          [description]
     */
    public static function addappeal($options = [])
    {
    	return static::getResult(config('show91.url.addappeal'), $options);
    }

    /**
     * 查看所有申诉的订单
     * @return [type] [description]
     */
    public static function seeappeal()
    {
    	return static::getResult(config('show91.url.seeappeal'));
    }

    /**
     * 协商留言
     * @param [type] $options [oid => 订单id， mess => 订单留言]
     */
    public static function addMess($options = [])
    {
    	$res = static::getResult(config('show91.url.addMess'), $options);
        $res = json_decode($res->getContents());

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
    	return static::getResult(config('show91.url.addCancelOrder'), $options);
    }

    /**
     * 确认协商接口
     * @param  [type] $options [oid => 订单id，v => 同意1/不同意2, p => 支付密码]
     * @return [type]          [description]
     */
    public static function confirmSc($options = [])
    {
    	return static::getResult(config('show91.url.confirmSc'), $options);
    }

    /**
     * 撤销协商接口
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function cancelSc($options = [])
    {
    	return static::getResult(config('show91.url.cancelSc'), $options);
    }

    /**
     * 撤销申诉
     * @param  [type] $options [aid => 申訴id]
     * @return [type]          [description]
     */
    public static function cancelAppeal($options = [])
    {
    	return static::getResult(config('show91.url.cancelAppeal'), $options);
    }

     /**
     * 查看订单截图
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function topic($options = [])
    {
    	return static::getResult(config('show91.url.topic'), $options);
    }

    /**
     * 上传截图
     * @param  [type] $options [oid => 訂單id， file1 => 文件上传表单名称]
     * @return [type]          [description]
     */
    public static function addpic($options = [])
    {
    	return static::getResult(config('show91.url.addpic'), $options);
    }

    /**
     * 对已发布的订单发起主动撤单，返回资金
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function chedan($options = [])
    {
    	return static::getResult(config('show91.url.chedan'), $options);
    }

    /**
     * 订单确认验收结算
     * @param  [type] $options [oid => 订单id，p => 支付密码]
     * @return [type]          [description]
     */
    public static function accept($options = [])
    {
    	return static::getResult(config('show91.url.accept'), $options);
    }

    /**
     * 获取订单留言
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function messageList($options = [])
    {
    	$res = static::getResult(config('show91.url.messageList'), $options);
        $res = json_decode($res->getContents());

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
    	return static::getResult(config('show91.url.addevidence'), $options);
    }

    /**
     * 订单补款
     * @param [type] $options [oid => 订单id， appwd => 订单密码, cash => 补款金额]
     */
    public static function addPrice($options = [])
    {
    	return static::getResult(config('show91.url.addPrice'), $options);
    }

    /**
     * 增加订单代练时间
     * @param [type] $options [oid => 订单id, orderAddTime.days => 增加天数, orderAddTime.hours => 增加小时数,
     *                        orderAddTime.msg => 留言长度可为空]
     */
    public static function addLimitTime($options = [])
    {
    	return static::getResult(config('show91.url.addLimitTime'), $options);
    }

    /**
     * 确认增加代练时间
     * @param  [type] $options [oid => 订单id, v => 处理结果1同意，2不同意, reply => 回复长度255，可控]
     * @return [type]          [description]
     */
    public static function confirmAt($options = [])
    {
    	return static::getResult(config('show91.url.confirmAt'), $options);
    }

    /**
     * 订单上下架
     * @param  [type] $options [oid => 订单id]
     * @return [type]          [description]
     */
    public static function grounding($options = [])
    {
    	return static::getResult(config('show91.url.grounding'), $options);
    }

    /**
     * 增加代练时间，商家用
     * @param [type] $options [day => 天, hour => 小时]
     */
    public static function addLimitTime2($options = [])
    {
    	return static::getResult(config('show91.url.addLimitTime2'), $options);
    }
}
