<?php

namespace App\Http\Controllers\Frontend\V2\Account;

use Exception;
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
    public function mineForm()
    {
        return Auth::user();
    }

    /**
     * 我的账号修改
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mineUpdate()
    {
        try {
            $user = Auth::user();

            if (request('password')) {
                $user->password = bcrypt(request('password'));
            }
            $user->type = request('type');
            $user->leveling_type = request('type');
            $user->save();
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }
}
