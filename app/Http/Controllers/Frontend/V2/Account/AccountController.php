<?php

namespace App\Http\Controllers\Frontend\V2\Account;

use App\Models\NewRole;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LoginHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    /**
     * 员工管理开关
     * @return mixed
     */
    public function employeeSwitch()
    {
        try {
            $user = User::find(request('user_id'));
            $user->status = request('status');
            $user->save();
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '修改成功!');
    }

    /**
     * 员工岗位删除
     * @return mixed
     */
    public function employeeDelete()
    {
        try {
            $user = User::find(request('user_id'));
            // 删除该员工下面的角色和权限
            $user->newRoles()->detach();
            $user->newPermissions()->detach();
            // 删除该角色并清空缓存
            $user->delete();
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
        } catch (Exception $e) {
            return response()->ajax(0, '服务器异常!');
        }
        return response()->ajax(1, '删除成功');
    }

    /**
     * 新增岗位
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employeeCreate()
    {
        return view('frontend.v2.account.employee-create');
    }

    /**
     * 编辑岗位
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employeeEdit()
    {
        return view('frontend.v2.account.employee-edit');
    }

    /**
     * 岗位新增接口
     * @return mixed
     */
    public function employeeAdd()
    {
        try{
            // 判断账号是否唯一
            $isSingle = User::where('name', request('name'))->withTrashed()->first();

            if ($isSingle) {
                return response()->ajax(0, '账号名已存在!');
            }
            // 数据
            $data['api_token'] = Str::random(25);
            $data['username'] = request('username');
            $data['name'] = request('name');
            $data['phone'] = request('phone');
            $data['qq'] = request('qq');
            $data['wechat'] = request('wechat');
            $data['remark'] = request('remark');
            $data['leveling_type'] = request('leveling_type');
            $data['password'] = bcrypt(request('password'));
            $data['parent_id'] = Auth::user()->getPrimaryUserId();
            $data['email'] = mt_rand()."@qq.com";
            $data['app_id'] = str_random(60);
            $data['app_secret'] = str_random(60);
            $data['voucher'] = "/frontend/v1/images/default-avatar.png";
            $roleIds = request('station', []);
            // 添加子账号同时添加角色
            $user = User::create($data);
            $user->newRoles()->sync($roleIds);
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
        } catch (Exception $e) {
            myLog('test', [$e->getMessage()]);
            return response()->ajax(0, '请重新提交数据!');
        }
        return response()->ajax(1, '添加成功');
    }

    public function employeeUpdate()
    {

    }
}
