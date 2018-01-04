<?php
namespace App\Repositories\Api\App;

use App\Exceptions\CustomException;
use DB;
use App\Models\Order;
use App\Models\OrderCharge;
use App\Models\OrderChargeRecord;
use Order as OrderForm;
use App\Extensions\Order\Operations\Delivery;

class OrderChargeRepository
{
    /**
     * 订单详情
     * @param $orderNo
     */
    public static function record($orderNo, $qsOrderId, $stockId, $gameGold, $productId, $bundleId, $userId)
    {
        $Order = Order::where('no', $orderNo)->first();
        if (empty($Order)) {
            throw new CustomException('订单不存在');
        }

        DB::beginTransaction();

        // 取充值记录
        $OrderCharge = OrderCharge::lockForUpdate()->firstOrNew(['order_no' => $orderNo]);

        // 存在则更新，不存在则新建
        if ($OrderCharge->exists) {
            $OrderCharge->charged_game_gold += $gameGold;
        } else {
            $orderDetail = $Order->detail()->pluck('field_value', 'field_name');
            if (!isset($orderDetail['game_gold']) || empty($orderDetail['game_gold'])) {
                myLog('app-charge-record-fail', ['充值失败', $orderNo, '该商品未设置游戏币数量，不能充值']);
                throw new CustomException('该商品未设置游戏币数量，不能充值');
            }

            $OrderCharge->user_id           = $Order->gainer_primary_user_id;
            $OrderCharge->qs_order_id       = $qsOrderId;
            $OrderCharge->total_game_gold   = $orderDetail['game_gold'] * $Order->quantity;
            $OrderCharge->charged_game_gold = $gameGold;
            $OrderCharge->game_gold_unit    = $orderDetail['game_gold_unit'] ?? '';
            $OrderCharge->status            = 1; // 充值中
            $OrderCharge->product_id        = $productId;
            $OrderCharge->bundle_id         = $bundleId;
        }

        // 判断是否已充满
        if ($OrderCharge->charged_game_gold >= $OrderCharge->total_game_gold) {
            $OrderCharge->status = 2; // 充值完成

            // 自动发货
            try {
                OrderForm::handle(new Delivery($orderNo, $userId));
            }
            catch (CustomException $e) {
                myLog('app-charge-record-fail', ['过充', $orderNo, $qsOrderId, $stockId, $gameGold, $productId, $bundleId]);
            }
        }

        if (!$OrderCharge->save()) {
            DB::rollback();
            myLog('app-charge-record-fail', ['更新充值失败', $orderNo, $qsOrderId, $stockId, $gameGold, $productId, $bundleId]);
            throw new CustomException('更新充值失败');
        }

        // 创建记录
        $OrderChargeRecord = new OrderChargeRecord;
        $OrderChargeRecord->user_id   = $Order->gainer_primary_user_id;
        $OrderChargeRecord->order_no  = $orderNo;
        $OrderChargeRecord->game_gold = $gameGold;
        $OrderChargeRecord->stock_id  = $stockId;

        if (!$OrderChargeRecord->save()) {
            DB::rollback();
            myLog('app-charge-record-fail', ['写更新记录失败', $orderNo, $qsOrderId, $stockId, $gameGold, $productId, $bundleId]);
            throw new CustomException('写更新记录失败');
        }

        DB::commit();
        return true;
    }
}
