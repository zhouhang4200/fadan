<?php

namespace App\Http\Controllers\Backend\Rbac;

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

        return view('backend.permission.admin.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.permission.admin.create');
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

        $data['name'] = $request->name;

        $data['alias'] = $request->alias;

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

        return view('backend.permission.admin.edit', compact('permission'));
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
        $permission = Permission::find($id)->delete();

        if ($bool) {

            return jsonMessages('1', '删除成功!');
        }
        return jsonMessages('2', '删除失败！');
    }
}
