<?php

namespace App\Http\Controllers\Frontend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Repositories\Frontend\UserAssetDailyRepository;

class AssetDailyController extends Controller
{
    public function index(Request $request, UserAssetDailyRepository $userAmountFlowRepository)
    {
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        $dataList = $userAmountFlowRepository->getList($dateStart, $dateEnd);

        return view('frontend.finance.asset-daily.index', compact('dataList', 'dateStart', 'dateEnd'));
    }
}
