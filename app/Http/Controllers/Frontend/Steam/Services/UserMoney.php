<?php

namespace App\Http\Controllers\Frontend\Steam\Services;

use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Recharge;
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
            Asset::handle(new Consume($debit_money, Consume::TRADE_SUBTYPE_BROKERAGE, $order_no, '消费手续费', $userId));
        } catch (\Exception $e) {
            Helper::log('steam-new-money', [$e->getMessage(), [$money, '1', $order_no, '消费手续费', $userId]]);
            throw new Exception($e->getMessage());
        }

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
            Asset::handle(new Recharge($money, Recharge::TRADE_SUBTYPE_AUTO, date("YmdHis").rand(1,1000), '银行卡自动充值', $userId));
        } catch (Exception $e) {
            Helper::log('steam-add-money', ['异常', $e->getMessage(), '用户：' => $userId, '加款金额' => $money]);
            throw new Exception($e->getMessage());
        }

    }


}
