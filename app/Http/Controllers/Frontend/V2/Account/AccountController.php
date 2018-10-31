<?php

namespace App\Http\Controllers\Frontend\V2\Account;

use App\Models\NewRole;
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

    /**
     * 员工管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employee()
    {
        return view('frontend.v2.account.employee');
    }

    /**
     * 子员工
     * @return mixed
     */
    public function employeeUser()
    {
        return User::where('parent_id', Auth::user()->getPrimaryUserId())->pluck('username', 'id');
    }

    /**
     * 岗位
     * @return mixed
     */
    public function employeeStation()
    {
        return NewRole::where('user_id', Auth::user()->getPrimaryUserId())->pluck('name', 'id');
    }

    /**
     * 员工管理接口
     * @return mixed
     */
    public function employeeDataList()
    {
        $userName = request('username');
        $name = request('name');
        $station = request('station');

        $filter = compact( 'userName', 'name', 'station');

        return User::staffManagementFilter($filter)
            ->with('newRoles')
            ->paginate(15);
    }
}
