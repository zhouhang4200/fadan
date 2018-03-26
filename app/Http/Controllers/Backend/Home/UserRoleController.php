<?php

namespace App\Http\Controllers\Backend\Home;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NewRole;
use App\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
    	// 获取所有的用户
    	$users = User::where('parent_id', 0)->paginate(10);
    	// 获取所有的角色
    	$roles = NewRole::where('user_id', 0)->get();

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.home.user.list', [
                'users' => $users,
                'roles' => $roles,
            ])->render());
    	}

    	return view('backend.home.user.index', compact('users', 'roles'));
    }

    public function match(Request $request)
    {
    	// 获取用户
    	$user = User::find($request->id);
    	// 获取 角色
    	$roleIds = $request->ids ?? [];
    	// 同步角色
    	$user->newRoles()->sync($roleIds);

    	return response()->ajax(1, '设置成功!');
    }
}
