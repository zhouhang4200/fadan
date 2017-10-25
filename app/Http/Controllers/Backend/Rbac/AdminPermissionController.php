<?php

namespace App\Http\Controllers\Backend\Rbac;

use Pinyin;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::where('guard_name', 'admin')->paginate(config('backend.page'));

        return view('backend.rbac.permission.admin.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::where('guard_name', 'admin')->get();

        return view('backend.rbac.permission.admin.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Permission::rules(), Permission::messages());

        $data['guard_name'] = 'admin';

        $data['name'] = 'admin.' . Pinyin::permalink($request->alias, '');

        $data['alias'] = $request->alias;

        $data['module_id'] = $request->module_id;

        $res = Permission::create($data);
        
        if (! $res) {

            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('admin-permissions.index'))->with('succ', '添加成功!');
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

        $modules = Module::where('guard_name', 'admin')->get();

        return view('backend.rbac.permission.admin.edit', compact('permission', 'modules'));
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

        $data['name'] = 'admin.' . Pinyin::permalink($request->alias, '');

        $data['alias'] = $request->alias;

        $data['module_id'] = $request->module_id;

        $permission = Permission::find($id);

        $int = $permission->update($data);

        if ($int > 0) {
            
            return redirect(route('admin-permissions.index'))->with('succ', '更新成功!');
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
        $bool = Permission::find($id)->delete();

        if ($bool) {

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }
}
