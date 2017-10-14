<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\UserAsset;
use App\Repositories\Frontend\UserAmountFlowRepository;

class AssetController extends Controller
{
    public function index()
    {
        $asset = UserAsset::find(Auth::user()->id);
        return view('frontend.asset.index', compact('asset'));
    }

    public function flow(Request $request, UserAmountFlowRepository $userAmountFlowRepository)
    {
        $tradeNo   = trim($request->trade_no);
        $tradeType = $request->trade_type;
        $timeStart = $request->time_start;
        $timeEnd   = $request->time_end;

        $dataList = $userAmountFlowRepository->getList($tradeNo, $tradeType, $timeStart, $timeEnd, $pageSize = 20);
        return view('frontend.asset.flow', compact('dataList'));
    }
}
