<?php

namespace App\Http\Controllers\Backend\Home;

use Cache;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\NewRole;
use App\Models\NewPermission;
use App\Models\NewModule;
use App\Models\NewRolePermission;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
	/**
	 * 角色列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$roles = NewRole::where('user_id', 0)->get();

    	if ($request->ajax()) {
    		return response()->json(view()->make('backend.home.role.list', [
                'roles' => $roles,
            ])->render());
    	}
    	return view('backend.home.role.index', compact('roles'));
    }

    /**
     * 添加角色
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function create(Request $request)
    {
    	$modules = NewModule::with('newPermissions')->get();

    	return view('backend.home.role.create', compact('modules'));
    }

    /**
     * 保存角色
     * @param Request $request [description]
     */
    public function add(Request $request)
    {
    	// 后台管理员配的所有商户通用角色
    	$data['user_id'] = 0; 
    	$data['name'] = $request->data['name'];
    	$data['alias'] = $request->data['alias'];
    	// 先保存到角色表
    	$role = NewRole::create($data);
        // 保存角色权限
        $permissionIds = isset($request->ids) ? $request->ids : [];
    	// 关联角色-权限数据
    	$role->newPermissions()->sync($permissionIds);
        // 清空缓存
        if ($role->newUsers) {
            foreach ($role->newUsers as $user) {
                Cache::forget('newPermissions:user:'.$user->id);
            }
        }
    	
    	return response()->ajax(1, '添加成功!');
    }

    /**
     * 编辑
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function edit(Request $request, $id)
    {
    	//角色
    	$role = NewRole::find($id);
    	// 模块
    	$modules = NewModule::with('newPermissions')->get();

    	return view('backend.home.role.edit', compact('role', 'modules'));
    }

    /**
     * 修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
        // 保存角色修改
        $role = NewRole::find($request->data['id']);
        $role->name = $request->data['name'];
        $role->alias = $request->data['alias'];
        $role->save();
        // 保存角色权限
        $permissionIds = isset($request->ids) ? $request->ids : [];
        // 同步更新角色-权限
        $role->newPermissions()->sync($permissionIds);
        // 清空缓存
        if ($role->newUsers->count() > 0) {
            foreach ($role->newUsers as $user) {
                // 清空主账号下的缓存
                Cache::forget('newPermissions:user:'.$user->id);
                // 当前除了本角色的所有权限
                $exceptCurrentRolePermissionIds = $user->newPermissions
                    ->merge($user->load('newRoles', 'newRoles.newPermissions')
                    ->newRoles->where('id', '!=', $role->id)->flatMap(function ($role) {
                        return $role->newPermissions;
                    })->sort()->values())
                    ->sort()
                    ->values()
                    ->pluck('id')
                    ->toArray();
                // 当前角色所有的权限 + 除了当前的权限 = 最新所有权限
                $permissionIds = array_map(function ($v) {
                    return (int) $v;
                }, $permissionIds);

                $allUserPermissionIds = array_unique(array_merge($exceptCurrentRolePermissionIds, array_values($permissionIds)));
                // 从小到达排序,z主账号所有的权限
                sort($allUserPermissionIds);
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
            }
        }
    	return response()->ajax(1, '修改成功!');
    }

    /**
     * 删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function destroy(Request $request)
    {
    	$role = NewRole::find($request->id);
    	
        // 清空缓存
        if ($role->newUsers->count() > 0) {
            foreach ($role->newUsers as $user) {
                // 清空主账号下的缓存
                Cache::forget('newPermissions:user:'.$user->id);
                // 当前除了本角色的所有权限
                $exceptCurrentRolePermissionIds = $user->newPermissions
                    ->merge($user->load('newRoles', 'newRoles.newPermissions')
                    ->newRoles->where('id', '!=', $role->id)->flatMap(function ($role) {
                        return $role->newPermissions;
                    })->sort()->values())
                    ->sort()
                    ->values()
                    ->pluck('id')
                    ->toArray();

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
                        if (in_array($stationPermission, $exceptCurrentRolePermissionIds)) {
                            $existPermissionIds[] = $stationPermission;
                        }
                    }
                    // 更新 岗位-权限
                    $primaryUserRole->newPermissions()->sync($existPermissionIds);
                    // 清空数组
                    unset($existPermissionIds);
                }
            }
        }
        // 删除角色-权限
        $role->newPermissions()->detach();
        // 删除 角色-用户
        $role->newUsers()->detach();
    	// 删除角色
    	$role->delete();

    	return response()->ajax(1, '删除成功!');
    }
}
