<?php
namespace App\Repositories\Backend;

use App\Models\Deposit;
use App\Models\User;
use App\Exceptions\CustomException;
use DB;
use Auth;
use Asset;
use App\Extensions\Asset\Expend;
use App\Extensions\Asset\Income;

// 押金
class DepositRepository
{
    public static function getList($no, $userId, $type, $status)
    {
        $dataList = Deposit::orderBy('id', 'desc')
            ->when($no, function ($query) use ($no) {
                return $query->where('no', $no);
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->paginate(20);

        return $dataList;
    }

    public static function find($id)
    {
        $model = Deposit::lockForUpdate()->find($id);
        if (empty($model)) {
            throw new CustomException('数据不存在');
        }

        return $model;
    }

    public static function store($userId, $type, $amount, $remark)
    {
        // 验证用户
        $user = User::find($userId);
        if (empty($user)) {
            throw new CustomException('用户不存在');
        }

        // 创建提现单
        $model = new Deposit;
        $model->user_id    = $userId;
        $model->no         = generateOrderNo();
        $model->type       = $type;
        $model->status     = 1;
        $model->amount     = $amount;
        $model->remark     = $remark;
        $model->created_by = Auth::user()->id;

        if (!$model->save()) {
            throw new CustomException('数据创建失败');
        }

        return true;
    }

    // 扣款审核
    public static function deductAudite($id)
    {
        DB::beginTransaction();

        $model = self::find($id);
        if ($model->status != 1) {
            throw new CustomException('状态不正确');
        }

        $model->status = 2;
        $model->deduct_audited_by = Auth::user()->id;
        if (!$model->save()) {
            DB::rollback();
            throw new CustomException('操作失败');
        }

        // 加款
        Asset::handle(new Expend($model->amount, 78, $model->no, $model->remark, $model->user_id, Auth::user()->id));
        // 写多态关联
        if (!$model->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }
        if (!$model->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        // Asset::handle(new Income(40.0928, Income::TRADE_SUBTYPE_ORDER_MARKET, '2017101' . rand(1000, 9999), '接单发货', Auth::user()->id, 888));

        DB::commit();
        return true;
    }

    // 退押金
    public static function refund($id)
    {
        DB::beginTransaction();

        $model = self::find($id);
        $model->refunded_by = Auth::user()->id;
        if ($model->status != 2) {
            throw new CustomException('状态不正确');
        }

        $model->status = 3;
        if (!$model->save()) {
            DB::rollback();
            throw new CustomException('操作失败');
        }

        DB::commit();
        return true;
    }

    // 退押金
    public static function refundAudit($id)
    {
        DB::beginTransaction();

        $model = self::find($id);
        if ($model->status != 3) {
            throw new CustomException('状态不正确');
        }

        $model->status = 4;
        $model->deduct_audited_by = Auth::user()->id;
        if (!$model->save()) {
            DB::rollback();
            throw new CustomException('操作失败');
        }

        // 加款
        Asset::handle(new Income($model->amount, 816, $model->no, $model->remark, $model->user_id, Auth::user()->id));
        // 写多态关联
        if (!$model->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }
        if (!$model->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        DB::commit();
        return true;
    }
}
