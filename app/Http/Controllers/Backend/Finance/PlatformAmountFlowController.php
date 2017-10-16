<?php

namespace App\Http\Controllers\Backend\Finance;

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

        $dataList = $platformAmountFlowRepository->getList($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEndDate);

        return view('backend.finance.platform-amount-flow.index', compact(
            'dataList',
            'userId',
            'tradeNo',
            'tradeType',
            'tradeSubtype',
            'timeStart',
            'timeEnd'
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

        $dataList = $platformAmountFlowRepository->getList($userId, $tradeNo, $tradeType, $tradeSubtype, $timeStart, $timeEndDate, 0);

        $excel->export($dataList);
    }
}
