<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::where('guard_name', 'web')->paginate(config('frontend.page'));

        return view('backend.permission.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::where('guard_name', 'web')->get();

        return view('backend.permission.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $request->module_id) {

            return back()->withInput()->with('missModule', '请选择模块!');
        }

        $this->validate($request, Permission::rules(), Permission::messages());

        $data['guard_name'] = 'web';

        $data['name'] = $request->name;

        $data['alias'] = $request->alias;

        $data['module_id'] = $request->module_id;

        $res = Permission::create($data);
        
        if (! $res) {

            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('permissions.index'))->with('succ', '添加成功!');
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
        $permission = Permission::find($id);

        $modules = Module::where('guard_name', 'web')->get();

        return view('backend.permission.edit', compact('permission', 'modules'));
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
        $this->validate($request, Permission::updateRules($id), Permission::messages());

        $data['name'] = $request->name;

        $data['alias'] = $request->alias;

        $data['module_id'] = $request->module_id;

        $permission = Permission::find($id);

        $int = $permission->update($data);

        if ($int > 0) {
            return redirect(route('permissions.index'))->with('succ', '更新成功!');
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
        $permission = Permission::find($id);

        $bool = $permission->delete();

        if ($bool) {

            $permission->rbacGroups()->detach($permission->id);

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }
}
