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
        $export = $request->export;
        $fullUrl = $request->fullUrl();

        $query = SmsSendRecord::select(DB::raw('id, date, user_id, count(1) as count'))->where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('startDate', 'endDate'))
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if ($export) {
            export(['发送时间', '发送条数'], '短信发送统计', $query,  function ($query, $out){
                $query->chunk(1000, function ($items) use ($out) {
                    $data = $items->toArray();
                    foreach ($data as $k => $v) {
                        $data = [
                            $v['date'],
                            $v['count'],
                        ];
                        fputcsv($out, $data);
                    }
                });
            });
        }
        $record = $query->paginate(20);

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
        $export = $request->export;
        $fullUrl = $request->fullUrl();

        $query = SmsSendRecord::where('user_id', Auth::user()->getPrimaryUserId())
            ->filter(compact('startDate', 'endDate', 'orderNo', 'clientPhone', 'foreignOrderNo'))
            ->orderBy('id', 'desc')
            ->where('date', $request->date);

        if ($export) {
            export(['千手订单号', '外部订单号', '发送手机', '发送内容', '发送时间'], '短信发送详情', $query,  function ($query, $out){
                $query->chunk(1000, function ($items) use ($out) {
                    $data = $items->toArray();
                    foreach ($data as $k => $v) {
                        $sourceOrderNo = '无';
                        if (isset($v['source_order_no']) && !empty($v['source_order_no'])) {
                            $sourceOrderNo = $v['source_order_no'];
                        } elseif (isset($v['foreign_order_no']) && !empty($v['foreign_order_no'])) {
                            $sourceOrderNo = $v['foreign_order_no'];
                        }
                        $data = [
                            $v['order_no'] . "\t",
                            $sourceOrderNo . "\t",
                            $v['client_phone'],
                            $v['contents'],
                            $v['created_at'],
                        ];
                        fputcsv($out, $data);
                    }
                });
            });
        }
        $recordDetail  = $query->paginate(20);

        return view('frontend.statistic.sms.show', compact('recordDetail', 'orderNo', 'clientPhone', 'foreignOrderNo', 'fullUrl'));
    }
}
