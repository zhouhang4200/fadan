<?php

namespace App\Http\Controllers\Frontend;

use App\Models\LoginHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginRecordController extends Controller
{
    /**
     * 登录详情 查找
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = $request->userId;

        $name = $request->name;

        $startDate = $request->startDate;

        $endData = $request->endDate;

        $filters = compact('userId', 'name', 'startDate', 'endDate');

        $datas = LoginHistory::filter()->paginate(config('frontend.page'));

        // return view(, compact('datas', 'userId', 'name', 'startDate', 'endDate'));
    }
}
