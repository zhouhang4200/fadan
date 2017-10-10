<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 验证通过写入登录数据
        LoginHistory::writeLoginHistory($request->ip());

        $user1 = \App\Models\User::find(2);
        $user = LoginHistory::find(1);
        // dd($user1->loginHistories);
        // dd($user->user);

        return view('home');
    }
}
