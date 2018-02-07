<?php
namespace App\Http\Controllers\Frontend\Statistic;

use DB;
use Auth;
use Excel;
use Illuminate\Http\Request;
use App\Models\SmsSendRecord;
use App\Http\Controllers\Controller;

/**
 * 短信发送统计
 * Class SmsController
 * @package App\Http\Controllers\Frontend\Statistic
 */
class SmsController extends Controller
{
    /**
     * 按日期统计
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $fullUrl = $request->fullUrl();

        $record = SmsSendRecord::select(DB::raw('id, date, user_id, count(1) as count'))
            ->where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('startDate', 'endDate'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('frontend.statistic.sms.index', compact('record', 'startDate', 'endDate', 'fullUrl'));
    }

    /**
     * 查看详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $orderNo = $request->order_no;
        $clientPhone = $request->client_phone;
        $foreignOrderNo = $request->foreign_order_no;
        $fullUrl = $request->fullUrl();

        $recordDetail = SmsSendRecord::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('startDate', 'endDate', 'orderNo', 'clientPhone', 'foreignOrderNo'))
            ->orderBy('id', 'desc')
            ->where('date', $request->date)
            ->paginate(15);

        return view('frontend.statistic.sms.show', compact('recordDetail', 'orderNo', 'clientPhone', 'foreignOrderNo'));
    }
}
