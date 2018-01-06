<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\UserAssetDailyRepository;

class UserAssetDailyController extends Controller
{
    /**
     * @param Request $request
     * @param UserAssetDailyRepository $userAssetDailyRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, UserAssetDailyRepository $userAssetDailyRepository)
    {
        $userId    = $request->user_id;
        $dateStart = $request->date_start;
        $dateEnd   = $request->date_end;

        if ($request->export == 1) {
            $userAssetDailyRepository->export($userId, $dateStart, $dateEnd);
        }

        $dataList = $userAssetDailyRepository->getList($userId, $dateStart, $dateEnd);

        return view('backend.finance.user-asset-daily.index', compact('dataList', 'userId', 'dateStart', 'dateEnd'));
    }
}
