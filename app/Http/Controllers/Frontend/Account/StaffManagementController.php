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
    public function index(Request $request)
    {
    	$name = $request->name;
    	$station = $request->station;
    	$userName = $request->username;

    	$groups = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
    	$children = User::where('parent_id', Auth::user()->getPrimaryUserId())->get();
    	$filters = compact('name', 'userName', 'station');

    	$users = User::staffManagementFilter($filters)->withTrashed()->paginate(config('frontend.page'));

    	return view('frontend.user.staff-management.index', compact('name', 'station', 'userName', 'users', 'groups', 'children'));
    }

    public function edit($id)
    {
    	$roles = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();
    	$user = User::find($id);

    	return view('frontend.user.staff-management.edit', compact('roles', 'user'));
    }

    public function update(Request $request, $id)
    {
    	DB::beginTransaction();

    	$user = User::find($id);
    	$data = $request->except(['password', 'role']);

    	if ($request->password) {
    		$data['password'] = bcrypt($request->password);
    	}

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

    public function delete(Request $request)
    {
    	DB::beginTransaction();
    	try {
    		User::destroy($request->id);
    	} catch (Exception $e) {
    		DB::rollBack();
    		return response()->ajax(0, '删除失败!');
    		throw new Exception($e->getMessage());
    	}
    	DB::commit();
    	return response()->ajax(1, '删除成功');
    }

    public function create()
    {
    	$roles = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())->get();

    	return view('frontend.user.staff-management.create', compact('roles'));
    }

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
