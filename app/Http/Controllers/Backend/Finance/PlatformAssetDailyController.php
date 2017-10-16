<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\PlatformAssetDailyRepository;
use App\Extensions\Excel\ExportPlatformAssetDaily;

class PlatformAssetDailyController extends Controller
{
    public function index(Request $request, PlatformAssetDailyRepository $platformAssetDailyRepository)
    {
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        $dataList = $platformAssetDailyRepository->getList($dateStart, $dateEnd);

        return view('backend.finance.platform-asset-daily.index', compact(
            'dataList',
            'dateStart',
            'dateEnd'
        ));
    }

    public function export(Request $request, PlatformAssetDailyRepository $platformAssetDailyRepository, ExportPlatformAssetDaily $excel)
    {
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        $dataList = $platformAssetDailyRepository->getList($dateStart, $dateEnd, 0);

        $excel->export($dataList);
    }
}
