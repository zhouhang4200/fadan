<?php

namespace App\Http\Controllers\Backend\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Backend\UserAssetRepository;

class UserAssetController extends Controller
{
    public function index(Request $request, UserAssetRepository $userAssetRepository)
    {
        $userId = trim($request->user_id);

        $dataList = $userAssetRepository->getList($userId);

        return view('backend.finance.user-asset.index', compact('dataList', 'userId'));
    }
}
