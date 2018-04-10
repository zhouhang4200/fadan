<?php

namespace App\Http\Controllers\Frontend\Statistic;

use DB;
use Auth;
use Excel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrderStatistic;
use App\Models\EmployeeStatistic;
use App\Http\Controllers\Controller;

/**
 * 代练平台统计 员工和订单统计
 */
class StatisticController extends Controller
{
    /**
     * 员工统计
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function employee(Request $request)
    {
    	$userName = $request->user_name;
    	$startDate = $request->start_date;
    	$endDate = $request->end_date;
    	$children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
    	$fullUrl = $request->fullUrl();
    	$filters = compact('userName', 'startDate', 'endDate');

        if (Auth::user()->parent_id == 0) {
            $parent = Auth::user();
            $userIds = Auth::user()->children()->withTrashed()->pluck('id')->merge(Auth::id());
        } else {
            $parent = Auth::user()->parent;
            $userIds = Auth::user()->parent->children()->withTrashed()->pluck('id')->merge(Auth::user()->parent->id);
        }

   //  	$query = EmployeeStatistic::whereIn('user_id', $userIds)
			// ->filter($filters)
			// ->select(DB::raw('
   //              user_id, 
   //              username, 
   //              name, 
   //              sum(complete_order_count) as complete_order_count, 
   //              sum(revoke_order_count) as revoke_order_count, 
   //              sum(arbitrate_order_count) as arbitrate_order_count, 
   //              sum(profit) as profit, 
   //              sum(complete_order_amount) as complete_order_amount
   //          '))
			// ->groupBy('user_id');

        $query = EmployeeStatistic::whereIn('employee_statistics.user_id', $userIds)
            ->filter($filters)
            ->select(DB::raw('
                employee_statistics.user_id, 
                users.name as name,
                users.username as username,
                sum(employee_statistics.complete_order_count) as complete_order_count, 
                sum(employee_statistics.revoke_order_count) as revoke_order_count, 
                sum(employee_statistics.arbitrate_order_count) as arbitrate_order_count, 
                sum(employee_statistics.profit) as profit, 
                sum(employee_statistics.complete_order_amount) as complete_order_amount
            '))
            ->leftJoin('users', 'users.id', '=', 'employee_statistics.user_id')
            ->groupBy('employee_statistics.user_id');

    	$datas = $query->paginate(config('frontend.page'));
        $excelDatas = $query->get();

    	$totalData = EmployeeStatistic::whereIn('user_id', $userIds)
			->filter($filters)
			->select(DB::raw('
                sum(complete_order_count) as total_complete_order_count, 
                sum(revoke_order_count) as total_revoke_order_count, 
                sum(arbitrate_order_count) as total_arbitrate_order_count, 
                sum(profit) as total_profit, 
                sum(complete_order_amount) as total_complete_order_amount, 
                count(distinct(user_id)) as total_user_id_count
            '))
			->first();

    	if ($request->export) {
    		if ($datas->count() < 1) {
    			return redirect(route('frontend.statistic.employee'))->withInput()->with('empty', '数据为空!');
    		}
            return $this->exportEmployee($excelDatas, $totalData);
        }

    	return view('frontend.finance.employee', compact('datas', 'userName', 'startDate', 'endDate', 'children', 'fullUrl', 'totalData', 'parent'));
    }

    /**
     * 订单统计
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function order(Request $request)
    {
    	$startDate = $request->start_date;
    	$endDate = $request->end_date;
    	$fullUrl = $request->fullUrl();
    	$filters = compact('startDate', 'endDate');

        if (Auth::user()->parent_id == 0) {
            $userIds = Auth::user()->children()->withTrashed()->pluck('id')->merge(Auth::id());
        } else {
            $userIds = Auth::user()->parent->children()->withTrashed()->pluck('id')->merge(Auth::user()->parent->id);
        }

        // query桥
    	$query = OrderStatistic::whereIn('user_id', $userIds)
			->filter($filters)
			->select(DB::raw('
                user_id, date, 
				sum(send_order_count) as send_order_count,
			 	sum(receive_order_count) as receive_order_count, 
			 	sum(complete_order_count) as complete_order_count,
			 	ifnull(round(sum(complete_order_count)/sum(receive_order_count), 4), 0) as complete_order_rate, 
			 	sum(revoke_order_count) as revoke_order_count, 
				sum(arbitrate_order_count) as arbitrate_order_count,
				sum(three_status_original_amount) as three_status_original_amount,
				sum(complete_order_amount) as complete_order_amount,
				sum(two_status_payment) as two_status_payment,
				sum(two_status_income) as two_status_income,
				sum(poundage) as poundage,
				sum(profit) as profit
			'))
            ->latest('date')
			->groupBy('date');

        // 统计总计
		$totalData = OrderStatistic::whereIn('user_id', $userIds)
			->filter($filters)
			->select(DB::raw('
                user_id,
				sum(send_order_count) as total_send_order_count,
			 	sum(receive_order_count) as total_receive_order_count, 
			 	sum(complete_order_count) as total_complete_order_count,
			 	ifnull(round(sum(complete_order_count)/sum(receive_order_count), 4), 0) as total_complete_order_rate, 
			 	sum(revoke_order_count) as total_revoke_order_count, 
				sum(arbitrate_order_count) as total_arbitrate_order_count,
				sum(three_status_original_amount) as total_three_status_original_amount,
				sum(complete_order_amount) as total_complete_order_amount,
				sum(two_status_payment) as total_two_status_payment,
				sum(two_status_income) as total_two_status_income,
				sum(poundage) as total_poundage,
				sum(profit) as total_profit
			'))
			->first();
        // 导出数据
        $excelDatas = $query->get();
        // 页面统计分页数据
        $datas = $query->paginate(config('frontend.page'));

		if ($request->export) {
    		if ($datas->count() < 1) {
    			return redirect(route('frontend.statistic.order'))->withInput()->with('empty', '数据为空!');
    		}
            return $this->exportOrder($excelDatas, $totalData);
        }

    	return view('frontend.finance.order', compact('datas', 'startDate', 'endDate', 'fullUrl', 'totalData'));
    }

    /**
     * 统计导出
     * @param  [type] $excelDatas [description]
     * @param  [type] $totalData  [description]
     * @return [type]             [description]
     */
    public function exportEmployee($excelDatas, $totalData)
    {
        try {
            // 标题
            $title = [
                '员工姓名',
                '账号',
                '已结算单数',
                '已结算发单金额',
                '已撤销单数',
                '已仲裁单数',
                '利润',
            ];

            $totalData = [
            	'总计',
                $totalData->total_user_id_count,
                $totalData->total_complete_order_count,
                $totalData->total_complete_order_amount,
                $totalData->total_revoke_order_count,
                $totalData->total_arbitrate_order_count,
                $totalData->total_profit,
            ];

            $chunkDatas = array_chunk(array_reverse($excelDatas->toArray()), 500);

            Excel::create('员工统计', function ($excel) use ($chunkDatas, $title, $totalData) {
                foreach ($chunkDatas as $chunkData) {
                    // 内容
                    $datas = [];
                    foreach ($chunkData as $key => $data) {
                    	$user = User::find($data['user_id']);
                        $datas[] = [
                            $user->username,
                            $data['name'],
                            $data['complete_order_count'] ?? '--',
                            $data['complete_order_amount'] ?? '--',
                            $data['revoke_order_count'] ?? '--',
                            $data['arbitrate_order_count'] ?? '--',
                            $data['profit'] ?? '--',
                        ];
                    }
                    // 将标题加入到数组
                    array_unshift($datas, $title);
                    array_push($datas, $totalData);
                    // 每页多少数据
                    $excel->sheet("页数", function ($sheet) use ($datas) {
                        $sheet->rows($datas);
                    });
                }
            })->export('xls');
        } catch (Exception $e) {

        }
    }

    /**
     * 代练订单统计导出
     * @param  [type] $excelDatas [description]
     * @param  [type] $totalData  [description]
     * @return [type]             [description]
     */
    public function exportOrder($excelDatas, $totalData)
    {
        try {
            // 标题
            $title = [
                '发布时间',
                '发布单数',
                '被接单数',
                '已结算单数',
                '已结算占比',
                '已撤销单数',
                '已仲裁单数',
                '已结算/撤销/仲裁来源价格',
                '已结算单发单金额',
                '撤销/仲裁支付金额',
                '撤销/仲裁获得赔偿',
                '手续费',
                '利润',
            ];

            $totalData = [
            	'总计',
            	$totalData->total_send_order_count,
			 	$totalData->total_receive_order_count, 
			 	$totalData->total_complete_order_count,
			 	$totalData->total_complete_order_rate, 
			 	$totalData->total_revoke_order_count, 
				$totalData->total_arbitrate_order_count,
				$totalData->total_three_status_original_amount,
				$totalData->total_complete_order_amount,
				$totalData->total_two_status_payment,
				$totalData->total_two_status_income,
				$totalData->total_poundage,
				$totalData->total_profit,
            ];
            // 
            $chunkDatas = array_chunk(array_reverse($excelDatas->toArray()), 500);

            Excel::create('订单统计', function ($excel) use ($chunkDatas, $title, $totalData) {
                foreach ($chunkDatas as $chunkData) {
                    // 内容
                    $datas = [];
                    foreach ($chunkData as $key => $data) {
                    	$user = User::find($data['user_id']);
                        $datas[] = [
                            $data['date'] ?? '--',
                            $data['send_order_count'] ?? '--',
						 	$data['receive_order_count'] ?? '--', 
						 	$data['complete_order_count'] ?? '--',
						 	$data['complete_order_rate'] ?? '--', 
						 	$data['revoke_order_count'] ?? '--', 
							$data['arbitrate_order_count'] ?? '--',
							$data['three_status_original_amount'] ?? '--',
							$data['complete_order_amount'] ?? '--',
							$data['two_status_payment'] ?? '--',
							$data['two_status_income'] ?? '--',
							$data['poundage'] ?? '--',
							$data['profit'] ?? '--',
                        ];
                    }
                    // 将标题加入到数组
                    array_unshift($datas, $title);
                    array_push($datas, $totalData);
                    // 每页多少数据
                    $excel->sheet("页数", function ($sheet) use ($datas) {
                        $sheet->rows($datas);
                    });
                }
            })->export('xls');
        } catch (Exception $e) {
            
        }
    }
}


