<?php

namespace App\Http\Controllers\Frontend\V2\Statistic;

use App\Models\OrderBasicData;
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
     * @return mixed
     */
    public function employeeDataList()
    {
        $userId = request('username', '');
        $startDate = request('date')[0] ?? null;
        $endDate = request('date')[1] ?? null;

        $parentUser = User::find(Auth::user()->getPrimaryUserId());

        $userIds = User::where('parent_id', $parentUser->id)->pluck('id')->merge($parentUser->id);

        $filter = compact('userId', 'startDate', 'endDate');

        $orderBasicData = OrderBasicData::filter($filter)
            ->whereIn('creator_user_id', $userIds)
            ->select(DB::raw('
                order_basic_datas.creator_user_id, 
                users.name as name,
                users.username as username,
                sum(1) as order_count,
                sum(order_basic_datas.original_price) as original_price, 
                sum(order_basic_datas.price) as price, 
                sum(order_basic_datas.original_price) - sum(order_basic_datas.price) as diff_price
            '))
            ->leftJoin('users', 'users.id', '=', 'order_basic_datas.creator_user_id')
            ->groupBy('order_basic_datas.creator_user_id')
            ->paginate(15);

        return $orderBasicData;
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
        $startDate = request('date')[0] ?? null;
        $endDate = request('date')[1] ?? null;

        $filter = compact('startDate', 'endDate');

        $parentUser = User::find(Auth::user()->getPrimaryUserId());

        $userIds = User::where('parent_id', $parentUser->id)->pluck('id')->merge($parentUser->id);

        // 统计数据
        $orderBasicData = OrderBasicData::where('creator_user_id', Auth::id())
            ->whereIn('creator_user_id', $userIds)
            ->filter($filter)
            ->select(DB::raw('
                date,
                creator_user_id,
                sum(1) as send_order_count,
			 	sum(case when status = 13 then 1 else 0 end) as receive_order_count, 
			 	sum(case when status = 20 then 1 else 0 end) as complete_order_count,
			 	sum(case when status = 19 then 1 else 0 end) as revoke_order_count, 
				sum(case when status = 21 then 1 else 0 end) as arbitrate_order_count,
				sum(case when status in (19, 20, 21) then original_price else 0 end) as three_status_original_amount,
				sum(case when status = 20 then price else 0 end) as complete_order_amount,
				sum(case when status in (19, 21) then consult_amount else 0 end) as two_status_payment,
				sum(case when status in (19, 21) then consult_deposit else 0 end) as two_status_income,
				sum(case when status in (19, 21) then consult_poundage else 0 end) as poundage,
				sum(case when status = 20 then original_price - price else 0 end) - 
				sum(case when status in (19, 21) then consult_amount-consult_poundage-consult_deposit else 0 end) as profit 
            '))
            ->latest('date')
            ->groupBy('date')
            ->paginate(15);

        foreach ($orderBasicData as $item) {
            $item->complete_order_rate = $item->receive_order_count == 0 ? 0 : bcdiv($item->complete_order_count*100, $item->receive_order_count, 2);
        }

        return $orderBasicData;
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
        $startDate = request('date')[0] ?? '';
        $endDate = request('date')[1] ?? '';

        $smsSendRecords = SmsSendRecord::select(DB::raw('id, date, user_id, count(1) as count'))
            ->where('user_id', Auth::user()->getPrimaryUserId())
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
            'path' => Paginator::resolveCurrentPath(),
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
