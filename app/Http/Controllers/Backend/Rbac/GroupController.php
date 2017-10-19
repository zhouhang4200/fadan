<?php

namespace App\Http\Controllers\Backend\Rbac;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = User::where('parent_id', 0)->whereHas('roles')->latest('id')->paginate(config('backend.page'));

        return view('backend.group.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = User::find($request->id);

        $roles = Role::where('guard_name', 'web')->get();

        return view('backend.group.create', compact('roles', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $request->roles) {

            return back()->with('missRole', '请选择角色!');
        }

        $user = User::find($request->userId);

        $array = $user->roles()->sync($request->roles);

        if ($array['attached'] || $array['detached'] || $array['updated']) {

            return redirect(route('groups.index'))->with('succ', '赋予角色成功!');
        }

        return back()->with('storeError', '赋予角色失败!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return view('backend.group.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        $roles = Role::where('guard_name', 'web')->get();

        return view('backend.group.edit', compact('user', 'roles'));
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
        if (! $request->roles) {

            return back()->with('missRole', '请选择角色!');
        }


        $user = User::find($id);

        if ($request->roles == $user->roles->pluck('id')->toArray()) {

            return redirect(route('groups.index'))->with('succ', '修改账号角色成功!');
        }

        $array = $user->roles()->sync($request->roles);

        if ($array['attached'] || $array['detached'] || $array['updated']) {

            return redirect(route('groups.index'))->with('succ', '修改账号角色成功!');
        }
        return back()->with('updateError', '修改账号角色失败!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        $roleIds = $user->roles->pluck('id')->toArray(); 

        $int = $user->roles()->detach($roleIds);

        if ($int > 0) {

            return response()->json(['code' => '1', 'message' => '删除成功!']);
        }
        return response()->json(['code' => '2', 'message' => '删除失败!']);
    }
}
