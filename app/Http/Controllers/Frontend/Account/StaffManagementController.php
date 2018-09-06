<?php

namespace App\Http\Controllers\Frontend\Account;

use Cache;
use Auth, DB;
use Exception;
use App\Models\User;
use App\Models\RbacGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewRole;

class StaffManagementController extends Controller
{
    /**
     * 员工管理列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
    	$name = $request->name;
    	$station = $request->station; // 岗位
    	$userName = $request->username;
        // 获取主账号分配的角色
        $userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get();
        // 获取所有的子账号
    	$children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
        // 筛选
    	$filters = compact('name', 'userName', 'station');

        //状态1是封号，0是正常
    	$users = User::staffManagementFilter($filters)
            ->paginate(config('frontend.page'));

        // 删除的时候页面不刷新
        if ($request->ajax()) {
            return response()->json(view()->make('frontend.v1.user.staff-management.list', [
                'users' => $users,
            ])->render());
        }

    	return view('frontend.v1.user.staff-management.index', compact('name', 'station', 'userName', 'users', 'userRoles', 'children'));
    }

    /**
     * 员工编辑
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id)
    {
    	$user = User::find($id);
        // 主账号编辑的岗位
        $userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get();

    	return view('frontend.v1.user.staff-management.edit', compact('userRoles', 'user'));
    }

    /**
     * 员工岗位修改
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
    	DB::beginTransaction();
        // 子账号
    	$user = User::find($request->id);
        $data = $request->data;
        // 如果存在密码则修改密码
        if (clientRSADecrypt($request->password)) {
            $data['password'] = bcrypt(clientRSADecrypt($request->password));
        } else {
            unset($data['password']);
        }
        
    	try {
            // 关联到管理员-角色表
            $roleIds = $request->roles ?? [];
            $user->newRoles()->sync($roleIds);
            // 更新账号
            $user->update($data);
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
    	} catch (Exception $e) {
    		DB::rollBack();
    		return response()->ajax(0, '修改失败！');
    	}
    	DB::commit();
    	return response()->ajax(1, '修改成功!');
    }

    /**
     * 禁止员工登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function forbidden(Request $request)
    {
        $user = User::find($request->id);
        // status=1是禁用
        if ($user->status == 1) {
            $user->status = 0;
            $user->save();
            return response()->ajax(1, '已启用');
        } else {
            $user->status = 1;
            $user->save();
            return response()->ajax(1, '已禁用');
        }
    }

    /**
     * 员工删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
        $user = User::find($request->id);
        // 删除该员工下面的角色和权限
        $user->newRoles()->detach();
        $user->newPermissions()->detach();
        // 删除该角色并清空缓存
        $user->delete();
        // 清除缓存
        Cache::forget('newPermissions:user:'.$user->id);

    	return response()->ajax(1, '删除成功');
    }

    /**
     * 员工添加
     * @return [type] [description]
     */
    public function create()
    {
        //获取主账号设置的所有角色
        $userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get(); 

    	return view('frontend.v1.user.staff-management.create', compact('userRoles'));
    }

    /**
     * 员工保存
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
        // 判断账号是否唯一
        $isSingle = User::where('name', $request->data['name'])->withTrashed()->first();

        if ($isSingle) {
            return response()->ajax(0, '账号名已存在!');
        }
        // 数据
        $data = $request->data;
        $data['api_token'] = Str::random(25);
        $data['password'] = bcrypt(clientRSADecrypt($request->password));
        $data['parent_id'] = Auth::user()->getPrimaryUserId();
        $data['email'] = mt_rand(5, 15)."@qq.com";
        $data['app_id'] = str_random(60);
        $data['app_secret'] = str_random(60);
        $data['voucher'] = "/frontend/v1/images/default-avatar.png";
        $roleIds = $request->roles ?: [];
        // 添加子账号同时添加角色
        $user = User::create($data);
        $user->newRoles()->sync($roleIds);
        // 清除缓存
        Cache::forget('newPermissions:user:'.$user->id);

    	return response()->ajax(1, '添加成功!');
    }
}
