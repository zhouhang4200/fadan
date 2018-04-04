<?php

namespace App\Http\Controllers\Backend\Home;

use Cache;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NewRole;
use App\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->id;
    	// 获取所有的用户
    	$users = User::where('parent_id', 0)->filter(compact('id'))->paginate(10);
    	// 获取所有的角色
    	$roles = NewRole::where('user_id', 0)->get();

    	return view('backend.home.user.index', compact('users', 'roles', 'id'));
    }

    /**
     * 给用户赋角色
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function match(Request $request)
    {
    	// 获取用户
    	$user = User::find($request->id);
    	// 获取 角色
    	$roleIds = $request->ids ?? [];
    	// 同步角色
    	$user->newRoles()->sync($roleIds);

        // 清空主账号下的缓存
        Cache::forget('newPermissions:user:'.$user->id);
        // 此主账号下所有的权限
        $allUserPermissionIds = $user->getUserPermissions()->pluck('id')->toArray();
        // 清空子账号下面的缓存
        foreach ($user->children as $child) {
            // 清空子账号的缓存
            Cache::forget('newPermissions:user:'.$child->id);
        }
        // 遍历主账号创建的岗位
        $primaryUserRoles = NewRole::where('user_id', $user->id)->get();
        // 遍历岗位
        foreach ($primaryUserRoles as $k => $primaryUserRole) {
            // 获取当前岗位下面的权限
            $stationPermissions = $primaryUserRole->newPermissions->pluck('id')->toArray();
            // 过滤岗位下面的权限，记录在主账号角色中的权限
            $existPermissionIds = [];
            foreach ($stationPermissions as $stationPermission) {
                if (in_array($stationPermission, $allUserPermissionIds)) {
                    $existPermissionIds[] = $stationPermission;
                }
            }
            // 更新 岗位-权限
            $primaryUserRole->newPermissions()->sync($existPermissionIds);
            // 清空数组
            unset($existPermissionIds);
        }

    	return response()->ajax(1, '设置成功!');
    }
}
