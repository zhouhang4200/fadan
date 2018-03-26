<?php

namespace App\Http\Controllers\Frontend\Account;

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
    	$station = $request->station;
    	$userName = $request->username;
        // 获取主账号分配的角色
    	// $groups = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
        $userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get();
    	$children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
    	$filters = compact('name', 'userName', 'station');

        //状态是2时表示删除不显示
    	$users = User::staffManagementFilter($filters)
            ->paginate(config('frontend.page'));

    	return view('frontend.user.staff-management.index', compact('name', 'station', 'userName', 'users', 'userRoles', 'children'));
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
        // $roles = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
        $userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get();

    	return view('frontend.user.staff-management.edit', compact('userRoles', 'user'));
    }

    /**
     * 员工岗位修改
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function update(Request $request, $id)
    {
    	DB::beginTransaction();

    	$user = User::find($id);
    	$data = $request->except(['password', 'role']);

    	if ($request->password) {
    		$data['password'] = bcrypt($request->password);
    	}
        // $request->role 是一个数组
    	if ($request->role) {
            // 主账号设置的岗位数组
    		$data['role'] = $request->role;
            // 关联到管理员-角色表
    		$user->newRoles()->sync($request->role);
            // 获取角色下面的所有权限，将权限关联子账号
    		$userRoles = NewRole::whereIn('id', $request->role)->get();
            // 写入管理员-权限表
            $permissions = [];

            foreach ($userRoles as $k => $userRole) {
                $permissions[$k] = $userRole->newPermissions()->pluck('id');
            } 
            // 写入 管理员-权限 表
            $user->newPermissions()->sync(collect($permissions)->flatten()->unique()->toArray());
    	}
    	
    	try {
    		$user->update($data);
    	} catch (Exception $e) {
    		DB::rollBack();
    		return back()->with('fail', '修改失败！');
    	}
    	DB::commit();
    	return redirect(route('staff-management.index'))->with('succ', '修改成功!');
    }

    /**
     * 禁止员工登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function forbidden(Request $request)
    {
    	DB::beginTransaction();
        
        try {
            $user = User::find($request->id);
            // status=1是禁用
            if ($user->status == 1) {
                $user->status = 0;
                $user->save();
            } else {
                $user->status = 1;
                $user->save();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->ajax(0, '启用失败');
            throw new Exception($e->getMessage());
        } 
    	DB::commit();
    	if ($user->status == 1) {
    		return response()->ajax(1, '已禁用');
    	} else {
    		return response()->ajax(1, '已启用');
    	}
    }

    /**
     * 员工删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
        User::destroy($request->id);

    	return response()->ajax(1, '删除成功');
    }

    /**
     * 员工添加
     * @return [type] [description]
     */
    public function create()
    {
    	// $roles = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
        //获取主账号设置的所有角色
        $userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())->get(); 

    	return view('frontend.user.staff-management.create', compact('userRoles'));
    }

    /**
     * 员工保存
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
        $this->validate($request, User::staffManagementRules(), User::staffManagementMessages());
        DB::beginTransaction();
        try {
            $data = $request->except('role');
            $data['password'] = bcrypt($request->password);
            $data['api_token'] = Str::random(25);
            $data['parent_id'] = Auth::id();
            $user = User::create($data);
            // 为员工添加角色
            if ($request->role) {
                $data['role'] = $request->role;
                // 更新 管理员-角色
                $user->newRoles()->sync($request->role);
                // 更新管理员-权限
                // $rbacGroups = RbacGroup::whereIn('id', $request->role)->get();
                $userRoles = NewRole::where('id', $request->role)->get();

                $permissions = [];
                foreach ($userRoles as $k => $userRole) {
                    $permissions[$k] = $userRole->newPermissions()->pluck('id');
                } 
                $user->newPermissions()->sync(collect($permissions)->flatten()->unique()->toArray());
            }
    	} catch (Exception $e) {
    		DB::rollBack();
    		return back()->withInput()->with('fail', '添加失败');
            throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	return redirect(route('staff-management.index'))->with('succ', '添加成功');
    }
}
