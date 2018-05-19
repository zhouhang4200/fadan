<?php

namespace App\Http\Controllers\Frontend\Account;

use DB;
use Auth;
use Cache;
use Exception;
use App\Models\User;
use App\Models\NewRole;
use App\Models\NewModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StationController extends Controller
{
	/**
	 * 岗位列表
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function index(Request $request)
    {
    	$userRoles = NewRole::where('user_id', Auth::user()->getPrimaryUserId())
            ->paginate(config('frontend.page'));

        if ($request->ajax()) {
    		return response()->json(view()->make('frontend.v1.user.station.list', [
                'userRoles' => $userRoles,
            ])->render());
    	}

        return view('frontend.v1.user.station.index', compact('userRoles'));
    }

    /**
     * 岗位添加
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    	// 主账号
        $user = User::find(Auth::user()->getPrimaryUserId());
        // 清除缓存
        Cache::forget('newPermissions:user:'.$user->id);
        // 获取此账号下的所有的权限IDS
        $userPermissionIds = $user->getUserPermissions()->pluck('id');
        // 获取此账号的权限以及权限模块
        $modulePermissions = NewModule::whereHas('newPermissions', function ($query) use ($userPermissionIds) {
                $query->whereIn('new_permissions.id', $userPermissionIds);
            })
            ->with(['newPermissions' => function ($query) use ($userPermissionIds) {
                $query->whereIn('new_permissions.id', $userPermissionIds);
            }])            
            ->get();      

        return view('frontend.v1.user.station.create', compact('modulePermissions'));
    }

    /**
     * 岗位保存
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	// 传送勾选的权限
        if (count($request->ids) > 0) {
            $data['name'] = $request->data['name'];
            $data['alias'] = $request->data['name'];
            $data['user_id'] = Auth::user()->getPrimaryUserId();
        
            $newRole = NewRole::create($data);
            // 角色-权限关联
            $newRole->newPermissions()->sync($request->ids); 
            // 清除缓存
            if ($newRole->newUsers) {
                foreach ($newRole->newUsers as $childUser) {
                    Cache::forget('newPermissions:user:'.$childUser->id);
                }
            }    

            return response()->ajax(1, '添加成功!');
        } else {
        	return response()->ajax(1, '请先勾选权限！');
        }    
    }

    /**
     * 岗位编辑
     *
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	// 获取指定的岗位
        $userRole = NewRole::find($id);
        // 获取主账号
        $user = User::find(Auth::user()->getPrimaryUserId());
        // 清除缓存
        Cache::forget('newPermissions:user:'.$user->id);
        // 获取此账号下面所有的权限
        $userPermissionIds = $user->getUserPermissions()->pluck('id');
        // 获取此账号的权限以及权限模块
        $modulePermissions = NewModule::whereHas('newPermissions', function ($query) use ($userPermissionIds) {
                $query->whereIn('new_permissions.id', $userPermissionIds);
            })
            ->with(['newPermissions' => function ($query) use ($userPermissionIds) {
                $query->whereIn('new_permissions.id', $userPermissionIds);
            }])            
            ->get();
        // 清除缓存
        if ($userRole->newUsers) {
            foreach ($userRole->newUsers as $childUser) {
                Cache::forget('newPermissions:user:'.$childUser->id);
            }
        }      
                       
        return view('frontend.v1.user.station.edit', compact('userRole', 'modulePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if (count($request->permissionIds) > 0) {
        	// 主账号
        	$user = User::find(Auth::user()->getPrimaryUserId());
            // 清除缓存
            Cache::forget('newPermissions:user:'.$user->id);
        	// 获取当前角色
            $userRole = NewRole::find($request->data['id']);
            // 数据
            $userRole->user_id = $user->id;
        	$userRole->alias = $request->data['name'];
        	$userRole->name = $request->data['name'];
        	// 修改岗位
            $userRole->save();
            // 关联岗位-权限
            $userRole->newPermissions()->sync($request->permissionIds);  
            // 清除缓存
            if ($userRole->newUsers) {
                foreach ($userRole->newUsers as $user) {
                    Cache::forget('newPermissions:user:'.$user->id);
                }
            }
            // 清除缓存
            if ($userRole->newUsers) {
                foreach ($userRole->newUsers as $childUser) {
                    Cache::forget('newPermissions:user:'.$childUser->id);
                }
            }   
            return response()->ajax(1, '修改成功!');
        } else {
        	return response()->ajax(1, '请勾选权限！');	
        }
    }

    /**
     * 删除岗位
     *
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
    	// 获取当前岗位
        $userRole = NewRole::find($request->roleId);
        // 岗位删除成功之后，再删除子账号下面的权限
        $userRole->delete();
        // 删除此岗位下面所有的权限值
        $userRole->newPermissions()->detach();
        // 删除此角色下的用户
        $userRole->newUsers()->detach();
        // 清除缓存
        if ($userRole->newUsers) {
            foreach ($userRole->newUsers as $childUser) {
                Cache::forget('newPermissions:user:'.$childUser->id);
            }
        }
        return response()->ajax(1, '删除成功!');
    }
}
