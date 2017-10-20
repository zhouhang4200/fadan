<?php

namespace App\Http\Controllers\Backend\Account;

use Auth;
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
        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $filters = compact('startDate', 'endDate');

        $loginRecords = AdminLoginHistory::filter($filters)->where('admin_user_id', Auth::id())->paginate(config('frontend.page'));

        return view('backend.account.loginrecord.index', compact('loginRecords', 'startDate', 'endDate'));
    }
}
