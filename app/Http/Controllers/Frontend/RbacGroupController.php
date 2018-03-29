<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\User;
use App\Models\Module;
use App\Models\RbacGroup;
use Illuminate\Http\Request;
use App\Models\UserRbacGroup;
use App\Http\Controllers\Controller;
use Spatie\Permission\Traits\RefreshesPermissionCache;

class RbacGroupController extends Controller
{
    use RefreshesPermissionCache;

    public static function boot()
    {
        parent::boot();

        static::bootRefreshesPermissionCache();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rbacGroups = RbacGroup::where('user_id', Auth::user()->getPrimaryUserId())
            ->whereHas('permissions')
            ->paginate(config('frontend.page'));

        return view('frontend.user.rbacgroup.index', compact('rbacGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissionIds = Auth::user()->getAllPermissions()->pluck('id');

        $modulePermissions = Module::where('guard_name', 'web')
            ->with(['permissions' => function ($query) use ($permissionIds) {
                $query->whereIn('id', $permissionIds);
            }])            
            ->get();      

        return view('frontend.user.rbacgroup.create', compact('modulePermissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $request->permissions) {
            return back()->withInput()->with('missError', '请勾选权限！');
        }

        $this->validate($request, RbacGroup::rules(), RbacGroup::messages());

        $permissionIds = $request->permissions;

        if (count($permissionIds) > 0) {
            $data['name'] = $request->name;
            $data['user_id'] = Auth::id();
        
            RbacGroup::create($data)->permissions()->sync($permissionIds);   

            return redirect(route('rbacgroups.index'))->with('succ', '添加成功!');
        }     

        return back()->withInput()->with('missError', '请勾选权限名!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function show(RbacGroup $rbacGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rbacGroup = RbacGroup::find($id);

        $permissionIds = Auth::user()->getAllPermissions()->pluck('id');

        $modulePermissions = Module::where('guard_name', 'web')
                        ->with(['permissions' => function ($query) use ($permissionIds) {
                            $query->whereIn('id', $permissionIds);
                        }])            
                        ->get();       
                       
        return view('frontend.user.rbacgroup.edit', compact('rbacGroup', 'modulePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! $request->permissions) {
            return back()->withInput()->with('missError', '请勾选权限！');
        }

        $this->validate($request, RbacGroup::rules(), RbacGroup::messages());

        $permissionIds = $request->permissions;

        if (count($permissionIds) > 0) {
            $data['name'] = $request->name;
            $data['user_id'] = Auth::id();
            $rbacGroup = RbacGroup::find($id);
            $int = $rbacGroup->update($data);

            if ($int > 0) {
                $rbacGroup->permissions()->sync($permissionIds);  
            }   

            $children = User::where('parent_id', Auth::id())->get();

            foreach ($children as $child) {
                $rbacGroups = $child->rbacGroups;
                $permissions = $rbacGroups->map(function ($rbacGroup) {
                    return $rbacGroup->permissions;                  
                })->flatten();

                $child->permissions()->sync($permissions->pluck('id')->toArray());
            }
            return redirect(route('rbacgroups.index'))->with('succ', '修改成功!');
        }
        return back()->withInput()->with('missError', '请勾选权限名!');
    }

    /**
     * 删除岗位，同时删除子账号下面的权限
     *
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rbacGroup = RbacGroup::find($id);
        $bool = $rbacGroup->delete();
        // 岗位删除成功之后，再删除子账号下面的权限
        if ($bool) {
            // 获取此岗位下面所有的权限值
            $rbacGroup->permissions()->detach();
            // 主账号先删除此岗位
            UserRbacGroup::where('rbac_group_id', $id)->delete();
            // 获取该主账号下所有的子账号
            $children = User::where('parent_id', Auth::id())->get();
            // 遍历所有的子账号
            foreach ($children as $child) {
                // 获取子账号拥有的岗位
                $rbacGroups = $child->rbacGroups;
                // 遍历每个岗位，将岗位扁平化为一维数组
                $permissions = $rbacGroups->map(function ($rbacGroup) {
                    return $rbacGroup->permissions;                   
                })->flatten();
                // 删除此子账号下拥有的权限
                $child->permissions()->detach();
            }          
            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败！']);
    }
}
