<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
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
        $user = Auth::user();

        if ($user->pid == 0) {

            $name = $request->name;

            $userIds = User::where('name', 'like', "%{$name}%")->where('pid', $user->id)->pluck('id')->toArray();

            $startDate = $request->startDate;

            $endDate = $request->endDate;

            $filters = compact('name', 'userIds', 'startDate', 'endDate');

            $loginRecords = LoginHistory::filter($filters)->where('pid', $user->id)->paginate(config('frontend.page'));

            return view('frontend.loginrecord.index', compact('loginRecords', 'name', 'startDate', 'endDate'));
        }
    }
}
