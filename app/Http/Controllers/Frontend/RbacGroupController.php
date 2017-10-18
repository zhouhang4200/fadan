<?php

namespace App\Http\Controllers\Frontend;

use Auth;
use App\Models\Module;
use App\Models\RbacGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RbacGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rbacGroups = RbacGroup::where('user_id', Auth::id())->whereHas('permissions')->paginate(config('frontend.page'));

        return view('frontend.rbacgroup.index', compact('rbacGroups'));
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

        return view('frontend.rbacgroup.create', compact('modulePermissions'));
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

            $data['alias'] = $request->alias;

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
                       
        return view('frontend.rbacgroup.edit', compact('rbacGroup', 'modulePermissions'));
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

            $data['alias'] = $request->alias;

            $data['user_id'] = Auth::id();
            
            $rbacGroup = RbacGroup::find($id);

            $int = $rbacGroup->update($data);

            if ($int > 0) {

                $rbacGroup->permissions()->sync($permissionIds);  
            }

            return redirect(route('rbacgroups.index'))->with('succ', '修改成功!');
        }

        return back()->withInput()->with('missError', '请勾选权限名!');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RbacGroup  $rbacGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rbacGroup = RbacGroup::find($id);

        $bool = $rbacGroup->delete();

        if ($bool) {

            $rbacGroup->permissions()->detach();

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败！']);
    }
}
