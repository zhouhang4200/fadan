<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
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
        $adminUserId = $request->adminUserId;

        $name = $request->name;

        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $filters = compact('adminUserId', 'name', 'startDate', 'endDate');

        $loginRecords = AdminLoginHistory::filter($filters)->paginate(config('frontend.page'));

        return view('backend.loginrecord.index', compact('loginRecords', 'adminUserId', 'name', 'startDate', 'endDate'));
    }
}
