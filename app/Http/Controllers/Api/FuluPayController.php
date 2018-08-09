<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FuluPay;
use App\Repositories\Api\UserWithdrawOrderRepository;
use App\Exceptions\CustomException;

// 财务接口回调
class FuluPayController extends Controller
{
    public function withdrawNotify(Request $request)
    {
        try {
            if (!$request->has('BillID')) throw new CustomException('参数不正确1');
            if (!$request->has('BillType')) throw new CustomException('参数不正确2');
            if (!$request->has('BillDate')) throw new CustomException('参数不正确3');
            if (!$request->has('BillStatus')) throw new CustomException('参数不正确4');
            if (!$request->has('BillUserID')) throw new CustomException('参数不正确5');
            if (!$request->has('BillUserName')) throw new CustomException('参数不正确6');
            if (!$request->has('PayAccount')) throw new CustomException('参数不正确7');
            if (!$request->has('PayBankFullName')) throw new CustomException('参数不正确8');
            if (!$request->has('Sign')) throw new CustomException('参数不正确9');
            if (!$request->has('TransferDetail')) throw new CustomException('参数不正确10');

            // 验签
            FuluPay::checkSign($request->all());

            // 处理结果
            UserWithdrawOrderRepository::withdrawResult($request->all());

        } catch (CustomException $e) {
            return response()->finance('-1', $e->getMessage());
        }

        return response()->finance('0');
    }
}
