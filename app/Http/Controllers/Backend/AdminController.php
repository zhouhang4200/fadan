<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\AdminLoginHistory;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
	// public function __construct()
	// {
	// 	$this->middleware('auth:admin');
	// }

    public function index(Request $request)
    {
    	// 验证通过写入登录数据
        AdminLoginHistory::writeLoginHistory($request->ip());

    	return view('Backend.index');
    }
}
