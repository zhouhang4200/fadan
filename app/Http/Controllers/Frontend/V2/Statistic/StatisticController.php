<?php

namespace App\Http\Controllers\Frontend\V2\Statistic;

use DB;
use Auth;
use App\Models\User;
use App\Models\OrderStatistic;
use App\Models\EmployeeStatistic;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    /**
     * 员工统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employee()
    {
        return view('frontend.v2.statistic.employee');
    }

    /**
     * 员工统计接口
     */
    public function employeeDataList()
    {
        $userName = request('username', '');
        $startDate = request('date')[0];
        $endDate = request('date')[1];

        $filter = compact('userName', 'startDate', 'endDate');

        $userIds = User::whereIn('id', User::where('parent_id', Auth::user()->getPrimaryUserId())->pluck('id')->merge(Auth::user()->getPrimaryUserId())->unique())
            ->pluck('id');

        $query = EmployeeStatistic::whereIn('employee_statistics.user_id', $userIds)
            ->filter($filter)
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

        return $query->paginate(15);
    }

    /**
     * 员工统计下所有的员工和主账号
     * @return mixed
     */
    public function employeeUser()
    {
        return User::whereIn('id', User::where('parent_id', Auth::user()->getPrimaryUserId())->pluck('id')->merge(Auth::user()->getPrimaryUserId())->unique())->get()->toArray();
    }

    /**
     * 订单统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order()
    {
        return view('frontend.v2.statistic.order');
    }

    public function orderDataList()
    {
        $startDate = request('date')[0];
        $endDate = request('date')[1];
        $userIds = User::whereIn('id', User::where('parent_id', Auth::user()->getPrimaryUserId())->pluck('id')->merge(Auth::user()->getPrimaryUserId())->unique())
            ->pluck('id');

        $filter = compact( 'startDate', 'endDate');
        return OrderStatistic::whereIn('user_id', $userIds)
            ->filter($filter)
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
            ->groupBy('date')
            ->paginate(15);
    }
}
