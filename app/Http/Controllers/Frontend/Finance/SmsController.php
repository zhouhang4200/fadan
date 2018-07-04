<?php

namespace App\Http\Controllers\Frontend\Finance;

use App\Models\SmsRecharge;
use App\Models\UserAsset;
use DB;
use Asset;
use App\Extensions\Asset\Consume;
use App\Models\SmsBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SmsController extends Controller
{
    /**
     * @param Request $request
     */
    public function recharge(Request $request)
    {
        DB::beginTransaction();
        // 找查是否有记录,如果有则在当前记录上加上购买的数量,没有则直接创建
        $smsBalance = SmsBalance::where('user_id', auth()->user()->getPrimaryUserId())->lockForUpdate()->first();

        $orderNO = generateOrderNo();

        try {
            Asset::handle(new Consume(bcmul($request->amount, 0.1), 4, $orderNO, '购买短信', auth()->user()->id));

            if (!$smsBalance) {
                $beforeAmount = 0;
                $afterAmount = $request->amount;
                SmsBalance::create([
                    'user_id' => auth()->user()->getPrimaryUserId(),
                    'amount' => $request->amount,
                ]);
            } else {
                $beforeAmount = $smsBalance->amount;
                $afterAmount = $beforeAmount + $request->amount;

                $smsBalance->amount = $afterAmount;
                $smsBalance->save();
            }

            // 创建订单
            SmsRecharge::create([
                'user_id' => auth()->user()->getPrimaryUserId(),
                'order_no' => $orderNO,
                'before_amount' => $beforeAmount,
                'amount' => $request->amount,
                'after_amount' => $afterAmount,
            ]);
        } catch (\Exception $exception) {
            return response()->ajax('0', $exception->getMessage());
        }
        DB::commit();
        return response()->ajax('1', '充值成功', [
            'sms_balance' => $afterAmount,
            'balance' => UserAsset::where('user_id', auth()->user()->getPrimaryUserId())->value('balance') + 0
        ]);
    }
}
