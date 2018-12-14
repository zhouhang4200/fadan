<?php
namespace App\Repositories\Backend;

use App\Models\UserWithdrawOrder;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exceptions\CustomException;
use App\Extensions\Asset\Freeze;
use DB;
use Asset;
use Auth;
use App\Services\FuluPay;
use Storage;

class UserWithdrawOrderRepository
{
    public function getList($timeStart, $timeEnd, $userId, $no, $type, $status, $adminRemark, $pageSize = 20)
    {
        $dataList = UserWithdrawOrder::orderBy('creator_primary_user_id')->orderBy('id')
            ->with('user')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('creator_primary_user_id', $userId);
            })
            ->when(!empty($type), function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->when(!empty($status), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(!empty($no), function ($query) use ($no) {
                return $query->where('no', $no);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd);
            })
            ->when(!empty($adminRemark), function ($query) use ($adminRemark) {
                return $query->where('admin_remark', $adminRemark);
            })
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }

    /**
     * @param $timeStart
     * @param $timeEnd
     * @param $userId
     * @param $no
     * @param $status
     * @param $adminRemark
     */
    public function export($timeStart, $timeEnd, $userId, $no, $type, $status, $adminRemark)
    {
        $order = $this->getList($timeStart, $timeEnd, $userId, $no, $type, $status, $adminRemark, 0);

        $response = new StreamedResponse(function () use ($order){
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
            fputcsv($out, [
                '提现单号',
                '主账号ID',
                '姓名',
                '开户行',
                '卡号',
                '提现金额',
                '类型',
                '状态',
                '申请时间',
                '处理时间',
            ]);

            foreach ($order as $k => $v) {
                fputcsv($out, [
					$v->no . "\t",
                    $v->creator_primary_user_id,
                    $v->user->realNameIdent->name ?? '',
                    $v->user->realNameIdent->bank_name ?? '',
                    isset($v->user->realNameIdent->bank_number) ? $v->user->realNameIdent->bank_number . "\t" : '',
                    $v->fee,
                    config('withdraw.type')[$v->type],
                    config('withdraw.status')[$v->status],
                    $v->created_at,
                    $v->updated_at,
                ]);
            }
            fclose($out);
        },200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="提现记录.csv"',
        ]);
        $response->send();
    }

    /**
     * 手工减款
     * @return mixed
     */
    public static function subtractMoney($userId, $fee, $remark)
    {
        DB::beginTransaction();

        $no = generateOrderNo();

        // 资产操作
        Asset::handle(new Freeze($fee, 3, $no, $remark, $userId, auth('admin')->user()->id));

        // 创建提现单
        $withdraw = new UserWithdrawOrder;
        $withdraw->no                      = $no;
        $withdraw->type                    = 2;
        $withdraw->status                  = 1;
        $withdraw->fee                     = $fee;
        $withdraw->creator_user_id         = $userId;
        $withdraw->creator_primary_user_id = $userId;
        $withdraw->remark                  = $remark;

        if (!$withdraw->save()) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        // 写多态关联
        if (!$withdraw->userAmountFlows()->save(Asset::getUserAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        if (!$withdraw->platformAmountFlows()->save(Asset::getPlatformAmountFlow())) {
            DB::rollback();
            throw new Exception('申请失败');
        }

        // 减款
        $withdraw->complete($remark, 2);

        DB::commit();
    }

    // 更新状态
    public static function setStatus($id, $before, $after)
    {
        DB::beginTransaction();

        // 查询订单
        $order = UserWithdrawOrder::lockForUpdate()->find($id);
        if (empty($order)) {
            throw new CustomException('记录不存在');
        }

        if ($order->status != $before) {
            throw new CustomException('状态不正确');
        }

        $order->status = $after;
        $order->save();

        DB::commit();
        return true;
    }

    // 更新附件
    public static function setAttach($id, $path)
    {
        DB::beginTransaction();

        // 查询订单
        $order = UserWithdrawOrder::lockForUpdate()->find($id);
        if (empty($order)) {
            throw new CustomException('记录不存在');
        }

        if ($order->status != 4) {
            throw new CustomException('状态不正确');
        }

        if ($order->attach) {
            Storage::delete($order->attach); // 删除图片
        }

        $order->attach = $path;
        $order->save();

        DB::commit();
        return true;
    }

    // 自动办款
    public static function auto($id)
    {
        DB::beginTransaction();

        // 查询订单
        $order = UserWithdrawOrder::lockForUpdate()->find($id);
        if (empty($order)) {
            throw new CustomException('记录不存在');
        }

        if ($order->status != 5) {
            throw new CustomException('只有待确认状态才能操作');
        }

        if (empty($order->bank_name)) {
            $order->account_name = $order->user->realNameIdent->name;
            $order->bank_name    = $order->user->realNameIdent->bank_name;
            $order->bank_card    = $order->user->realNameIdent->bank_number;
        }

        $order->status = 6; // 6.办款中
        $order->save();

        // 通知接口
        $fuluPay = new FuluPay;
        $order->bill_id = $fuluPay->withdraw(
            $order->id,
            $order->fee,
            $order->type == 1 ? 2 : 1,
            2,
            $order->bank_card,
            $order->account_name,
            $order->bank_name
        );

        $order->save();

        DB::commit();
        return true;
    }

    public static function find($id)
    {
        $order = UserWithdrawOrder::find($id);
        return $order;
    }
}
