<?php
namespace App\Repositories\Api;

use DB;
use App\Exceptions\CustomException as Exception;
use App\Models\User;
use App\Models\UserRechargeOrder;
use Asset;
use App\Extensions\Asset\Recharge;

class UserRechargeOrderRepository
{
    static public function find($goodsId)
    {
        $goods = Goods::find($goodsId);
        return $goods;
    }

    /**
     * 创建加款单
     * @param $fee
     * @param $userId
     * @param $remark
     * @param $foreignOrderNo
     * @param $wangwang
     * @param bool $isAuto
     * @param integer $adminUserId
     * @return mixed
     * @throws Exception
     */
    public function store($fee, $userId, $remark, $foreignOrderNo, $wangwang, $isAuto = true, $adminUserId = 0)
    {
        DB::beginTransaction();

        $user = User::find($userId);

        $no = generateOrderNo();
        $primaryUserId = $user->getPrimaryUserId();

        // 增加余额
        try {
            $subtype = $isAuto ? Recharge::TRADE_SUBTYPE_AUTO : Recharge::TRADE_SUBTYPE_MANUAL;
            Asset::handle(new Recharge($fee, $subtype, $no, '自动充值', $primaryUserId, $adminUserId));
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // 创建提现单
        $recharge = new UserRechargeOrder;
        $recharge->no                      = $no;
        $recharge->foreign_order_no        = $foreignOrderNo;
        $recharge->wangwang                = $wangwang;
        $recharge->fee                     = $fee;
        $recharge->creator_user_id         = $userId;
        $recharge->creator_primary_user_id = $primaryUserId;
        $recharge->remark                  = $remark;
        $recharge->created_at              = date('Y-m-d H:i:s');

        if (!$recharge->save()) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        // 写多态关联
        if (!$recharge->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$recharge->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        DB::commit();
    }
}
