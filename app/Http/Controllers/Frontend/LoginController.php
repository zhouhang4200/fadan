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

        if ($user->parent_id == 0) {

            $name = $request->name;

            $users = $user->children;

            $pid = $user->id;

            if ($name) {

                $filters = compact('name', 'startDate', 'endDate', 'pid');

                $histories = LoginHistory::childFilter($filters)->paginate(config('frontend.page'));

                return view('frontend.v1.user.login.history', compact('user', 'histories', 'users', 'name', 'startDate', 'endDate'));
            } else {

                $filters = compact('startDate', 'endDate');

                $histories = LoginHistory::filter($filters)->where('user_id', $user->id)->paginate(config('frontend.page'));

                return view('frontend.v1.user.login.history', compact('user', 'name', 'histories', 'users', 'startDate', 'endDate'));
            }
        }

        $filters = compact('startDate', 'endDate');

        $histories = LoginHistory::filter($filters)->where('user_id', $user->id)->paginate(config('frontend.page'));

        return view('frontend.v1.user.login.history', compact('user', 'histories', 'startDate', 'endDate'));
    }
}
