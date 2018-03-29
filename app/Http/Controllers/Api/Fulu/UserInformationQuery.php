<?php

namespace App\Http\Controllers\Api\Fulu;

use App\Models\RealNameIdent;
use App\Models\QianShouUser;
use DB, Asset;
use Illuminate\Http\Request;

/**
 * 公司：手游用户信息查询接口
 * Class AutoAddFundsController
 * @package App\Http\Controllers\Api
 */
class UserInformationQuery
{

    protected $token = 'j9Z3tPmQGyt0b9fzOGVfEt';

    /**
     * 查询用户信息
     * 返回信息
     * 商户姓名，手机号，身份证号，提款银行卡号，公司名称，注册时间，最后登录IP
     * @param Request $request
     * @return bool
     *
     */
    public function index(Request $request)
    {
        $idNumber  = $request->id_number;
        $timestamp  = $request->timestamp;
        $sign  = $request->sign;

        // 签名验证 示例 id_number=51230119730806667452   sign=3b17565355b2c5c3c90a129ab2dd9b89   timestamp=1521019316
        if ($sign != md5($idNumber . $timestamp . $this->token)) {
            return response()->ajax(0, '签名错误');
        }

        if (!$idNumber) {
            return response()->ajax(0, '请输入要查询的身份证号');
        }

        // 查询订单集市
        $queryResult = RealNameIdent::where('identity_card', $idNumber)->with('user')->first();

        $info = [];
        if ($queryResult) {
            $info = [
                'name' => $queryResult->name,
                'phone_number' => $queryResult->phone_number,
                'id_number' => $queryResult->identity_card,
                'bank_name' => $queryResult->bank_name,
                'bank_card' => $queryResult->bank_number,
                'corporate_name' => $queryResult->license_name,
                'register_time' => $queryResult->user->created_at->toDateTimeString(),
                'last_login_time' => $queryResult->user->updated_at->toDateTimeString(),
            ];
        } else {
            // 查询千手
            $queryResult = QianShouUser::where('id_number', $idNumber)->first();

            if ($queryResult) {
                $info = [
                    'name' => $queryResult->withdrawals_name,
                    'phone_number' => $queryResult->user_phone,
                    'id_number' => $queryResult->id_number,
                    'bank_name' => $queryResult->withdrawals_bank,
                    'bank_card' => $queryResult->withdrawals_account,
                    'corporate_name' => $queryResult->user_company,
                    'register_time' => $queryResult->created_at->toDateTimeString(),
                    'last_login_time' => $queryResult->updated_at->toDateTimeString(),
                ];
            }
        }
        if ($info) {
            return response()->ajax(1, '查询成功', $info);
        }
        return response()->ajax(0, '没有查到相关数据');
    }
}