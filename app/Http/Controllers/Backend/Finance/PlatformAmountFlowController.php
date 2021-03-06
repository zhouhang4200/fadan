<?php

namespace App\Http\Controllers\Backend\Finance;

use App\Models\PlatformAmountFlow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\PlatformAmountFlowRepository;
use App\Extensions\Excel\ExportPlatformAmountFlow;

class PlatformAmountFlowController extends Controller
{
    public function index(Request $request, PlatformAmountFlowRepository $platformAmountFlowRepository)
    {
        $userId       = trim($request->user_id);
        $tradeNo      = trim($request->trade_no);
        $tradeType    = $request->trade_type;
        $tradeSubtype = $request->trade_subtype;
        $timeStart    = $request->time_start;
        $timeEnd      = $request->time_end;
        $timeEndDate  = !empty($timeEnd) ? $timeEnd . ' 23:59:59' : '';
        $fullUrl = $request->fullUrl();

        $dataList = $platformAmountFlowRepository->getList($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEndDate);

        try {
            if (request('export')) {
                set_time_limit(0);

                $filters = compact('userId', 'tradeNo', 'tradeType', 'tradeSubtype', 'timeStart', 'timeEnd');

                $query = PlatformAmountFlow::filter($filters)
                    ->orderBy('id', 'desc');

                export(array(
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
                    '累计用户成交次数',
                    '累计用户成交金额',
                    '时间',
                ), '平台资金流水', $query, function ($query, $out){
                    $query->chunk(1000, function ($chunk) use ($out) {
                        $datas = $chunk->toArray();
                        $tradetypePlatform = config('tradetype.platform');
                        $tradesubtypePlatformSub = config('tradetype.platform_sub');

                        foreach ($datas as $k => $value) {
                            $arr = [
                                $value['id'],
                                $value['user_id'],
                                $value['admin_user_id'],
                                $tradetypePlatform[$value['trade_type']],
                                $tradesubtypePlatformSub[$value['trade_subtype']],
                                $value['trade_no'],
                                $value['fee'] + 0,
                                $value['remark'],
                                $value['amount'] + 0,
                                $value['managed'] + 0,
                                $value['balance'] + 0,
                                $value['frozen'] + 0,
                                $value['total_recharge'] + 0,
                                $value['total_withdraw'] + 0,
                                $value['total_consume'] + 0,
                                $value['total_refund'] + 0,
                                $value['total_trade_quantity'] + 0,
                                $value['total_trade_amount'] + 0,
                                $value['created_at'],
                            ];
                            fputcsv($out, $arr);
                        }
                    });
                });
            }
        } catch (\Exception $e) {
            myLog('test', [$e->getFile(), $e->getLine(), $e->getMessage()]);
        }

        return view('backend.finance.platform-amount-flow.index', compact(
            'dataList',
            'userId',
            'tradeNo',
            'tradeType',
            'tradeSubtype',
            'timeStart',
            'timeEnd',
            'fullUrl'
        ));
    }

    public function export(Request $request, PlatformAmountFlowRepository $platformAmountFlowRepository, ExportPlatformAmountFlow $excel)
    {
        $userId       = trim($request->user_id);
        $tradeNo      = trim($request->trade_no);
        $tradeType    = $request->trade_type;
        $tradeSubtype = $request->trade_subtype;
        $timeStart    = $request->time_start;
        $timeEnd      = $request->time_end;
        $timeEndDate  = !empty($timeEnd) ? $timeEnd . ' 23:59:59' : '';

//        $dataList = $platformAmountFlowRepository->getList($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEndDate, 0);

//        $excel->export($dataList);

    }
}
