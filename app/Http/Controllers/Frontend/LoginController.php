<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
	/**
	 * 主账号或者子账号登陆记录
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function history(Request $request)
    {
    	$user = Auth::user();

        $startDate = $request->startDate;

        $endDate = $request->endDate;

        $filters = compact('startDate', 'endDate');

        $histories = LoginHistory::filter($filters)->where('user_id', $user->id)->paginate(config('frontend.page'));

        return view('frontend.login.history', compact('histories', 'startDate', 'endDate'));
    }

    public function childHistory(Request $request)
    {
    	$user = Auth::user();

        if ($user->pid == 0) {

            $name = $request->name;

            $userIds = User::where('name', 'like', "%{$name}%")->where('pid', $user->id)->pluck('id')->toArray();

            $startDate = $request->startDate;

            $endDate = $request->endDate;

            $filters = compact('name', 'userIds', 'startDate', 'endDate');

            $loginRecords = LoginHistory::filter($filters)->where('pid', $user->id)->paginate(config('frontend.page'));

            return view('frontend.login.index', compact('loginRecords', 'name', 'startDate', 'endDate'));
        }

    }
}
