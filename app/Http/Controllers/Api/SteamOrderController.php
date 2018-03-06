<?php

namespace App\Http\Controllers\Api;

use App\Extensions\Asset\Consume;
use App\Extensions\Asset\Facades\Asset;
use App\Models\SteamCdkeyLibrary;
use App\Models\SteamOrder;
use App\Models\SteamStorePrice;
use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SteamOrderController extends Controller
{

    public function getOrder()
    {

        $data = array();

        $value = \Redis::lpop('steam:order:order_no');//队列去订单号
        if (!$value) { //如果存在
            $data['status'] = 0;
            $data['info'] = '暂无订单';
            return $data;
        }
        Helper::log('take-order', ['被取走的订单号' => $value]);
        $order = SteamOrder::where('no', $value)->first();

        SteamOrder::where('no', $value)->update(['status' => 2]);

        $data['status'] = 1;
        $data['info'] = 'yes';
        return $order;
    }


    public function returnOrderData(Request $request)
    {
        DB::beginTransaction();
        try {

            if (!isset($request['order_no']) || !isset($request['status']) || !isset($request['consume_money']) || !isset($request['filled_account'])) {
                $data['status'] = -1;
                $data['info'] = '参数不正确';
                return $data;
            }
            $order = SteamOrder::where('no', $request['order_no'])->first();
            if (!$order) {
                $data['status'] = -1;
                $data['info'] = '订单号不存在';
                return $data;
            }

            if ($order->status == 1) {
                $data['status'] = -1;
                $data['info'] = '该订单已交易成功不能修改';
                return $data;
            }

            if ($request['status'] == 1) {
                SteamCdkeyLibrary::where('cdk', $order->cdk)->update(['status' => 0]);//修改cdk状态为0已使用
            }

            if ($request['status'] == 3) {
                SteamCdkeyLibrary::where('cdk', $order->cdk)->update(['status' => 1]);//'1' => '正常',
            }

            if ($request['status'] == 4) {
                SteamCdkeyLibrary::where('cdk', $order->cdk)->update(['status' => 4]);//'4' => '处理中'
            }

            if ($request['status'] == 5) {
                SteamCdkeyLibrary::where('cdk', $order->cdk)->update(['status' => 4]);//'4' => '处理中'
            }

            $order = SteamOrder::where('no', $request['order_no'])->update([
                'status' => $request['status'],
                'consume_money' => $request['consume_money'],
                'message' => $request['message'] ?? '',
                'filled_account' => $request['filled_account'],
                'success_time' => Carbon::now(),
            ]);

            $userId = SteamOrder::where('no', $request['order_no'])->value('user_id');

            if ($order) {
                if ($request['status'] == '1') {
					$SteamStorePrice = SteamStorePrice::where('user_id', $userId)->first();
					if ($SteamStorePrice) {
						$debit_money = bcmul($SteamStorePrice->clone_price, $request['consume_money'], 4);
					} else {
						$debit_money = bcmul(0.005, $request['consume_money'], 4);
					}
					Asset::handle(new Consume($debit_money, Consume::TRADE_SUBTYPE_BROKERAGE, $request['order_no'], 'Steam手续费', $userId));
                }
            }
            DB::commit();
            $data['status'] = 1;
            $data['info'] = 'yes';
            return $data;

        } catch (\Exception $e) {
            DB::rollBack();
            $data['status'] = -1;
            $data['info'] = $e->getMessage();
            return $data;

        }

    }

}
