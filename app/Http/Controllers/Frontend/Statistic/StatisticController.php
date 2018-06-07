<?php

namespace App\Http\Controllers\Frontend\Statistic;

use DB;
use Cache;
use Auth;
use Excel;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrderStatistic;
use App\Models\EmployeeStatistic;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Models\OrderBasicData;

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
                sum(employee_statistics.complete_order_amount) as complete_order_amount,
                sum(all_count) as all_count,
                sum(all_original_price) as all_original_price,
                sum(all_price) as all_price,
                sum(subtract_price) as subtract_price
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
                count(distinct(user_id)) as total_user_id_count,
                sum(all_count) as all_count,
                sum(all_original_price) as all_original_price,
                sum(all_price) as all_price,
                sum(subtract_price) as subtract_price
            '))
			->first();

    	if ($request->export) {
    		if ($datas->count() < 1) {
    			return redirect(route('frontend.statistic.employee'))->withInput()->with('empty', '数据为空!');
    		}
            return $this->exportEmployee($excelDatas, $totalData);
        }

    	return view('frontend.v1.finance.statistic.employee', compact('datas', 'userName', 'startDate', 'endDate', 'children', 'fullUrl', 'totalData', 'parent'));
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

    	return view('frontend.v1.finance.statistic.order', compact('datas', 'startDate', 'endDate', 'fullUrl', 'totalData'));
    }

    /**
     * 统计导出
     * @param  [type] $excelDatas [description]
     * @param  [type] $totalData  [description]
     * @return [type]             [description]
     */
    public function exportEmployee($excelDatas, $totalData)
    {
        // try {
            // 标题
            $title = [
                '员工姓名',
                '发布数量',
                '来源价格',
                '发布价格',
                '来源/发布差价',
            ];

            $totalData = [
            	'总计',
                $totalData->count,
                $totalData->original_price,
                $totalData->price,
                $totalData->diff_price,
            ];

            $chunkDatas = array_chunk(array_reverse($excelDatas), 500);

            Excel::create('员工统计', function ($excel) use ($chunkDatas, $title, $totalData) {
                foreach ($chunkDatas as $chunkData) {
                    // 内容
                    $datas = [];
                    foreach ($chunkData as $key => $data) {
                        $datas[] = [
                            $data->username,
                            $data->count,
                            $data->original_price,
                            $data->price,
                            $data->diff_price,
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
        // } catch (\Exception $e) {

        // }
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
        } catch (\Exception $e) {
            
        }
    }

    /**
     * 员工每日数据详情
     * @return [type] [description]
     */
    public function todayData(Request $request)
    {
        $userId = $request->user_id;
        $startDate = $request->start_date ?? Carbon::now()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->toDateString();
        $fullUrl = $request->fullUrl();
        $children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();

        if (Auth::user()->parent_id == 0) {
            $parent = Auth::user();
            $userIds = Auth::user()->children()->pluck('id')->merge(Auth::id())->toArray();
            $userIds = implode(',', $userIds);
        } else {
            $parent = Auth::user()->parent;
            $userIds = Auth::user()->parent->children()->pluck('id')->merge(Auth::user()->parent->id)->toArray();
            $userIds = implode(',', $userIds);
        }

        $endAddDate = Carbon::parse($endDate)->addDays(1)->toDateString();
        $timeSql = "a.order_created_at >= '$startDate' AND a.order_created_at < '$endAddDate'";

        $userSql = '';
        $totalSql = "and a.creator_user_id in ($userIds)";

        if (isset($userId) && !empty($userId)) {
            $userSql = "and a.creator_user_id = '$userId'";
            $totalSql = "and a.creator_user_id = '$userId'";
        }

        $userDatas = DB::select("select 
            count(a.order_no) as count,
            Sum(a.price) as price,
            sum(a.original_price) as original_price,
            sum(a.original_price-price) as  diff_price,
            a.creator_user_id,
            b.username
            from order_basic_datas a
            left join users b
            on a.creator_user_id = b.id
            where a.is_repeat != 1 
            and ".$timeSql.$userSql."
            group by a.creator_user_id
        ");

        $totalDatas = DB::select("select 
            count(distinct a.creator_user_id) as creator_count,
            count(a.order_no) as count,
            Sum(a.price) as price,
            sum(a.original_price) as original_price,
            sum(a.original_price-price) as  diff_price,
            a.creator_user_id,
            b.username
            from order_basic_datas a
            left join users b
            on a.creator_user_id = b.id
            where a.is_repeat != 1 
            and ".$timeSql.$totalSql."
        ");

      //   $totalQuery = "SELECT 
      //       m.no,
      //       m.date,
      //       m.status,
      //       m.price,
      //       m.creator_user_id,
      //       m.creator_primary_user_id,
      //       m.name,
      //       m.username,
      //       m.game_id,
      //       m.game_name,
      //       m.original_price,
      //       m.created_at,
      //       m.field_value,
      //       m.field_name,
      //       SUM(CASE WHEN m.status = 20 THEN 1 ELSE 0 END) AS complete_count,
      //       SUM(CASE WHEN m.status = 20 THEN m.price ELSE 0 END) AS complete_price,
      //       SUM(CASE WHEN m.status = 19 THEN 1 ELSE 0 END) AS revoked_count,
      //       SUM(CASE WHEN m.status = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
      //       m.original_price-m.price AS diff_price,
      //       SUM(CASE WHEN m.trade_subtype = 76 THEN m.fee ELSE 0 END) AS create_order_pay_amount,
      //       SUM(CASE WHEN m.trade_subtype = 77 THEN m.fee ELSE 0 END) AS change_order_pay_amount,
      //       SUM(CASE WHEN m.trade_subtype = 814 THEN m.fee ELSE 0 END) AS reback_order_income_amount,
      //       SUM(CASE WHEN m.trade_subtype = 813 THEN m.fee ELSE 0 END) AS delete_order_income_amount,
      //       SUM(CASE WHEN m.trade_subtype = 79 THEN m.fee ELSE 0 END) AS complain_order_pay_amount,
      //       SUM(CASE WHEN m.trade_subtype = 817 THEN m.fee ELSE 0 END) AS complain_order_income_amount,
      //       SUM(CASE WHEN m.trade_subtype = 87 AND m.status IN (19, 21) THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_order_price,
      //       SUM(CASE WHEN m.trade_subtype BETWEEN 810 AND 811 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_deposit,
      //       SUM(CASE WHEN m.trade_subtype = 73 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_pay_poundage
      //     FROM 
      //     ( SELECT a.no, a.created_at, game_id, a.status, a.price, a.creator_user_id, a.creator_primary_user_id, DATE_FORMAT(a.created_at, '%Y-%m-%d') AS DATE,
      //       b.name, b.username, a.original_price, e.name AS game_name, oc.field_value, oc.field_name, d.trade_subtype, d.fee, d.trade_no
      //       FROM orders a
      //       LEFT JOIN users b
      //       ON a.creator_user_id = b.id
      //       LEFT JOIN games e
      //       ON a.game_id = e.id 
      //       LEFT JOIN order_details oc
      //       ON  a.no = oc.order_no
      //       LEFT JOIN 
      //       user_amount_flows d 
      //       ON a.no = d.trade_no AND a.creator_primary_user_id = d.user_id
      //       WHERE a.service_id = 4 AND ".$timeSql."
      //       AND oc.field_name='is_repeat' AND oc.field_value != 1
      //       AND a.creator_user_id IN ($userIds)
      //           ) m
      //       GROUP BY m.no";

      //       $userQuery = "SELECT 
      //       m.no,
      //       m.date,
      //       m.status,
      //       m.price,
      //       m.creator_user_id,
      //       m.creator_primary_user_id,
      //       m.name,
      //       m.username,
      //       m.game_id,
      //       m.game_name,
      //       m.original_price,
      //       m.created_at,
      //       m.field_value,
      //       m.field_name,
      //       SUM(CASE WHEN m.status = 20 THEN 1 ELSE 0 END) AS complete_count,
      //       SUM(CASE WHEN m.status = 20 THEN m.price ELSE 0 END) AS complete_price,
      //       SUM(CASE WHEN m.status = 19 THEN 1 ELSE 0 END) AS revoked_count,
      //       SUM(CASE WHEN m.status = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
      //       m.original_price-m.price AS diff_price,
      //       SUM(CASE WHEN m.trade_subtype = 76 THEN m.fee ELSE 0 END) AS create_order_pay_amount,
      //       SUM(CASE WHEN m.trade_subtype = 77 THEN m.fee ELSE 0 END) AS change_order_pay_amount,
      //       SUM(CASE WHEN m.trade_subtype = 814 THEN m.fee ELSE 0 END) AS reback_order_income_amount,
      //       SUM(CASE WHEN m.trade_subtype = 813 THEN m.fee ELSE 0 END) AS delete_order_income_amount,
      //       SUM(CASE WHEN m.trade_subtype = 79 THEN m.fee ELSE 0 END) AS complain_order_pay_amount,
      //       SUM(CASE WHEN m.trade_subtype = 817 THEN m.fee ELSE 0 END) AS complain_order_income_amount,
      //       SUM(CASE WHEN m.trade_subtype = 87 AND m.status IN (19, 21) THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_order_price,
      //       SUM(CASE WHEN m.trade_subtype BETWEEN 810 AND 811 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_deposit,
      //       SUM(CASE WHEN m.trade_subtype = 73 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_pay_poundage
      // FROM 
      // ( SELECT a.no, a.created_at, game_id, a.status, a.price, a.creator_user_id, a.creator_primary_user_id, DATE_FORMAT(a.created_at, '%Y-%m-%d') AS DATE,
      //   b.name, b.username, a.original_price, e.name AS game_name, oc.field_value, oc.field_name, d.trade_subtype, d.fee, d.trade_no
      //   FROM orders a
      //   LEFT JOIN users b
      //   ON a.creator_user_id = b.id
      //   LEFT JOIN games e
      //   ON a.game_id = e.id 
      //   LEFT JOIN order_details oc
      //   ON  a.no = oc.order_no
      //   LEFT JOIN user_amount_flows d 
      //   ON a.no = d.trade_no AND a.creator_primary_user_id = d.user_id
      //   WHERE a.service_id = 4 AND ".$timeSql."
      //   AND oc.field_name='is_repeat' AND oc.field_value != 1
      //   AND a.creator_user_id = $userId
      //       ) m
      //   GROUP BY m.no";

      //   $userDatas = DB::select("select z.username, count(z.no) as count, sum(original_price) as original_price,
      //       sum(price) as price, sum(diff_price) as diff_price, sum(complete_count) as complete_count, 
      //       sum(complete_price) as complete_price, sum(revoked_count) as revoked_count, sum(arbitrationed_count) as arbitrationed_count, case when z.status in (19, 20, 21) then sum(diff_price+revoked_and_arbitrationed_return_order_price+revoked_and_arbitrationed_return_deposit+revoked_and_arbitrationed_pay_poundage) else 0 end as profit from (".$userQuery.") z");

      //   $totalDatas = DB::select("select count(z.creator_user_id) as creator_count, count(z.no) as count, sum(original_price) as original_price,
      //       sum(price) as price, sum(diff_price) as diff_price, sum(complete_count) as complete_count, 
      //       sum(complete_price) as complete_price, sum(revoked_count) as revoked_count, sum(arbitrationed_count) as arbitrationed_count, case when z.status in (19, 20, 21) then sum(diff_price+revoked_and_arbitrationed_return_order_price+revoked_and_arbitrationed_return_deposit+revoked_and_arbitrationed_pay_poundage) else 0 end as profit from (".$totalQuery.") z group by z.creator_user_id");

        if (isset($totalDatas) && ! empty($totalDatas) && count($totalDatas) > 0) {
            $totalDatas = $totalDatas[0];
        }
        if ($request->export) {
            if (! isset($totalDatas) || empty($totalDatas)) {
                return redirect(route('frontend.statistic.employee'))->withInput()->with('empty', '数据为空!');
            }

            return $this->exportEmployee($userDatas, $totalDatas);
        }

        return view('frontend.v1.finance.employee.today', compact('statuses', 'status', 'datas', 'startDate', 'endDate', 'fullUrl', 'children', 'userId', 'userDatas', 'count', 'page', 'totalDatas', 'parent'));
    }

    /* 员工每日数据详情
     * @return [type] [description]
     */
    public function todayData2(Request $request)
    {
        $userId = $request->user_id ?? Auth::user()->id;
        $startDate = $request->start_date ?? Carbon::now()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->toDateString();
        $fullUrl = $request->fullUrl();
        $children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();

        if (Auth::user()->parent_id == 0) {
            $parent = Auth::user();
            $userIds = Auth::user()->children()->pluck('id')->merge(Auth::id())->toArray();
            $userIds = implode(',', $userIds);
        } else {
            $parent = Auth::user()->parent;
            $userIds = Auth::user()->parent->children()->pluck('id')->merge(Auth::user()->parent->id)->toArray();
            $userIds = implode(',', $userIds);
        }

        $timeSql = "a.created_at >= '$startDate' AND a.created_at < '$endDate'";

        if ($startDate == $endDate) {
            $endAddDate = Carbon::parse($endDate)->addDays(1)->toDateString();
            $timeSql = "a.created_at >= '$startDate' AND a.created_at < '$endAddDate'";
        }


        $totalQuery = "SELECT 
            m.no,
            m.date,
            m.status,
            m.price,
            m.creator_user_id,
            m.creator_primary_user_id,
            m.name,
            m.username,
            m.game_id,
            m.game_name,
            m.original_price,
            m.created_at,
            m.field_value,
            m.field_name,
            SUM(CASE WHEN m.status = 20 THEN 1 ELSE 0 END) AS complete_count,
            SUM(CASE WHEN m.status = 20 THEN m.price ELSE 0 END) AS complete_price,
            SUM(CASE WHEN m.status = 19 THEN 1 ELSE 0 END) AS revoked_count,
            SUM(CASE WHEN m.status = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
            m.original_price-m.price AS diff_price,
            SUM(CASE WHEN m.trade_subtype = 76 THEN m.fee ELSE 0 END) AS create_order_pay_amount,
            SUM(CASE WHEN m.trade_subtype = 77 THEN m.fee ELSE 0 END) AS change_order_pay_amount,
            SUM(CASE WHEN m.trade_subtype = 814 THEN m.fee ELSE 0 END) AS reback_order_income_amount,
            SUM(CASE WHEN m.trade_subtype = 813 THEN m.fee ELSE 0 END) AS delete_order_income_amount,
            SUM(CASE WHEN m.trade_subtype = 79 THEN m.fee ELSE 0 END) AS complain_order_pay_amount,
            SUM(CASE WHEN m.trade_subtype = 817 THEN m.fee ELSE 0 END) AS complain_order_income_amount,
            SUM(CASE WHEN m.trade_subtype = 87 AND m.status IN (19, 21) THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_order_price,
            SUM(CASE WHEN m.trade_subtype BETWEEN 810 AND 811 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_deposit,
            SUM(CASE WHEN m.trade_subtype = 73 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_pay_poundage
          FROM 
          ( SELECT a.no, a.created_at, game_id, a.status, a.price, a.creator_user_id, a.creator_primary_user_id, DATE_FORMAT(a.created_at, '%Y-%m-%d') AS DATE,
            b.name, b.username, a.original_price, e.name AS game_name, oc.field_value, oc.field_name, d.trade_subtype, d.fee, d.trade_no
            FROM orders a
            LEFT JOIN users b
            ON a.creator_user_id = b.id
            LEFT JOIN games e
            ON a.game_id = e.id 
            LEFT JOIN order_details oc
            ON  a.no = oc.order_no
            LEFT JOIN 
            user_amount_flows d 
            ON a.no = d.trade_no AND a.creator_primary_user_id = d.user_id
            WHERE a.service_id = 4 AND ".$timeSql."
            AND oc.field_name='is_repeat' AND oc.field_value != 1
            AND a.creator_user_id IN ($userIds)
                ) m
            GROUP BY m.no";

            $userQuery = "SELECT 
            m.no,
            m.date,
            m.status,
            m.price,
            m.creator_user_id,
            m.creator_primary_user_id,
            m.name,
            m.username,
            m.game_id,
            m.game_name,
            m.original_price,
            m.created_at,
            m.field_value,
            m.field_name,
            SUM(CASE WHEN m.status = 20 THEN 1 ELSE 0 END) AS complete_count,
            SUM(CASE WHEN m.status = 20 THEN m.price ELSE 0 END) AS complete_price,
            SUM(CASE WHEN m.status = 19 THEN 1 ELSE 0 END) AS revoked_count,
            SUM(CASE WHEN m.status = 21 THEN 1 ELSE 0 END) AS arbitrationed_count,
            m.original_price-m.price AS diff_price,
            SUM(CASE WHEN m.trade_subtype = 76 THEN m.fee ELSE 0 END) AS create_order_pay_amount,
            SUM(CASE WHEN m.trade_subtype = 77 THEN m.fee ELSE 0 END) AS change_order_pay_amount,
            SUM(CASE WHEN m.trade_subtype = 814 THEN m.fee ELSE 0 END) AS reback_order_income_amount,
            SUM(CASE WHEN m.trade_subtype = 813 THEN m.fee ELSE 0 END) AS delete_order_income_amount,
            SUM(CASE WHEN m.trade_subtype = 79 THEN m.fee ELSE 0 END) AS complain_order_pay_amount,
            SUM(CASE WHEN m.trade_subtype = 817 THEN m.fee ELSE 0 END) AS complain_order_income_amount,
            SUM(CASE WHEN m.trade_subtype = 87 AND m.status IN (19, 21) THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_order_price,
            SUM(CASE WHEN m.trade_subtype BETWEEN 810 AND 811 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_return_deposit,
            SUM(CASE WHEN m.trade_subtype = 73 THEN m.fee ELSE 0 END) AS revoked_and_arbitrationed_pay_poundage
      FROM 
      ( SELECT a.no, a.created_at, game_id, a.status, a.price, a.creator_user_id, a.creator_primary_user_id, DATE_FORMAT(a.created_at, '%Y-%m-%d') AS DATE,
        b.name, b.username, a.original_price, e.name AS game_name, oc.field_value, oc.field_name, d.trade_subtype, d.fee, d.trade_no
        FROM orders a
        LEFT JOIN users b
        ON a.creator_user_id = b.id
        LEFT JOIN games e
        ON a.game_id = e.id 
        LEFT JOIN order_details oc
        ON  a.no = oc.order_no
        LEFT JOIN user_amount_flows d 
        ON a.no = d.trade_no AND a.creator_primary_user_id = d.user_id
        WHERE a.service_id = 4 AND ".$timeSql."
        AND oc.field_name='is_repeat' AND oc.field_value != 1
        AND a.creator_user_id = $userId
            ) m
        GROUP BY m.no";

        $userDatas = DB::select("select z.username, count(z.no) as count, sum(original_price) as original_price,
            sum(price) as price, sum(diff_price) as diff_price, sum(complete_count) as complete_count, 
            sum(complete_price) as complete_price, sum(revoked_count) as revoked_count, sum(arbitrationed_count) as arbitrationed_count, case when z.status in (19, 20, 21) then sum(diff_price+revoked_and_arbitrationed_return_order_price+revoked_and_arbitrationed_return_deposit+revoked_and_arbitrationed_pay_poundage) else 0 end as profit from (".$userQuery.") z");

        $totalDatas = DB::select("select count(z.creator_user_id) as creator_count, count(z.no) as count, sum(original_price) as original_price,
            sum(price) as price, sum(diff_price) as diff_price, sum(complete_count) as complete_count, 
            sum(complete_price) as complete_price, sum(revoked_count) as revoked_count, sum(arbitrationed_count) as arbitrationed_count, case when z.status in (19, 20, 21) then sum(diff_price+revoked_and_arbitrationed_return_order_price+revoked_and_arbitrationed_return_deposit+revoked_and_arbitrationed_pay_poundage) else 0 end as profit from (".$totalQuery.") z group by z.creator_user_id");

        if (isset($totalDatas) && ! empty($totalDatas) && count($totalDatas) > 0) {
            $totalDatas = $totalDatas[0];
        }
        if ($request->export) {
            if (! isset($totalDatas) || empty($totalDatas)) {
                return redirect(route('frontend.statistic.employee'))->withInput()->with('empty', '数据为空!');
            }

            return $this->exportEmployee($userDatas, $totalDatas);
        }

        return view('frontend.v1.finance.employee.today', compact('statuses', 'status', 'datas', 'startDate', 'endDate', 'fullUrl', 'children', 'userId', 'userDatas', 'count', 'page', 'totalDatas', 'parent'));
    }
}


