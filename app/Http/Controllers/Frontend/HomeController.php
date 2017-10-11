<?php

namespace App\Http\Controllers\Frontend;

use App\Models\LoginHistory;
use Illuminate\Http\Request;
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

        return view('frontend.index');
    }
}
