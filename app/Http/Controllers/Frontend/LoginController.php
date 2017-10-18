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

    /**
     * 子账号登录历史
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function child(Request $request)
    {
    	$user = Auth::user();

        if ($user->pid == 0) {

            $users = $user->children;

            $name = $request->name;

            $startDate = $request->startDate;

            $endDate = $request->endDate;

            $pid = $user->id;

            $filters = compact('name', 'startDate', 'endDate', 'pid');

            $histories = LoginHistory::childFilter($filters)->paginate(config('frontend.page'));

            return view('frontend.login.child', compact('histories', 'users', 'name', 'startDate', 'endDate'));
        }

    }
}
