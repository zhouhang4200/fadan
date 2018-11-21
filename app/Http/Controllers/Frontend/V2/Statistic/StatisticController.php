<?php

namespace App\Http\Controllers\Frontend\V2\Statistic;

use DB;
use Auth;
use App\Models\User;
use App\Models\SmsSendRecord;
use App\Models\OrderStatistic;
use App\Models\EmployeeStatistic;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class StatisticController extends Controller
{
    /**
     * 员工统计
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employee()
    {
        return view('frontend.v2.statistic.employee');
    }

    /**
     * 员工统计接口
     *
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
     *
     * @return mixed
     */
    public function employeeUser()
    {
        return User::whereIn('id', User::where('parent_id', Auth::user()->getPrimaryUserId())
            ->pluck('id')
            ->merge(Auth::user()->getPrimaryUserId())->unique())
            ->get()
            ->toArray();
    }

    /**
     * 订单统计
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order()
    {
        return view('frontend.v2.statistic.order');
    }

    /**
     * 订单统计接口
     *
     * @return mixed
     */
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

    /**
     * 短信统计
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function message()
    {
        return view('frontend.v2.statistic.message');
    }

    /**
     * 短信统计接口
     *
     * @return LengthAwarePaginator
     */
    public function messageDataList()
    {
        $startDate = request('date')[0];
        $endDate = request('date')[1];

        $smsSendRecords = SmsSendRecord::select(DB::raw('id, date, user_id, count(1) as count'))->where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('startDate', 'endDate'))
            ->orderBy('date', 'desc')
            ->groupBy('date')
            ->offset((request('page', 1)-1)*15)
            ->limit(15)
            ->get();

        $pageCount = $smsSendRecords->sum('count');

        $count = SmsSendRecord::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('startDate', 'endDate'))
            ->groupBy('date')
            ->get()
            ->count();

        $smsSendRecords = $smsSendRecords->toArray();

        array_push($smsSendRecords, ['id' => 0, 'user_id' => 0, 'date' => '总计', 'count' => $pageCount]);

        return new LengthAwarePaginator($smsSendRecords, $count, 15, request('page', 1), [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * 短信详情页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function messageShow()
    {
        $date = request('date');

        return view('frontend.v2.statistic.message-show', compact('date'));
    }

    /**
     * 短信详情接口
     *
     * @return mixed
     */
    public function messageShowDataList()
    {
        $orderNo = request('order_no');
        $clientPhone = request('client_phone');

        return SmsSendRecord::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('orderNo', 'clientPhone'))
            ->where('date', request('date'))
            ->orderBy('id', 'desc')
            ->paginate(15);
    }
}
