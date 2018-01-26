<?php

namespace App\Http\Controllers\Frontend\Steam\Services;

use App\Http\Controllers\Frontend\Steam\Custom\Helper;
use App\Exceptions\CustomException;
use App\Extensions\Asset\Facades\Asset;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAsset;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class UserMoney
{

    /**
     * 扣手续费
     * @param $order_no
     * @param $userId
     * @param $money
     */
    public static function userDebit($order_no, $userId, $money)
    {

        try {
            if(abs($money) == 0){
                throw new Exception('金额不能为0');
            };
            $debit_money = bcmul(0.01, $money, 4);
            Asset::consume($debit_money, 51, $order_no, '消费手续费', $userId);
        } catch (\Exception $e) {
            Helper::log('steam-new-money', [$e->getMessage(), [$money, '1', $order_no, '消费手续费', $userId]]);
            throw new Exception($e->getMessage());
        }


        /*// 开启 事务处理
        DB::beginTransaction();

        try {

            $row = User::where('id', $userId)->lockForUpdate()->first();
            Helper::log('order-log-money', ['订单号:', $order_no, '用户：' => $userId, '消费金额' => $money]);

            $debit_money = bcmul(0.01, $money, 4); //1.5  0.98

            $afterMoney = bcsub($row->balance, $debit_money, 4);

            // 减少余额
            $userAsset = User::where('id', $userId)->where('balance', $row->balance)->update([
                'balance' => $afterMoney,
                'updated_at' => Carbon::now(),
            ]);

            // 是否有更新
            if ($userAsset) {
                // 写入资金变化
                Helper::log('order-log-money', ['订单号:', $order_no, '用户：' => $userId, '扣款金额' => $debit_money, '余额' => $afterMoney]);
                self::writeLog(1, $userId, $order_no, $debit_money, $afterMoney, '');
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }*/


    }

    /**
     * 加款
     * @param $userId
     * @param $money
     * @param $remarks
     * @return bool
     */
    public static function moneyAdd($userId, $money, $remarks)
    {

        try {
            Asset::recharge($money, 11, date("YmdHis").rand(1,1000), $remarks, $userId);
        } catch (Exception $e) {
            Helper::log('steam-add-money', ['异常', $e->getMessage(), '用户：' => $userId, '加款金额' => $money]);
            throw new Exception($e->getMessage());
        }


        // 开启 事务处理
        /*DB::beginTransaction();
        try {

            $row = User::where('id', $userId)->lockForUpdate()->first();
            $happen_money = bcadd($row->balance, $money, 4);


            // 修改余额
            $userAsset = User::where('id', $userId)->update([
                'balance' => $happen_money,
                'updated_at' => Carbon::now(),
            ]);

            $order_no = date("YmdHis") . rand(10000, 99999);

            // 是否有更新
            if ($userAsset) {
                // 写入资金变化
                self::writeLog(2, $userId, $order_no, $money, $happen_money, $remarks);
                DB::commit();
                return true;
            }

        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }*/

    }


}
