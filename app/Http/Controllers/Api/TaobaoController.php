<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Api\TaobaoTradeRepository;
use App\Exceptions\CustomException as Exception;

/**
 * 接收已授权店铺的淘宝订单
 * Class TaobaoController
 * @package App\Http\Controllers\Api
 */
class TaobaoController extends Controller
{
    /**
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = TaobaoTradeRepository::getParams($request->data);
            TaobaoTradeRepository::create($data['trade'], $data['order']);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 交易成功
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function tradeSuccess(Request $request)
    {
        try {
            $data = TaobaoTradeRepository::getParams($request->data);
            $data['trade']['trade_status'] = 2; // 1.买家付完款 2.交易成功 3.买家发起退款

            TaobaoTradeRepository::update($data['trade'], $data['order']);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 买家发起退款
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refundCreated(Request $request)
    {
        try {
            $data = TaobaoTradeRepository::getParams($request->data);
            $data['trade']['trade_status'] = 3; // 1.买家付完款 2.交易成功 3.买家发起退款

            TaobaoTradeRepository::update($data['trade'], $data['order']);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }
}
