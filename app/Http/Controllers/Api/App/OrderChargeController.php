<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\CustomException;
use App\Repositories\Api\App\OrderChargeRepository;

class OrderChargeController extends Controller
{
    /**
     * 充值结果通知
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notify(Request $request)
    {
        $userId    = $request->params['user_id'];
        $orderNo   = $request->params['order_no'];
        $qsOrderId = $request->params['qs_order_id'];
        $stockId   = $request->params['stock_id'];
        $gameGold  = $request->params['game_gold'];
        $productId = $request->params['product_id'];
        $bundleId  = $request->params['bundle_id'];

        try {
            OrderChargeRepository::record($orderNo, $qsOrderId, $stockId, $gameGold, $productId, $bundleId, $userId);
        }
        catch (CustomException $e) {
            return response()->jsonReturn(0, $e->getMessage());
        }

        return response()->jsonReturn(1);
    }
}
