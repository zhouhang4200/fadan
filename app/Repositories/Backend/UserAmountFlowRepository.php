<?php
namespace App\Repositories\Backend;

use App\Models\UserAmountFlow;
use Excel;
class UserAmountFlowRepository
{
    public function getList($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEnd, $pageSize = 20)
    {
        $dataList = UserAmountFlow::orderBy('id', 'desc')
            ->when(!empty($userId), function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->when(!empty($tradeNo), function ($query) use ($tradeNo) {
                return $query->where('trade_no', $tradeNo);
            })
            ->when(!empty($tradeType), function ($query) use ($tradeType) {
                return $query->where('trade_type', $tradeType);
            })
            ->when(!empty($tradeSubtype), function ($query) use ($tradeSubtype) {
                return $query->where('trade_subtype', $tradeSubtype);
            })
            ->when(!empty($timeStart), function ($query) use ($timeStart) {
                return $query->where('created_at', '>=', $timeStart);
            })
            ->when(!empty($timeEnd), function ($query) use ($timeEnd) {
                return $query->where('created_at', '<=', $timeEnd);
            })
            ->when($pageSize === 0, function ($query) {
                return $query->limit(10000)->get();
            })
            ->when($pageSize, function ($query) use ($pageSize) {
                return $query->paginate($pageSize);
            });

        return $dataList;
    }

    public function export($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEnd){

		$dataList = $this->getList($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEnd, 0);

		// 标题
		$title = [
			'流水号',
			'用户',
			'管理员',
			'类型',
			'子类型',
			'相关单号',
			'金额',
			'备注',
			'平台资金',
			'平台托管',
			'用户余额',
			'用户冻结',
			'累计用户加款',
			'累计用户提现',
			'累计用户消费',
			'累计退款给用户',
			'累计用户支出',
			'累计用户收入',
			'时间',
		];
		// 数组分割,反转
		$chunkOrders = array_chunk(array_reverse($dataList->toArray()), 500);

		Excel::create(iconv('UTF-8', 'gbk', '商户资金流水'), function ($excel) use ($chunkOrders, $title) {

			foreach ($chunkOrders as $chunkOrder) {
				// 内容
				$datas = [];
				foreach ($chunkOrder as $key => $v) {
					$datas[] = [
						$v['id'],
						$v['user_id'],
						$v['admin_user_id'],
						config('tradetype.platform')[$v['trade_type']] ?? $v['trade_type'] ,
						config('tradetype.platform_sub')[$v['trade_subtype']] ?? $v['trade_subtype'] ,
						$v['trade_no'] ?? 0,
						$v['fee'] ?? 0,
						$v['remark'] ?? '',
						$v['amount'] ?? 0,
						$v['managed'] ?? 0,
						$v['balance'] ?? 0,
						$v['frozen'] ?? 0,
						$v['total_recharge'] ?? 0,
						$v['total_withdraw'] ?? 0,
						$v['total_consume'] ?? 0,
						$v['total_refund'] ?? 0,
						$v['total_expend'] ?? 0,
						$v['total_income'] ?? 0,
						$v['created_at'] ?? '',
					];
				}
				// 将标题加入到数组
				array_unshift($datas, $title);
				// 每页多少数据
				$excel->sheet("页数", function ($sheet) use ($datas) {
					$sheet->rows($datas);
				});
			}
		})->export('xls');

	}

}
