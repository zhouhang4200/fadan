<?php

namespace App\Http\Controllers\Frontend\Statistic;

use DB;
use Auth;
use Excel;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmployeeStatistic;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    public function employee(Request $request)
    {
    	$userName = $request->user_name;
    	$startDate = $request->start_date;
    	$endDate = $request->end_date;
    	$children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
    	$fullUrl = $request->fullUrl();
    	$filters = compact('userName', 'startDate', 'endDate');

    	$datas = EmployeeStatistic::where('parent_id', Auth::user()->getPrimaryUserId())
    				->filter($filters)
    				->select(DB::raw('user_id, user_name, name, sum(complete_order_count) as complete_order_count, sum(revoke_order_count) as revoke_order_count, sum(arbitrate_order_count) as arbitrate_order_count, sum(profit) as profit, sum(send_order_amount) as send_order_amount'))
    				->groupBy('user_id')
    				->paginate(config('frontend.page'));

    	$totalData = EmployeeStatistic::where('parent_id', Auth::user()->getPrimaryUserId())
    				->filter($filters)
    				->select(DB::raw('sum(complete_order_count) as total_complete_order_count, sum(revoke_order_count) as total_revoke_order_count, sum(arbitrate_order_count) as total_arbitrate_order_count, sum(profit) as total_profit, sum(send_order_amount) as total_send_order_amount, count(distinct(user_id)) as total_user_id_count'))
    				->first();

    	if ($request->export) {
    		if ($datas->count() < 1) {
    			return redirect(route('frontend.statistic.employee'))->withInput()->with('empty', '数据为空!');
    		}
            return $this->export($filters);
        }

    	return view('frontend.statistic.employee', compact('datas', 'userName', 'startDate', 'endDate', 'children', 'fullUrl', 'totalData'));
    }

    public function order()
    {

    }

    public function price()
    {

    }

    public function message()
    {

    }

    public function export($filters)
    {
        try {
            $datas = EmployeeStatistic::where('parent_id', Auth::user()->getPrimaryUserId())
    				->filter($filters)
    				->select(DB::raw('user_id, user_name, name, sum(complete_order_count) as complete_order_count, sum(revoke_order_count) as revoke_order_count, sum(arbitrate_order_count) as arbitrate_order_count, sum(profit) as profit, sum(send_order_amount) as send_order_amount'))
    				->groupBy('user_id')
    				->get();

            $totalData = EmployeeStatistic::where('parent_id', Auth::user()->getPrimaryUserId())
    				->filter($filters)
    				->select(DB::raw('sum(complete_order_count) as total_complete_order_count, sum(revoke_order_count) as total_revoke_order_count, sum(arbitrate_order_count) as total_arbitrate_order_count, sum(profit) as total_profit, sum(send_order_amount) as total_send_order_amount, count(distinct(user_id)) as total_user_id_count'))
    				->first();
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
                $totalData->total_send_order_amount,
                $totalData->total_revoke_order_count,
                $totalData->total_arbitrate_order_count,
                $totalData->total_profit,
            ];

            $chunkDatas = array_chunk(array_reverse($datas->toArray()), 1000);

            Excel::create(iconv('UTF-8', 'gbk', '奖惩情况'), function ($excel) use ($chunkDatas, $title, $totalData) {

                foreach ($chunkDatas as $chunkData) {
                    // 内容
                    $datas = [];
                    foreach ($chunkData as $key => $data) {
                    	$user = User::find($data['user_id']);
                        $datas[] = [
                            $user->user_name,
                            $data['name'],
                            $data['complete_order_count'] ?? '--',
                            $data['send_order_amount'] ?? '--',
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
}
