<?php

namespace App\Http\Controllers\Api\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Api\App\OrderRepository;
use App\Models\Order;
use App\Repositories\Api\App\OrderChargeRepository;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $userId    = 123;
        $orderNo   = '2018010213411100000821';
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
