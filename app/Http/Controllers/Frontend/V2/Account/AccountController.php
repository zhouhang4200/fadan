<?php

namespace App\Http\Controllers\Frontend\V2\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * 我的账号
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mine()
    {
        return view('frontend.v2.account.mine');
    }

    /**
     * 我的账号接口
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function mineDataList()
    {
        return response()->json(['1', 2]);
    }

    /**
     * 我的账号修改
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mineEdit()
    {
        return view('frontend.v2.account.mine-edit');
    }
}
