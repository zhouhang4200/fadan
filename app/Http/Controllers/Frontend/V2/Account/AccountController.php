<?php

namespace App\Http\Controllers\Frontend\V2\Account;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
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

    /**
     * 登录记录
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loginHistory()
    {
        return view('frontend.v2.account.login-history');
    }

    /**
     * 登录记录子账号
     * @return mixed
     */
    public function LoginHistoryUser()
    {
        return User::where('parent_id', Auth::user()->getPrimaryUserId())->pluck('username', 'id');
    }

    /**
     * 登录记录接口
     * @return mixed
     */
    public function loginHistoryDataList()
    {
        $startDate = request('date')[0];
        $endDate = request('date')[1];

        $filter = compact( 'startDate', 'endDate');

        return LoginHistory::where('user_id', Auth::user()->id)
            ->newfilter($filter)
            ->with(['user', 'city'])
            ->latest('id')
            ->paginate(15);
    }
}
