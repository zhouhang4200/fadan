<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AssetException;
use App\Models\Recharge as RechargeModel;
use App\Extensions\Asset\Recharge;
use App\Models\User;
use App\Models\UserAmountFlow;
use App\Services\Mcrypt3Des;
use DB, Asset;
use Illuminate\Http\Request;

/**
 * Class AutoAddFundsController
 * @package App\Http\Controllers\Api
 */
class AutoAddFundsController
{
    /**
     * 自动加款会员
     * @param Request $request
     */
    public function member(Request $request)
    {
        if (!in_array(getClientIp(), ['113.57.118.58', '113.57.118.50', '120.202.26.95', '113.57.130.18', '116.205.13.50', '120.76.129.109', '120.25.253.208', '116.205.11.26', '116.205.11.27'])) {
            myLog('auto-funds', ['ip' => getClientIp(), 'IP不在白名单']);
            return response()->ajax(0, 'IP不在白名单');
        }
        $token = '4cdM8BQ894RnN9LrcwpdTJpHo7Ga4MIrm1';
        $data = $request->input('data', 0);
        $sign = $request->input('sign', 0);
        $timestamp = $request->input('timestamp', time());

        if (abs($timestamp - time()) > 120) {
            return response()->ajax(0, '无效请求');
        }

        if (!$data || !$sign || !$timestamp) {
            return response()->ajax(0, '参数不完整');
        }

        if (md5($data . $token . $timestamp) != $sign) {
            return response()->ajax(0, '签名错误');
        }

        $crypt = new Mcrypt3Des('4RnN9Lr7', '4RnN9Lr7');
        $cryptData = $crypt->decrypt(base64_decode($data));

        myLog('auto-funds', [$cryptData]);
        try {
            $requestData = json_decode($cryptData);
        } catch (\Exception $e) {
            return response()->ajax(0, '解密失败');
        }

        if (!isset($requestData->user_id) || !isset($requestData->order_id) || !isset($requestData->money)) {
            return response()->ajax(0, '业务参数不完整');
        }

        $userInfo = User::where('id', $requestData->user_id)->first();

        if (!$userInfo) {
            return response()->ajax(0, '不存在的用户');
        }
        try {
            // 创建加款订单
            RechargeModel::create([
                'foreign_order_no' => $requestData->order_id,
                'amount' => $requestData->money,
                'user_id' => $requestData->user_id,
            ]);
        } catch (\Exception $exception) {
            return response()->ajax(0, '该订单号已经成功自动加款');
        }

        // 是否存在 加款记录
        $exist = UserAmountFlow::where('user_id', $requestData->user_id)
            ->where('trade_type', 1)
            ->where('trade_subtype', 11)
            ->where('trade_no', $requestData->order_id)
            ->first();

        if ($exist) {
            return response()->ajax(0, '该订单号已经成功自动加款');
        }

        // 加款
        try {
            Asset::handle(new Recharge($requestData->money, Recharge::TRADE_SUBTYPE_AUTO, $requestData->order_id, '银行卡自动充值', $requestData->user_id));
            return response()->ajax(1, '加款成功');
        } catch (AssetException $assetException) {
            return response()->ajax(0, '加款失败');
        }
    }

    /**
     * @param Request $request
     */
    public function memberInfo(Request $request)
    {
        $memberInfo = RealNameIdent::where('status', 1)->pluck('name', 'user_id')->toJson();


        $crypt = new Mcrypt3Des('4RnN9Lr7', '4RnN9Lr7');
        $encryptData = $crypt->encrypt($memberInfo);

        return response()->ajax(1, '加款成功', $encryptData);
    }
}