<?php
namespace App\Repositories\Backend;

use App\Models\UserWithdrawOrder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserWithdrawOrderRepository
{
    public function getList($timeStart, $timeEnd, $userId, $no, $status, $adminRemark, $pageSize = 20)
    {
        $dataList = UserWithdrawOrder::orderBy('creator_primary_user_id')->orderBy('id')
            ->with('user')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('creator_primary_user_id', $userId);
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
    public function export($timeStart, $timeEnd, $userId, $no, $status, $adminRemark)
    {
        $order = $this->getList($timeStart, $timeEnd, $userId, $no, $status, $adminRemark, 0);

        $response = new StreamedResponse(function () use ($order){
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
            fputcsv($out, [
                '主账号ID',
                '姓名',
                '开户行',
                '卡号',
                '提现金额',
                '状态',
                '申请时间',
                '处理时间',
            ]);

            foreach ($order as $k => $v) {
                fputcsv($out, [
                    $v->creator_primary_user_id,
                    $v->user->realNameIdent->name ?? '',
                    $v->user->realNameIdent->bank_name ?? '',
                    isset($v->user->realNameIdent->bank_number) ? $v->user->realNameIdent->bank_number . "\t" : '',
                    $v->fee,
                    config('withdraw.status')[$v->status],
                    $v->created_at,
                    $v->uddate_at,
                ]);
            }
            fclose($out);
        },200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="提现记录.csv"',
        ]);
        $response->send();
    }
}
