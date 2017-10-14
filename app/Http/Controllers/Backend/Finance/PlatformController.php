<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlatformAsset;
use App\Repositories\Backend\PlatformAmountFlowRepository;

class PlatformController extends Controller
{
    public function asset()
    {
        $platformAsset = PlatformAsset::find(1);
        return view('backend.finance.platform.asset', compact('platformAsset'));
    }

    public function flow(Request $request, PlatformAmountFlowRepository $platformAmountFlowRepository)
    {
        $userId       = trim($request->user_id);
        $tradeNo      = trim($request->trade_no);
        $tradeType    = $request->tradeType;
        $tradeSubType = $request->tradeSubType;
        $timeStart    = $request->timeStart;
        $timeEnd      = $request->timeEnd;

        $dataList = $platformAmountFlowRepository->getList($userId, $tradeNo, $tradeType, $tradeSubType, $timeStart, $timeEnd);

        return view('backend.finance.platform.flow', compact('dataList'));
    }
}
