<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\Role;
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

        return view('backend.role.admin.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.role.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Role::rules(), Role::messages());

        $data['guard_name'] = 'admin';

        $data['name'] = $request->name;

        $data['alias'] = $request->alias;

        $res = Role::create($data);
        
        if (! $res) {
            return back()->withInput()->with('createFail', '添加失败！');
        }
        return redirect(route('roles.index'))->with('succ', '添加成功!');
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

        return view('backend.role.admin.edit', compact('role'));
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
        $this->validate($request, Role::updateRules($id), Role::messages());

        $data['name'] = $request->name;

        $data['alias'] = $request->alias;

        $role = Role::find($id);

        $int = $role->update($data);

        if ($int > 0) {
            return redirect(route('roles.index'))->with('succ', '更新成功!');
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
        $role = Role::find($id)->delete();

        if ($bool) {

            return jsonMessages('1', '删除成功!');
        }
        return jsonMessages('2', '删除失败！');
    }
}
