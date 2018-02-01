<?php

namespace App\Http\Controllers\Frontend\Account;

use Auth, DB;
use Exception;
use App\Models\User;
use App\Models\RbacGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffManagementController extends Controller
{
    /**
     * 岗位管理列表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
    	$name = $request->name;
    	$station = $request->station;
    	$userName = $request->username;

    	$groups = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
    	$children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
    	$filters = compact('name', 'userName', 'station');

        //状态是2时表示删除不显示
    	$users = User::staffManagementFilter($filters)
        ->withTrashed()
        ->where('status', '!=', 2)
        ->paginate(config('frontend.page'));

    	return view('frontend.user.staff-management.index', compact('name', 'station', 'userName', 'users', 'groups', 'children'));
    }

    /**
     * 岗位编辑
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id)
    {
    	$roles = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
    	$user = User::find($id);

    	return view('frontend.user.staff-management.edit', compact('roles', 'user'));
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
    		$data['role'] = $request->role;
    		$user->rbacgroups()->sync($request->role);
    		$rbacGroups = RbacGroup::whereIn('id', $request->role)->get();

            $permissions = [];
            foreach ($rbacGroups as $k => $rbacGroup) {
                $permissions[$k] = $rbacGroup->permissions()->pluck('id');
            } 
            $user->permissions()->sync(collect($permissions)->flatten()->unique()->toArray());
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
	    	$user = User::withTrashed()->find($request->id);

	    	if (! $user->deleted_at) {    		
		    	$user->delete();
	    	} else {
	    		$user->restore();
	    	}
    	} catch (Exception $e) {
    		DB::rollBack();
    		return response()->ajax(0, '启用失败');
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	if ($user->deleted_at) {
    		return response()->ajax(1, '已开启');
    	} else {
    		return response()->ajax(1, '已关闭');
    	}
    }

    /**
     * 岗位删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
    	DB::beginTransaction();
    	try {
            User::where('id', $request->id)->update(['status' => 2]);
    		User::destroy($request->id);
    	} catch (Exception $e) {
    		DB::rollBack();
    		return response()->ajax(0, '删除失败!');
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	return response()->ajax(1, '删除成功');
    }

    /**
     * 岗位添加
     * @return [type] [description]
     */
    public function create()
    {
    	$roles = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();

    	return view('frontend.user.staff-management.create', compact('roles'));
    }

    /**
     * 岗位保存
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function store(Request $request)
    {
    	DB::beginTransaction();
        try {
            $this->validate($request, User::staffManagementRules(), User::staffManagementMessages());
            $data = $request->except('role');
            $data['password'] = bcrypt($request->password);
            $data['api_token'] = Str::random(25);
            $data['parent_id'] = Auth::id();
            $user = User::create($data);

            if ($request->role) {
                $data['role'] = $request->role;
                $user->rbacgroups()->sync($request->role);
                $rbacGroups = RbacGroup::whereIn('id', $request->role)->get();

                $permissions = [];
                foreach ($rbacGroups as $k => $rbacGroup) {
                    $permissions[$k] = $rbacGroup->permissions()->pluck('id');
                } 
                $user->permissions()->sync(collect($permissions)->flatten()->unique()->toArray());
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
