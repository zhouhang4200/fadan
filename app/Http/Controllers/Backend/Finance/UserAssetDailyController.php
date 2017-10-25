<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\UserAssetDailyRepository;
// use App\Extensions\Excel\ExportUserAssetDaily;

class UserAssetDailyController extends Controller
{
    public function index(Request $request, UserAssetDailyRepository $userAssetDailyRepository)
    {
        $userId    = $request->user_id;
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        $dataList = $userAssetDailyRepository->getList($userId, $dateStart, $dateEnd);

        return view('backend.finance.user-asset-daily.index', compact('dataList', 'userId', 'dateStart', 'dateEnd'));
    }

    public function export(Request $request, UserAssetDailyRepository $userAssetDailyRepository, ExportPlatformAssetDaily $excel)
    {
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        $dataList = $userAssetDailyRepository->getList($dateStart, $dateEnd, 0);

        $excel->export($dataList);
    }
}
