<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Api\TaobaoTradeRepository;
use App\Exceptions\CustomException as Exception;

/**
 * 接收已授权店铺的淘宝订单
 * trade_status 1.等待卖家发货 2.交易成功 3.买家已经申请退款 4.卖家已发货 5.卖家已经同意退款 6.卖家拒绝退款 7.退款成功 8.退款关闭
 * Class TaobaoController
 * @package App\Http\Controllers\Api
 */
class TaobaoController extends Controller
{
    /**
     * 付款完成
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = TaobaoTradeRepository::getParams($request->data);

            try {
                if($data['trade']['num_iid'] == '563493487291' && $data['trade']['payment'] <= 10) {
                    $data['trade']['handle_status'] = 1;
                }
            } catch (\Exception $exception) {

            }
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
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 2,
                'handle_status' => 1,
            ]);
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
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 3,
                'handle_status' => 1,
            ]);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 卖家发货
     * @param Request $request
     */
    public function tradeShip(Request $request)
    {
        try {
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 4,
                'handle_status' => 1,
            ]);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 卖家同意退款
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refundAgree(Request $request)
    {
        try {
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 5,
                'handle_status' => 1,
            ]);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 卖家拒绝退款
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refundRefuse(Request $request)
    {
        try {
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 6,
                'handle_status' => 1,
            ]);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 退款成功
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refundSuccess(Request $request)
    {
        try {
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 7,
                'handle_status' => 1,
            ]);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }

    /**
     * 退款关闭
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function refundClosed(Request $request)
    {
        try {
            TaobaoTradeRepository::updateTradeStatus([
                'tid' => $request->data['Tid'],
                'trade_status' => 8,
                'handle_status' => 1,
            ]);
        } catch (Exception $e) {
            return response()->tb(0, $e->getMessage());
        }

        return response()->tb(1);
    }
}
