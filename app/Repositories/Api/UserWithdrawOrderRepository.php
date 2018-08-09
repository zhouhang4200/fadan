<?php
namespace App\Repositories\Api;

use App\Models\UserWithdrawOrder;
use DB;
use App\Exceptions\CustomException;
use Asset;
use App\Extensions\Asset\Withdraw;
use App\Extensions\Asset\Unfreeze;

// 提现
class UserWithdrawOrderRepository
{
    public static function withdrawResult($data)
    {
        DB::beginTransaction();

        $order = UserWithdrawOrder::where('bill_id', $data['BillID'])->lockForUpdate()->first();
        if (empty($order)) {
            throw new CustomException('订单不存在');
        }

        if ($order->status != 6) {
            throw new CustomException('状态不正确');
        }

        if ($data['BillStatus'] == 1) { // 成功
            Asset::handle(new Withdraw($order->fee, 23, $order->no, '自动提现', $order->creator_primary_user_id, 0));
            $order->status = 7;
        } else { // 失败
            Asset::handle(new Unfreeze($order->fee, 41, $order->no, '自动提现失败解冻', $order->creator_primary_user_id, 0));
            $order->status = 8;
        }

        // 写多态关联
        if (!$order->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('资产操作失败');
        }

        if (!$order->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('资产操作失败');
        }

        $order->bill_status        = $data['BillStatus'];
        $order->bill_user_name     = $data['BillUserID'] . '.' . $data['BillUserName'];
        $order->pay_account        = $data['PayAccount'];
        $order->pay_bank_full_name = $data['PayBankFullName'];
        $order->transfer_detail    = json_encode($data['TransferDetail']);
        $order->save();

        DB::commit();
        return true;
    }
}
