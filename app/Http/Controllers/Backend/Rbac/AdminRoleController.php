<?php

namespace App\Http\Controllers\Backend\Rbac;

use Pinyin;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminRoleController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->paginate(config('backend.page'));

        return view('backend.rbac.role.admin.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modulePermissions = Module::where('guard_name', 'admin')->with('permissions')->get();

        $permissions = Permission::where('guard_name', 'admin')->get()->toArray();

        return view('backend.rbac.role.admin.create', compact('modulePermissions', 'permissions'));
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

            return back()->withInput()->with('missError', '请选择权限!');
        }

        $this->validate($request, Role::rules(), Role::messages());

        $data['guard_name'] = 'admin';

        $data['name'] = 'admin' . Pinyin::permalink($request->alias, '');

        $data['alias'] = $request->alias;

        $res = Role::create($data)->permissions()->sync($request->permissions);
        
        if (! $res) {

            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('admin-roles.index'))->with('succ', '添加成功!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);

        $modulePermissions = Module::where('guard_name', 'admin')->with('permissions')->get();

        return view('backend.rbac.role.admin.edit', compact('role', 'modulePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! $request->permissions) {

            return back()->withInput()->with('missError', '请选择权限!');
        }

        $this->validate($request, Role::updateRules($id), Role::messages());

        $data['name'] = 'admin' . Pinyin::permalink($request->alias, '');

        $data['alias'] = $request->alias;

        $role = Role::find($id);

        $int = $role->update($data);

        if ($int > 0) {

            $role->permissions()->sync($request->permissions);

            return redirect(route('admin-roles.index'))->with('succ', '更新成功!');
        }

        return back()->withInput()->with('updateFail', '更新失败!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bool = Role::find($id)->delete();

        if ($bool) {

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }
}
