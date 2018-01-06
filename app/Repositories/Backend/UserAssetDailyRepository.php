<?php
namespace App\Repositories\Backend;

use App\Models\UserAssetDaily;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserAssetDailyRepository
{
    public function getList($userId, $dateStart, $dateEnd, $pageSize = 20)
    {
        $dataList = UserAssetDaily::orderBy('id', 'desc')->with('realNameIdent')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when(!empty($dateStart), function ($query) use ($dateStart) {
                return $query->where('date', '>=', $dateStart);
            })
            ->when(!empty($dateEnd), function ($query) use ($dateEnd) {
                return $query->where('date', '<=', $dateEnd);
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
     * @param $userId
     * @param $dateStart
     * @param $dateEnd
     */
    public function export($userId, $dateStart, $dateEnd)
    {
        $order = $this->getList($userId, $dateStart, $dateEnd, 0);

        $response = new StreamedResponse(function () use ($order){
            $out = fopen('php://output', 'w');
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF)); // 添加 BOM
            fputcsv($out, [
                '日期',
                '用户ID',
                '真名',
                '银行',
                '卡号',
                '余额',
                '冻结',
                '累计充值',
                '累计提现'
            ]);
//            $order->chunk(1000, function ($items) use ($out) {

                foreach ($order as $k => $v) {
                    fputcsv($out, [
                        $v->date,
                        $v->user_id,
                        $v->realNameIdent->name ?? '',
                        $v->realNameIdent->bank_name ?? '',
                        $v->realNameIdent->bank_number ?? '',
                        $v->balance,
                        $v->frozen,
                        $v->total_recharge,
                        $v->total_withdraw,
                    ]);
                }
//            });
            fclose($out);
        },200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="用户资产日报.csv"',
        ]);
        $response->send();
    }
}
